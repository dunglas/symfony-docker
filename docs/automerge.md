# Merging Dependabot Updates Automatically

Symfony Docker pins its Docker images and GitHub Actions by digest,
and ships a [Dependabot](https://docs.github.com/code-security/dependabot)
configuration that opens a pull request when a new version is published.
GitHub can merge these pull requests for you once CI passes.

## GitHub App

The default `GITHUB_TOKEN` can't merge changes to `.github/workflows/`,
and merges made with it don't trigger CI on the default branch.
Use a token from a dedicated GitHub App instead:

<!-- markdownlint-disable MD013 -->

1. [Create a GitHub App](https://docs.github.com/apps/creating-github-apps/registering-a-github-app/registering-a-github-app)
   with webhooks disabled, and the **Contents**, **Pull requests** and
   **Workflows** repository permissions set to **Read and write**
2. Generate a private key for the app, then install it on your repository
3. In **Settings** > **Secrets and variables** > **Dependabot**, add the app's
   client ID as `AUTO_MERGE_APP_CLIENT_ID` and the private key as
   `AUTO_MERGE_APP_PRIVATE_KEY`

<!-- markdownlint-enable MD013 -->

Workflows triggered by Dependabot can only read Dependabot secrets,
not Actions secrets.

## Repository Settings

1. In **Settings** > **General**, enable **Allow auto-merge**
2. In **Settings** > **Rules** > **Rulesets**, add a ruleset targeting the
   default branch that requires the `Tests` and `Lint` status checks

Without required status checks, pull requests are merged right away,
before CI completes.

## Workflow

Create `.github/workflows/auto-merge.yaml`:

<!-- markdownlint-disable MD013 -->

```yaml
name: Auto-merge

on:
  pull_request: ~

permissions: {}

jobs:
  auto-merge:
    name: Auto-merge
    runs-on: ubuntu-latest
    if: github.event.pull_request.user.login == 'dependabot[bot]'
    steps:
      - name: Generate a token
        id: app-token
        uses: actions/create-github-app-token@bcd2ba49218906704ab6c1aa796996da409d3eb1 # v3.2.0
        with:
          client-id: ${{ secrets.AUTO_MERGE_APP_CLIENT_ID }}
          private-key: ${{ secrets.AUTO_MERGE_APP_PRIVATE_KEY }}
          permission-contents: write
          permission-pull-requests: write
          permission-workflows: write
      - name: Enable auto-merge
        run: gh pr merge --auto --rebase "$PR_URL"
        env:
          PR_URL: ${{ github.event.pull_request.html_url }}
          GH_TOKEN: ${{ steps.app-token.outputs.token }}
```

<!-- markdownlint-enable MD013 -->

Composer updates change your application's dependencies,
and CI doesn't run your test suite by default.
Until you uncomment the test steps in `.github/workflows/ci.yaml`,
exclude them in the `if` condition:

<!-- markdownlint-disable MD013 -->

```yaml
if: github.event.pull_request.user.login == 'dependabot[bot]' && !startsWith(github.head_ref, 'dependabot/composer/')
```

<!-- markdownlint-enable MD013 -->
