# Using Claude Code in YOLO Mode with Dev Containers

This project ships with a [Dev Container](https://containers.dev/) configuration that enables
[Claude Code](https://claude.ai/claude-code) to run in **YOLO mode** (fully autonomous, no permission prompts)
inside a sandboxed environment with network-level restrictions.

This setup is ideal for letting Claude Code work on your Symfony project autonomously
while ensuring it cannot reach arbitrary internet hosts.

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or any Docker-compatible runtime)
- [Visual Studio Code](https://code.visualstudio.com/) with the [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) extension
- A valid [Claude Code subscription or API key](https://claude.ai/claude-code)

## Quick Start

1. Open the project in Visual Studio Code.
2. When prompted "Reopen in Container", click **Reopen in Container**.
   Alternatively, open the Command Palette (`Ctrl+Shift+P` / `Cmd+Shift+P`) and run
   **Dev Containers: Reopen in Container**.
3. Wait for the container to build and start. On each container start, the
   `postStartCommand` configures the firewall automatically.
4. Claude Code is pre-installed and configured in YOLO mode — open the Claude Code
   panel in Visual Studio Code or run `claude` in the integrated terminal to start using it.

That's it. Claude Code will run without permission prompts, and the firewall ensures
network access is restricted to only the necessary services.

## What Is YOLO Mode?

YOLO mode (also known as "bypass permissions" mode) allows Claude Code to execute
commands, edit files, and perform actions without asking for confirmation at each step.
This dramatically speeds up autonomous coding workflows.

The Dev Container configuration enables this via two Visual Studio Code settings:

```json
{
  "claudeCode.allowDangerouslySkipPermissions": true,
  "claudeCode.initialPermissionMode": "bypassPermissions"
}
```

## Network Sandboxing

Running an AI agent with full autonomy requires guardrails. The Dev Container includes
a firewall script (`.devcontainer/init-firewall.sh`) that locks down outbound network
access using `iptables` and `ipset`. Only the following destinations are allowed:

| Destination                                       | Reason                          |
| ------------------------------------------------- | ------------------------------- |
| GitHub (`github.com`, `api.github.com`)           | Git operations, API access      |
| Anthropic (`anthropic.com`)                       | Claude Code backend             |
| npm registry (`registry.npmjs.org`)               | Node.js dependencies            |
| Packagist (`packagist.org`, `repo.packagist.org`) | PHP/Composer dependencies       |
| Visual Studio Code Marketplace                    | Extension downloads             |
| Sentry, Statsig                                   | Telemetry (used by Claude Code) |
| Host gateway IP                                   | Communication with Docker host  |

All other outbound connections are **rejected**. The firewall uses
[dnsmasq](https://thekelleys.org.uk/dnsmasq/doc.html) to dynamically resolve
and whitelist IPs for allowed domains, handling CDN IP rotation gracefully.

Inbound connections from the host gateway IP are allowed on all ports,
and ports 80, 443 (TCP), and 443 (UDP/HTTP3) are open to any source
so you can access your Symfony app from the host browser.

## Customizing the Allowed Domains

To allow additional domains (e.g., a private registry or API), edit
`.devcontainer/init-firewall.sh` and add them to the `ipset` line in the
dnsmasq configuration section:

```bash
# Domains are '/'-separated, ending with the ipset name
ipset=/github.com/anthropic.com/your-domain.com/allowed-domains
```

Then rebuild the Dev Container for the changes to take effect.

## Using Without Visual Studio Code

The Dev Container configuration works with any tool that supports the
[Dev Container specification](https://containers.dev/), including:

- [Dev Container CLI](https://github.com/devcontainers/cli) (`devcontainer up`)
- [GitHub Codespaces](https://github.com/features/codespaces)
- JetBrains IDEs (with the Dev Containers plugin)

To use Claude Code from the terminal inside the container:

```console
claude
```

To start directly in YOLO mode from the CLI:

```console
claude --dangerously-skip-permissions
```

## Troubleshooting

### Firewall blocks a required domain

If Claude Code or Composer/npm fails to reach a service, check the firewall
logs and add the domain to the dnsmasq allowlist as described above.

### Container fails to start

Ensure Docker is running and that you have allocated enough resources
(at least 2 GB of RAM for the container). The firewall setup requires
`NET_ADMIN` capability, which the Dev Container configures automatically
via Docker Compose.
