# Using AI Coding Agents with Dev Containers

This project ships with a [Dev Container](https://containers.dev/) configuration that enables
AI coding agents to run autonomously inside a sandboxed environment with network-level restrictions.

[Kilo Code](https://kilocode.ai), an open source coding agent, is pre-installed and configured out
of the box. It works with both **local models** (via [Ollama](https://ollama.com) or
[LM Studio](https://lmstudio.ai), so your code never leaves your machine) and **remote providers**
(any OpenAI-compatible API including Anthropic, OpenRouter, and more). You pick the model.

The setup also works with other agents such as [Claude Code](https://claude.ai/claude-code)
(see [Switching to Claude Code](#switching-to-claude-code)), [OpenAI Codex CLI](https://github.com/openai/codex),
and [opencode](https://opencode.ai).

This setup is ideal for letting AI agents work on your Symfony project autonomously
while ensuring they cannot reach arbitrary internet hosts.

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or any Docker-compatible runtime)
- [Visual Studio Code](https://code.visualstudio.com/) with the [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) extension
- A model to drive the agent: a local runtime (Ollama / LM Studio) or an API key for a remote provider

## Quick Start

1. Open the project in Visual Studio Code.
2. When prompted "Reopen in Container", click **Reopen in Container**.
   Alternatively, open the Command Palette (`Ctrl+Shift+P` / `Cmd+Shift+P`) and run
   **Dev Containers: Reopen in Container**.
3. Wait for the container to build and start. On each container start, the
   `postStartCommand` configures the firewall automatically.
4. Open the **Kilo Code** panel in Visual Studio Code and choose a model - see
   [Choosing a model](#choosing-a-model) below.

That's it. Inside the container, Kilo Code runs with **auto-approval enabled** (a
`~/.config/kilo/kilo.jsonc` written on create sets `"permission": { "*": "allow" }`), so it acts
without confirmation prompts. This is safe here because the firewall restricts network access to
only the necessary services. The config is container-scoped and does not affect Kilo Code on your
host.

## Choosing a model

Kilo Code needs a model provider. Configure it once in the Kilo Code panel
(**Settings** → **API Provider**).

### Local model (private, no API cost)

Run the model on your **host** machine (so it can use the host GPU - Apple Metal or NVIDIA) and
point Kilo Code at it from inside the container.

1. Install [Ollama](https://ollama.com) on the host then pull a coding model, e.g. `qwen3-coder` / `devstral` (requires a capable GPU).
2. Create a  `kilo.jsonc` in the directory root:

   ```jsonc
   {
      "$schema": "https://app.kilo.ai/config.json",
      "model": "ollama/qwen3-coder-next", // change with your model

      "provider": {
         "ollama": {
            "options": {
               "baseURL": "http://localhost:11434/v1"
            },
            "models": {
               "qwen3-coder-next": {
                  "name": "Qwen3-Coder-Next (local)",
                  "tool_call": true, // required for agent mode (file edits, terminal)
                  "reasoning": false,
                  "limit": {
                     "context": 131072, // Kilo's compaction bookkeeping
                     "output": 32000
                  }
               }
            }
         }
      }
   }
   ```

See Kilo Code's own guides for details:
[Local models](https://kilocode.ai/docs/advanced-usage/local-models) ·
[Ollama provider](https://kilo.ai/docs/ai-providers/ollama).

### Remote model

To use a hosted provider (OpenAI-compatible, Anthropic, OpenRouter, etc.):

1. Add the provider's API domain to the firewall allowlist in `.devcontainer/init-firewall.sh`
   (see [Customizing the Allowed Domains](#customizing-the-allowed-domains)), e.g. `api.openai.com`
   or `openrouter.ai`. Rebuild the Dev Container.
2. In the Kilo Code panel, select the provider and enter your API key.

See [Kilo Code's AI providers reference](https://kilo.ai/docs/ai-providers) for the full list.

## Network Sandboxing

Running an AI agent with full autonomy requires guardrails. The Dev Container includes
a firewall script (`.devcontainer/init-firewall.sh`) that locks down outbound network
access using `iptables` and `ipset`. Only the following destinations are allowed:

| Destination                                          | Reason                            |
| ---------------------------------------------------- | -------------------------------   |
| GitHub (`github.com`, `api.github.com`)              | Git operations, API access        |
| npm registry (`registry.npmjs.org`)                  | Node.js dependencies              |
| Packagist (`packagist.org`, `repo.packagist.org`)    | PHP/Composer dependencies         |
| jsDelivr (`cdn.jsdelivr.net`)                        | AssetMapper assets                |
| Visual Studio Code Marketplace                       | Extension downloads               |
| Host gateway IP                                      | Docker host + local model runtime |
| Private networks (`10/8`, `172.16/12`, `192.168/16`) | Docker Compose services           |

All other outbound connections are **rejected**. A **local** model reached on the host gateway
needs no allowlist entry; a **remote** provider does (see below). The firewall uses
[dnsmasq](https://thekelleys.org.uk/dnsmasq/doc.html) to dynamically resolve
and whitelist IPs for allowed domains, handling CDN IP rotation gracefully.

Inbound connections from the host gateway IP are allowed on all ports,
and ports 80, 443 (TCP), and 443 (UDP/HTTP3) are open to any source
so you can access your Symfony app from the host browser.

## Customizing the Allowed Domains

To allow additional domains (e.g., a private registry or a remote model provider), edit
`.devcontainer/init-firewall.sh` and add them to the `ipset` line in the
dnsmasq configuration section:

```bash
# Domains are '/'-separated, ending with the ipset name
ipset=/github.com/api.openai.com/your-domain.com/allowed-domains
```

Then rebuild the Dev Container for the changes to take effect.

## Switching to Claude Code

To use [Claude Code](https://claude.ai/claude-code) instead of (or alongside) Kilo Code, add
the four pieces below and rebuild the Dev Container.

1. **Add the devcontainer feature** in `.devcontainer/devcontainer.json`:

   ```json
   "features": {
       "ghcr.io/devcontainers/features/node:1": {},
       "ghcr.io/devcontainers-extra/features/claude-code:2": {}
   }
   ```

2. **Install the extension and enable YOLO mode** in the same file's `customizations.vscode` block:

   ```json
   "extensions": [
       "anthropic.claude-code",
       "bmewburn.vscode-intelephense-client",
       "xdebug.php-debug"
   ],
   "settings": {
       "claudeCode.allowDangerouslySkipPermissions": true,
       "claudeCode.initialPermissionMode": "bypassPermissions",
       "launch": { "...": "..." }
   }
   ```

   YOLO mode (also called "bypass permissions" mode) lets Claude Code execute commands, edit files,
   and act without confirmation at each step, which speeds up autonomous workflows.

3. **Symlink the project context** so Claude Code picks it up, by appending to `postCreateCommand`:

   ```jsonc
   "postCreateCommand": "npm install -g intelephense && ln -sf .devcontainer/AGENTS.md AGENTS.md && ln -sf .devcontainer/AGENTS.md CLAUDE.md && ln -sfn .devcontainer/.claude .claude",
   ```

4. **Allow Anthropic's domains** in `.devcontainer/init-firewall.sh` by adding them to the `ipset`
   line (`sentry.io` and `statsig.com` are used by Claude Code's telemetry):

   ```bash
   ipset=/github.com/anthropic.com/sentry.io/statsig.com/registry.npmjs.org/.../allowed-domains
   ```

Then run `claude` in the integrated terminal, or open the Claude Code panel.

## Using Other Agents

The Dev Container's network sandbox and project context (`.devcontainer/AGENTS.md`) work
with any AI coding agent. You just need to install the agent and whitelist the domains it
needs to reach.

### OpenAI Codex CLI

1. Add the OpenAI API domain to the firewall allowlist in `.devcontainer/init-firewall.sh`
   (see [Customizing the Allowed Domains](#customizing-the-allowed-domains)):

   ```bash
   ipset=/.../api.openai.com/allowed-domains
   ```

2. Install and run Codex inside the container:

   ```console
   npm install -g @openai/codex
   export OPENAI_API_KEY=your-key
   codex --full-auto
   ```

### opencode

1. Add the required API domain to the firewall allowlist (e.g., `api.anthropic.com`,
   `api.openai.com`, or your provider's domain).

2. Install and run opencode inside the container:

   ```console
   curl -fsSL https://opencode.ai/install | bash
   opencode
   ```

### Other Agents

For any other agent, follow the same pattern:

1. Add the agent's API domain(s) to the firewall allowlist.
2. Install the agent inside the container.
3. Run it - the `.devcontainer/AGENTS.md` file provides project context
   to agents that support the convention.

## Using Without Visual Studio Code

The Dev Container configuration works with any tool that supports the
[Dev Container specification](https://containers.dev/), including:

- [Dev Container CLI](https://github.com/devcontainers/cli) (`devcontainer up`)
- [GitHub Codespaces](https://github.com/features/codespaces)
- JetBrains IDEs (with the Dev Containers plugin)

Kilo Code is a Visual Studio Code extension; from a plain terminal inside the container, use a
CLI agent instead (see [Using Other Agents](#using-other-agents)).

## Troubleshooting

### Firewall blocks a required domain

If your agent or Composer/npm fails to reach a service, check the firewall
logs and add the domain to the dnsmasq allowlist as described above. A remote model provider
needs its API domain whitelisted; a local model on the host gateway does not.

### Kilo Code can't reach a local model

Confirm the runtime is listening on the host (`curl http://localhost:11434/api/tags` on the host),
that Kilo Code's base URL is `http://host.docker.internal:11434`, and - on Linux native Docker -
that `host.docker.internal` resolves (see the note under [Local model](#local-model-private-no-api-cost)).

### Container fails to start

Ensure Docker is running and that you have allocated enough resources
(at least 2 GB of RAM for the container). The firewall setup requires
`NET_ADMIN` capability, which the Dev Container configures automatically
via Docker Compose.
