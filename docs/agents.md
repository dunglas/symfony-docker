# Using AI coding agents

This project ships a [Dev Container](https://containers.dev/) ready for AI coding agents.
No agent is installed by default: you pick one, add it to `.devcontainer/devcontainer.json`,
and rebuild the container. The recommended agent is [OpenCode](https://opencode.ai), which is
open source and works with both local and remote models.

You can run an agent against a model on your own machine (no data leaves your computer) or
against a remote provider. An optional [network firewall](#optional-network-sandbox) restricts
the container's outbound traffic when you let an agent run autonomously.

## Prerequisites

- [Docker Engine](https://docs.docker.com/engine/) (or any Docker-compatible runtime)
- [A _Development Container_-compatible editor](https://containers.dev/supporting#editors) (Visual Studio Code, PhpStorm, Emacs...)

In Visual Studio Code, after editing `.devcontainer/devcontainer.json`, run **Dev Containers: Rebuild Container** from
the Command Palette (`Ctrl+Shift+P` / `Cmd+Shift+P`) to apply the change.

## OpenCode (recommended)

There is no official Dev Container feature for OpenCode yet, so install the CLI from the
`postCreateCommand` and add the Visual Studio Code extension. Edit `.devcontainer/devcontainer.json`:

```jsonc
{
  // Install the CLI on container creation (alongside the existing intelephense install)
  "postCreateCommand": "npm install -g intelephense && curl -fsSL https://opencode.ai/install | bash",
  "customizations": {
    "vscode": {
      "extensions": [
        "sst-dev.opencode",
        "bmewburn.vscode-intelephense-client",
        "xdebug.php-debug",
      ],
    },
  },
}
```

Rebuild the container, then run `opencode` in the integrated terminal or open the OpenCode panel.

### Using a local model

Run [Ollama](https://ollama.com) (FOSS) or [LM Studio](https://lmstudio.ai) (proprietary) on your host machine. The
Dev Container reaches the host through `host.docker.internal`, which is already mapped in
`compose.override.yaml`. No internet access is required, and this keeps working with the
[firewall](#optional-network-sandbox) enabled (private networks and the host gateway are allowed).

Point OpenCode at the host endpoint, for example Ollama on its default port:

```console
http://host.docker.internal:11434
```

LM Studio listens on `http://host.docker.internal:1234` by default. See the
[OpenCode providers documentation](https://opencode.ai/docs/providers/) for the exact
configuration.

### Using a remote model

Set the API key for your provider (Anthropic, OpenAI, OpenRouter, etc.) as documented by
OpenCode. With the [firewall](#optional-network-sandbox) enabled, add the provider's API domain
to the allowlist.

## Claude Code

Install [Claude Code](https://claude.ai/claude-code) (proprietary) through a Dev Container feature and add its
Visual Studio Code extension. Edit `.devcontainer/devcontainer.json`:

```jsonc
{
  "features": {
    "ghcr.io/devcontainers/features/node:1": {},
    "ghcr.io/devcontainers-extra/features/claude-code:2": {},
  },
  "customizations": {
    "vscode": {
      "extensions": [
        "anthropic.claude-code",
        "bmewburn.vscode-intelephense-client",
        "xdebug.php-debug",
      ],
    },
  },
}
```

Rebuild the container, then run `claude` in the integrated terminal or open the Claude Code panel.

Without the firewall, this is all you need. To let Claude Code run autonomously, enable the
[network sandbox](#optional-network-sandbox) first and add `anthropic.com`, `sentry.io`, and
`statsig.com` to the allowlist.

## Optional: network sandbox

Letting an agent edit files and run commands without confirmation (autonomous, or "YOLO", mode)
is convenient but unguarded. Pair it with a firewall that restricts the container's outbound
traffic to a short allowlist. Only run an agent autonomously with the firewall on.

### 1. Add the required tools to the dev image

Edit the `frankenphp_dev` stage in the `Dockerfile`:

```dockerfile
# hadolint ignore=DL3008
RUN <<-EOF
  mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
  apt-get update
  apt-get install -y --no-install-recommends \
    aggregate \
    curl \
    dnsmasq \
    dnsutils \
    iproute2 \
    ipset \
    iptables \
    jq \
    sudo
  install-php-extensions xdebug
  rm -rf /var/lib/apt/lists/*
  useradd -m -s /bin/bash nonroot
  # Allow nonroot to run only the firewall script as root, without a password
  echo "nonroot ALL=(root) NOPASSWD: /app/.devcontainer/init-firewall.sh" > /etc/sudoers.d/init-firewall
  chmod 0440 /etc/sudoers.d/init-firewall
  git config --system --add safe.directory /app
EOF
```

### 2. Grant the firewall capability

The firewall needs `NET_ADMIN`. Add it to the `php` service in
`.devcontainer/compose.devcontainer.yaml`:

```yaml
services:
  php:
    cap_add:
      - NET_ADMIN
```

### 3. Add the firewall script

Save the following as `.devcontainer/init-firewall.sh`. It uses `iptables` and `ipset` to drop
all outbound traffic except the allowlisted domains, and [dnsmasq](https://thekelleys.org.uk/dnsmasq/doc.html)
to resolve those domains dynamically so CDN IP rotation is handled gracefully.

```bash
#!/bin/bash
# Locks down outbound network access to an allowlist using iptables + ipset.
# dnsmasq intercepts DNS queries and adds the resolved IPs for allowed domains
# to the ipset dynamically, so the ipset stays current despite CDN IP rotation.
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
# Add your agent's provider domain here (e.g. anthropic.com, api.openai.com).
cat >/etc/dnsmasq.d/firewall-ipset.conf <<EOF
# Forward all queries to Docker's embedded DNS (actual port, not 53)
server=127.0.0.11#${DOCKER_DNS_PORT}
# Listen only on an alternate loopback address to avoid conflicting with
# any existing resolver on 127.0.0.1
listen-address=127.0.0.2
bind-interfaces
# Populate the allowed-domains ipset with every IP returned when resolving
# any of these domains (or their subdomains)
ipset=/github.com/registry.npmjs.org/packagist.org/cdn.jsdelivr.net/marketplace.visualstudio.com/vscode.blob.core.windows.net/update.code.visualstudio.com/allowed-domains
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

# Allow outbound traffic to local/private networks (e.g., Docker Compose services
# and a model running on the host via host.docker.internal)
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
```

### 4. Run the firewall on container start

Add a `postStartCommand` to `.devcontainer/devcontainer.json`:

```jsonc
{
  "postStartCommand": "sudo /app/.devcontainer/init-firewall.sh",
}
```

### 5. Enable autonomous mode

With the sandbox in place, you can let the agent skip confirmation prompts. For Claude Code, add
these settings to `customizations.vscode.settings` in `.devcontainer/devcontainer.json`:

```json
{
  "claudeCode.allowDangerouslySkipPermissions": true,
  "claudeCode.initialPermissionMode": "bypassPermissions"
}
```

Or from the terminal:

```console
claude --dangerously-skip-permissions
```

The default allowlist covers GitHub, Packagist, the npm registry, jsDelivr, and the Visual Studio Code
marketplace. Add any domain your agent needs (its provider API, a private registry) to the
`ipset=` line in the script, then rebuild the container:

```bash
# Domains are '/'-separated, ending with the ipset name
ipset=/github.com/.../your-domain.com/allowed-domains
```

## Without Visual Studio Code

The Dev Container configuration works with any tool that supports the
[Dev Container specification](https://containers.dev/), including:

- [Dev Container CLI](https://github.com/devcontainers/cli) (`devcontainer up`)
- [GitHub Codespaces](https://github.com/features/codespaces)
- JetBrains IDEs (with the Dev Containers plugin)

The `customizations.vscode.extensions` entries only apply to Visual Studio Code. In other
editors the agent CLI still installs from `postCreateCommand`; run it from the integrated
terminal, or install the editor's own extension if one exists.

## Troubleshooting

### The firewall blocks a required domain

If the agent, Composer, or npm fails to reach a service, add the domain to the `ipset=` line in
`.devcontainer/init-firewall.sh` and rebuild the container.

### The container fails to start

Ensure Docker is running and that you have allocated enough resources (at least 2 GB of RAM).
The firewall requires the `NET_ADMIN` capability, granted in
`.devcontainer/compose.devcontainer.yaml`.
