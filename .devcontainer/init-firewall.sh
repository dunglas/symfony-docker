#!/bin/bash
# Adapted from https://github.com/anthropics/claude-code/blob/main/.devcontainer/init-firewall.sh
#
# Modifications:
#   - Added packagist.org and repo.packagist.org to the allowlist (Composer/PHP dependencies)
#   - Added -exist flag to ipset add calls to handle duplicate IPs across domains
#   - Added inbound rules for published service ports (HTTP/HTTPS/HTTP3)
#   - Replaced per-domain IP resolution with dnsmasq --ipset to handle CDN IP rotation:
#     dnsmasq intercepts all DNS queries and adds resolved IPs for allowed domains to the
#     ipset dynamically, so the ipset is always current regardless of CDN IP rotation.
set -euo pipefail
IFS=$'\n\t'

# 1. Extract Docker DNS info BEFORE any flushing
DOCKER_DNS_RULES=$(iptables-save -t nat | grep "127\.0\.0\.11" || true)

# Flush existing rules and delete existing ipsets
iptables -F
iptables -X
iptables -t nat -F
iptables -t nat -X
iptables -t mangle -F
iptables -t mangle -X
ipset destroy allowed-domains 2>/dev/null || true

# 2. Selectively restore ONLY internal Docker DNS resolution
#    These rules redirect queries to 127.0.0.11:53 to Docker's actual DNS port.
#    These are restored first; later, our DNS redirect rules are inserted with
#    `iptables -t nat -I OUTPUT ...`, so they precede these Docker rules. Only
#    dnsmasq's upstream queries (which are exempt from our redirect) pass through them.
if [ -n "$DOCKER_DNS_RULES" ]; then
	echo "Restoring Docker DNS rules..."
	iptables -t nat -N DOCKER_OUTPUT 2>/dev/null || true
	iptables -t nat -N DOCKER_POSTROUTING 2>/dev/null || true
	echo "$DOCKER_DNS_RULES" | xargs -L 1 iptables -t nat
else
	echo "No Docker DNS rules to restore"
fi

# Allow DNS (outbound only; return traffic is handled by ESTABLISHED,RELATED)
# and localhost (covers dnsmasq ↔ Docker DNS on 127.0.0.11).
iptables -A OUTPUT -p udp --dport 53 -j ACCEPT
iptables -A OUTPUT -p tcp --dport 53 -j ACCEPT
iptables -A INPUT -i lo -j ACCEPT
iptables -A OUTPUT -o lo -j ACCEPT

# Create ipset with CIDR support
ipset create allowed-domains hash:net

# GitHub IP ranges
echo "Fetching GitHub IP ranges..."
gh_ranges=$(curl -s --connect-timeout 10 --max-time 30 --fail https://api.github.com/meta)
[ -z "$gh_ranges" ] && {
	echo "ERROR: Failed to fetch GitHub IP ranges"
	exit 1
}
echo "$gh_ranges" | jq -e '.web and .api and .git' >/dev/null ||
	{
		echo "ERROR: GitHub API response missing required fields"
		exit 1
	}

while read -r cidr; do
	[[ "$cidr" =~ ^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/[0-9]{1,2}$ ]] ||
		{
			echo "ERROR: Invalid CIDR: $cidr"
			exit 1
		}
	echo "Adding GitHub range $cidr"
	ipset add allowed-domains "$cidr" -exist
done < <(echo "$gh_ranges" | jq -r '(.web + .api + .git)[]' | grep -v ':' | aggregate -q)

# Extract Docker's actual DNS port so dnsmasq can connect to it directly.
# Docker's embedded DNS listens on a random high port and uses iptables NAT to
# redirect 127.0.0.11:53 → 127.0.0.11:<port>. By pointing dnsmasq at the real
# port, its upstream queries naturally bypass our port-53 DNAT rules (no
# uid-owner tricks needed) and don't require the DOCKER_OUTPUT NAT chain.
DOCKER_DNS_PORT=$(echo "$DOCKER_DNS_RULES" | sed -n 's/.*udp.*--to-destination 127\.0\.0\.11:\([0-9]*\).*/\1/p' | head -1)
[ -z "$DOCKER_DNS_PORT" ] && {
	echo "ERROR: Failed to extract Docker DNS port"
	exit 1
}
echo "Docker DNS port: $DOCKER_DNS_PORT"

# Configure dnsmasq to dynamically populate the ipset as domains are resolved.
# This means the ipset always contains current IPs regardless of CDN rotation —
# the IP is added to the ipset the moment it is resolved, before the connection
# is made, so no connection is ever blocked due to stale IPs.
cat >/etc/dnsmasq.d/firewall-ipset.conf <<EOF
# Forward all queries to Docker's embedded DNS (actual port, not 53)
server=127.0.0.11#${DOCKER_DNS_PORT}
# Listen only on an alternate loopback address to avoid conflicting with
# any existing resolver on 127.0.0.1
listen-address=127.0.0.2
bind-interfaces
# Populate the allowed-domains ipset with every IP returned when resolving
# any of these domains (or their subdomains)
ipset=/github.com/anthropic.com/sentry.io/statsig.com/registry.npmjs.org/packagist.org/repo.packagist.org/marketplace.visualstudio.com/vscode.blob.core.windows.net/update.code.visualstudio.com/allowed-domains
EOF

# Ensure idempotency when run as a postStartCommand: stop any existing dnsmasq.
pkill dnsmasq 2>/dev/null || true

dnsmasq --conf-dir=/etc/dnsmasq.d
echo "dnsmasq started"

# Redirect all outbound DNS queries (port 53) through dnsmasq so it can
# populate the ipset before each connection. dnsmasq's own upstream queries
# go to Docker's actual DNS port (not 53), so they are unaffected by these rules.
iptables -t nat -I OUTPUT -p tcp --dport 53 -j DNAT --to-destination 127.0.0.2:53
iptables -t nat -I OUTPUT -p udp --dport 53 -j DNAT --to-destination 127.0.0.2:53

# Allow traffic to/from the host gateway IP
HOST_IP=$(ip route | grep default | cut -d" " -f3)
[ -z "$HOST_IP" ] && {
	echo "ERROR: Failed to detect host IP"
	exit 1
}
echo "Host gateway IP: $HOST_IP"
iptables -A INPUT -s "$HOST_IP" -j ACCEPT
iptables -A OUTPUT -d "$HOST_IP" -j ACCEPT

# Allow inbound connections to published service ports (HTTP/HTTPS)
iptables -A INPUT -p tcp --dport 80 -j ACCEPT
iptables -A INPUT -p tcp --dport 443 -j ACCEPT
iptables -A INPUT -p udp --dport 443 -j ACCEPT

# Default DROP policies
iptables -P INPUT DROP
iptables -P FORWARD DROP
iptables -P OUTPUT DROP

# Allow established connections
iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
iptables -A OUTPUT -m state --state ESTABLISHED,RELATED -j ACCEPT

# Allow outbound traffic to local/private networks (e.g., Docker Compose services)
iptables -A OUTPUT -d 10.0.0.0/8 -j ACCEPT
iptables -A OUTPUT -d 172.16.0.0/12 -j ACCEPT
iptables -A OUTPUT -d 192.168.0.0/16 -j ACCEPT

# Allow outbound to whitelisted IPs only (GitHub CIDRs + IPs populated by dnsmasq)
iptables -A OUTPUT -m set --match-set allowed-domains dst -j ACCEPT

# Reject everything else with immediate feedback
iptables -A OUTPUT -j REJECT --reject-with icmp-admin-prohibited

echo "Firewall configuration complete"

# Verify
if curl --connect-timeout 5 https://example.com >/dev/null 2>&1; then
	echo "ERROR: Firewall check failed — able to reach example.com"
	exit 1
else
	echo "OK: example.com is blocked"
fi

if ! curl --connect-timeout 5 https://api.github.com/zen >/dev/null 2>&1; then
	echo "ERROR: Firewall check failed — unable to reach api.github.com"
	exit 1
else
	echo "OK: api.github.com is reachable"
fi

if ! curl --connect-timeout 5 https://repo.packagist.org/packages.json >/dev/null 2>&1; then
	echo "ERROR: Firewall check failed — unable to reach repo.packagist.org"
	exit 1
else
	echo "OK: repo.packagist.org is reachable"
fi
