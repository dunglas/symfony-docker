# Project

This is a Symfony application running on [FrankenPHP](https://frankenphp.dev), generated using [Symfony Docker](https://github.com/dunglas/symfony-docker). The stack includes Caddy (via FrankenPHP), [Mercure](https://mercure.rocks) for real-time, and [Vulcain](https://vulcain.rocks) for preloading. The Dockerfile uses multi-stage builds with separate dev and prod targets.

## Dev Container Environment

This project runs inside a Dev Container with an outbound firewall that blocks all traffic except explicitly allowed domains.

## Whitelisting a Domain

If an outbound request fails (e.g., `curl`, `composer require`, `npm install` to a new registry), the domain likely needs to be added to the firewall allowlist.

Edit `.devcontainer/init-firewall.sh` and add the domain to the `ipset=` line in the dnsmasq configuration block:

```bash
ipset=/github.com/anthropic.com/.../NEW_DOMAIN.COM/allowed-domains
```

Then rebuild the Dev Container to apply the change.
