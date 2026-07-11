# team51-configs

team51-configs is a shared package of quality-assurance configurations, including PHPCS, PHPStan, and PHPMD rulesets, and reusable GitHub Actions CI workflows for linting, syntax checks, PHPUnit, Playwright E2E, supply-chain audits, and workflow checks across Team51 repositories.

## Layout

[`php/quality-assurance/`](php/quality-assurance) is the canonical home for the shared PHPCS and PHPStan configuration. The pre-move `quality-assurance/` paths remain in place as frozen compatibility shims — every existing `<rule ref>`, `includes:`, and `require` path a consumer already uses keeps resolving with no changes required:

| Old path (frozen shim) | New canonical path |
| --- | --- |
| `quality-assurance/phpcs.dist.xml` | `php/quality-assurance/phpcs.dist.xml` |
| `quality-assurance/phpstan.dist.neon` | `php/quality-assurance/phpstan.dist.neon` |
| `quality-assurance/phpstan.dist.neon.php` | `php/quality-assurance/phpstan.dist.neon.php` |
| `quality-assurance/phpmd.dist.xml` | _(deprecated — stays at this legacy path only; not part of the canonical `php/quality-assurance/` layout)_ |

The three shims (`phpcs.dist.xml`, `phpstan.dist.neon`, `phpstan.dist.neon.php`) are one-line redirects to the canonical file, each carrying a one-line comment marking it as frozen; edit the canonical file under `php/quality-assurance/`, never the shim. `quality-assurance/phpmd.dist.xml` is not a shim — PHPMD is deprecated and was never moved, so that file remains its only home.

Migrating an include path to the canonical location is optional and may happen at a consumer's own pace — the old paths are a permanent compatibility tier, not deprecated-with-a-cutoff. `quality-tools/phpcs.xml.dist` is unrelated to this move: it is a separate, older, intentionally different ruleset (see [Migrating to the quality-assurance ruleset](docs/migrating-to-quality-assurance.md)) and stays exactly where it is. `docker/` and `composer/` are unaffected by this layout.

## PHPMD

PHPMD is deprecated and retained only for backwards compatibility with existing consumers at its legacy path, `quality-assurance/phpmd.dist.xml` — it was intentionally not moved to `php/quality-assurance/`. The PHPStan strict stack is the static-analysis configuration for new projects.

## Reusable workflows

See [Reusable workflows](docs/workflows.md) for the `workflow_call` inputs and caller examples for each workflow.

## Node configs

The npm package ships four base configs under `node/`, exported through the `package.json` `exports` map for imports such as `@a8cteam51/team51-configs/node/<file>`:

- `./node/eslint.config.base.mjs` — flat ESLint baseline combining the WordPress recommended, test-unit, and test-playwright configs; import it and append project overrides.
- `./node/stylelint.config.base.js` — Stylelint baseline extending `@wordpress/stylelint-config/scss`; spread it into the consumer's config rather than using `extends`, which drops `ignoreFiles`.
- `./node/playwright.config.base.js` — Playwright baseline spreading `@wordpress/scripts/config/playwright.config.js` with `testDir` defaulted to `tests/EndToEnd`; spread it and override individual nested keys such as `use`, `webServer`, and `projects` as needed.
- `./node/tsconfig.base.json` — TypeScript compiler-option deltas assuming modern TypeScript 6+ defaults; extend it from a consumer `tsconfig.json`.

A consumer adds this repository as an npm git dependency:

```json
"@a8cteam51/team51-configs": "github:a8cteam51/team51-configs#<tag-or-sha>"
```

The [Version pinning](#version-pinning) section documents the semver-tag and SHA pinning discipline used for Composer and reusable workflows. The same discipline applies here: use a tag or full SHA, never a bare branch.

Unlike a registry dependency, Dependabot and Renovate cannot, in the general case, see or bump a `github:` git-dependency ref automatically the way they track registry semver ranges. Bumping to a new tag or SHA requires a manual edit to the consumer's `package.json`.

## Version pinning

Consumers require `a8cteam51/team51-configs` at `dev-trunk` by default to track the latest state. A consumer that selects tagged releases uses a semantic-version constraint against `vX.Y.Z` tags, for example:

```json
"a8cteam51/team51-configs": "^1.0"
```

The consumer's Composer lock file records the resolved release and source reference for reproducible installs.

A reusable-workflow reference uses a fixed `vX` tag or a full commit SHA:

```yaml
uses: a8cteam51/team51-configs/.github/workflows/reusable-*.yml@vX
```

A full SHA is intrinsically immutable, and published tags are fixed by this repository's no-retagging policy. A bare branch name in the `uses:` position is mutable and less supply-chain-safe than a tag or SHA pin.

## PHP 8.x compatibility sniffs

This package deliberately pins `phpcompatibility/phpcompatibility-wp` to `"*"` because Composer stability flags such as `@alpha` and the `minimum-stability` setting are honored only in the root package. A consumer project that needs real PHP 8.x compatibility sniffing must opt in at its own project root by requiring the alpha releases directly:

```json
"phpcompatibility/phpcompatibility-wp": "^3@alpha",
"phpcompatibility/php-compatibility": "^10@alpha"
```
