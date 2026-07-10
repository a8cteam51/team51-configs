# Reusable workflows

This document is the per-workflow `workflow_call` input reference for the nine reusable workflows in this repository. The [backwards-compatibility rules](../CONTRIBUTING.md#reusable-workflow-inputs) freeze existing inputs; additional inputs must be optional and define a default.

## block.json Schema Check — `.github/workflows/reusable-block-json-check.yml`

Finds `block.json` files under the project path and validates each one against the WordPress `block.json` schema from `schemas.wp.org`.

| Input | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `project-path` | `string` | No | `'.'` | Path to the project, relative to the repository root. |
| `node-version` | `string` | No | `'26'` | Node.js version. |

The workflow exits successfully with a message when it finds no `block.json` files. The search prunes `./node_modules` and `./vendor` under `project-path`.

```yaml
jobs:
  block-json:
    uses: a8cteam51/team51-configs/.github/workflows/reusable-block-json-check.yml@v1
```

## CodeQL — `.github/workflows/reusable-codeql.yml`

Runs GitHub CodeQL initialization and analysis as one matrix job per configured language.

| Input | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `languages` | `string` | No | `'["actions"]'` | Non-empty JSON array of CodeQL languages. |

An empty `languages` array fails validation instead of producing an empty successful matrix. CodeQL has no PHP analyzer, so PHP is outside this workflow; PHPStan provides static analysis for PHP.

```yaml
jobs:
  codeql:
    permissions:
      actions: read
      contents: read
      security-events: write
    uses: a8cteam51/team51-configs/.github/workflows/reusable-codeql.yml@v1
    with:
      languages: '["actions", "javascript-typescript"]'
```

## PHP Lint — `.github/workflows/reusable-php-lint.yml`

Installs Composer dependencies and runs each configured Composer script as a separate matrix job.

| Input | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `scripts` | `string` | Yes | — | Non-empty JSON array of Composer scripts; each script runs as a parallel job. |
| `php-version` | `string` | No | `'8.5'` | PHP version for the script jobs. |
| `project-path` | `string` | No | `'.'` | Path to the project, relative to the repository root. |
| `composer-options` | `string` | No | `'--prefer-dist --ignore-platform-req=php+'` | Composer options passed to dependency installation. |

An empty `scripts` array fails validation. Each matrix job invokes its script as `composer "$SCRIPT"`.

```yaml
jobs:
  php-lint:
    uses: a8cteam51/team51-configs/.github/workflows/reusable-php-lint.yml@v1
    with:
      scripts: '["lint:php:phpcs", "lint:php:phpstan"]'
```

## PHP Syntax Check — `.github/workflows/reusable-php-syntax-check.yml`

Runs `php -l` over every PHP file under the project path for each configured PHP version, without installing dependencies or loading an autoloader.

| Input | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `project-path` | `string` | No | `'.'` | Path to the project, relative to the repository root. |
| `php-versions` | `string` | No | `'["8.5"]'` | Non-empty JSON array of PHP versions to check, such as `["8.5","8.6"]`. |

An empty `php-versions` array fails validation. The file search prunes `./vendor` and `./node_modules` under `project-path`.

```yaml
jobs:
  php-syntax:
    uses: a8cteam51/team51-configs/.github/workflows/reusable-php-syntax-check.yml@v1
    with:
      php-versions: '["8.5", "8.6"]'
```

## PHPUnit — `.github/workflows/reusable-phpunit.yml`

Installs Composer dependencies, optionally starts a WordPress environment, runs a Composer test script, and stops the environment after the test run.

| Input | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `project-path` | `string` | No | `'.'` | Path to the project, relative to the repository root. |
| `php-version` | `string` | No | `'8.5'` | PHP version for the test run. |
| `wp-version` | `string` | No | `''` | WordPress version tag. An empty value defers to the consumer's `.wp-env.json` `core` setting or wp-env's default stable version. |
| `wp-env-core` | `string` | No | `''` | Full `WP_ENV_CORE` value. Overrides `wp-version` and accepts repository refs or ZIP URLs. |
| `composer-script` | `string` | No | `'test'` | Composer script invoked for the test run. |
| `composer-options` | `string` | No | `'--prefer-dist --ignore-platform-req=php+'` | Composer options passed to dependency installation. |
| `node-version` | `string` | No | `'26'` | Node.js version used by the wp-env CLI. |
| `wp-env-config-file` | `string` | No | `''` | wp-env configuration path relative to `project-path`. An empty value uses `.wp-env.json`; use a separate file per environment instead of the deprecated implicit development/test split. |
| `wp-env-xdebug` | `string` | No | `''` | Value passed to `wp-env start --xdebug=<mode>`, such as `coverage`. An empty value starts without Xdebug. |
| `needs-wp-env` | `boolean` | No | `true` | Whether to run `npm ci` and start and stop wp-env. Set `false` for unit-only suites without a WordPress runtime. |

When `needs-wp-env` is `true`, the project must contain `package.json` and `package-lock.json`. `wp-env-core` takes precedence over `wp-version`, and the stop step runs under `always()`.

```yaml
jobs:
  phpunit:
    uses: a8cteam51/team51-configs/.github/workflows/reusable-phpunit.yml@v1
```

## Playwright E2E — `.github/workflows/reusable-playwright-e2e.yml`

Installs PHP and Node.js dependencies, builds assets when configured, runs Playwright against wp-env, and uploads a failure report.

| Input | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `project-path` | `string` | No | `'.'` | Path to the project, relative to the repository root. `npm ci` requires `package.json` and `package-lock.json` there. |
| `artifact-slug` | `string` | Yes | — | Label used in the uploaded failure-report artifact name. |
| `php-version` | `string` | No | `'8.5'` | PHP version. |
| `wp-version` | `string` | No | `''` | WordPress version tag. An empty value defers to the consumer's `.wp-env.json` `core` setting or wp-env's default stable version. |
| `wp-env-core` | `string` | No | `''` | Full `WP_ENV_CORE` value. Overrides `wp-version` and accepts repository refs or ZIP URLs. |
| `node-version` | `string` | No | `'26'` | Node.js version. |
| `composer-options` | `string` | No | `'--prefer-dist --no-dev --ignore-platform-req=php+'` | Composer install options. Omit `--no-dev` when a development-mode scoping pipeline populates `dependencies/`. |
| `build-script` | `string` | No | `'build'` | npm script that builds assets before wp-env starts. An empty value skips the build. |
| `wp-env-config-file` | `string` | No | `''` | wp-env configuration path relative to `project-path`. An empty value uses `.wp-env.json`. |
| `playwright-script` | `string` | No | `'test:e2e'` | npm script that runs the Playwright suite. |

The workflow installs and caches Chromium, stops wp-env under `always()`, and uploads `playwright-report-<artifact-slug>` on failure. The required slug prevents artifact-name collisions between multiple E2E jobs in one workflow run.

```yaml
jobs:
  playwright:
    uses: a8cteam51/team51-configs/.github/workflows/reusable-playwright-e2e.yml@v1
    with:
      artifact-slug: primary
```

## Scripts/Styles Lint — `.github/workflows/reusable-scripts-styles-lint.yml`

Installs npm dependencies and conditionally runs the configured ESLint and Stylelint scripts.

| Input | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `project-path` | `string` | No | `'.'` | Path to the project, relative to the repository root. `npm ci` requires `package.json` and `package-lock.json` there. |
| `node-version` | `string` | No | `'26'` | Node.js version. |
| `lint-scripts` | `boolean` | No | `true` | Whether to run `npm run lint:scripts` with ESLint. Set `false` for repositories without JavaScript sources. |
| `lint-styles` | `boolean` | No | `true` | Whether to run `npm run lint:styles` with Stylelint. Set `false` for repositories without CSS sources. |

```yaml
jobs:
  scripts-styles:
    uses: a8cteam51/team51-configs/.github/workflows/reusable-scripts-styles-lint.yml@v1
```

## Supply-Chain Audit — `.github/workflows/reusable-supply-chain-audit.yml`

Runs Composer and npm dependency audits as independently gated jobs.

| Input | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `project-path` | `string` | No | `'.'` | Path to the project, relative to the repository root. |
| `php-version` | `string` | No | `'8.5'` | PHP version used for `composer audit`. |
| `node-version` | `string` | No | `'26'` | Node.js version used for `npm audit`. |
| `composer-audit` | `boolean` | No | `true` | Whether to run `composer audit`. Set `false` for repositories without `composer.json`; at least one audit must remain enabled. |
| `npm-audit` | `boolean` | No | `true` | Whether to run `npm audit` after `npm ci`. Set `false` for repositories without `package.json`; a package lock is required. The full dependency graph is audited by default, and at least one audit must remain enabled. |
| `composer-audit-flags` | `string` | No | `'--abandoned=report'` | Extra `composer audit` flags. The default audits the full graph, fails on security advisories, and reports abandoned packages without failing on them; pass `--no-dev` for production dependencies only. |
| `composer-options` | `string` | No | `'--prefer-dist --ignore-platform-req=php+'` | Composer options passed to dependency installation. |
| `npm-audit-flags` | `string` | No | `'--audit-level=high'` | Extra `npm audit` flags. The default audits the full graph, reports low and moderate advisories without failing, and fails on high or critical advisories; pass `--omit=dev` for production dependencies only. |
| `fail-on-findings` | `boolean` | No | `true` | Whether audit findings fail the job. Set `false` to report findings in the logs without blocking. |

Setting `fail-on-findings` to `false` makes valid findings advisory-only. Setting both audit inputs to `false` fails validation instead of reporting success without an audit.

```yaml
jobs:
  supply-chain:
    uses: a8cteam51/team51-configs/.github/workflows/reusable-supply-chain-audit.yml@v1
```

## Workflow Checks — `.github/workflows/reusable-workflow-checks.yml`

Runs Actionlint and Zizmor static analysis against `.github/workflows/**`.

This workflow takes no inputs. A caller that wants the Zizmor job's SARIF uploaded to the Security tab must grant `security-events: write`; a reusable workflow cannot exceed the permissions granted by its caller.

```yaml
jobs:
  workflow-checks:
    permissions:
      actions: read
      contents: read
      security-events: write
    uses: a8cteam51/team51-configs/.github/workflows/reusable-workflow-checks.yml@v1
```
