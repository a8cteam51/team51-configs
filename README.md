# team51-configs

team51-configs is a shared package of quality-assurance configurations, including PHPCS, PHPStan, and PHPMD rulesets, and reusable GitHub Actions CI workflows for linting, syntax checks, PHPUnit, Playwright E2E, supply-chain audits, and workflow checks across Team51 repositories.

## Layout

[`php/quality-assurance/`](php/quality-assurance) is the canonical home for the shared PHPCS, PHPStan, and PHPMD configuration. The pre-move `quality-assurance/` paths remain in place as frozen compatibility shims — every existing `<rule ref>`, `includes:`, and `require` path a consumer already uses keeps resolving with no changes required:

| Old path (frozen shim) | New canonical path |
| --- | --- |
| `quality-assurance/phpcs.dist.xml` | `php/quality-assurance/phpcs.dist.xml` |
| `quality-assurance/phpstan.dist.neon` | `php/quality-assurance/phpstan.dist.neon` |
| `quality-assurance/phpstan.dist.neon.php` | `php/quality-assurance/phpstan.dist.neon.php` |
| `quality-assurance/phpmd.dist.xml` | `php/quality-assurance/phpmd.dist.xml` |

Three of the four shims (`phpcs.dist.xml`, `phpstan.dist.neon`, `phpstan.dist.neon.php`) are one-line redirects to the canonical file. `quality-assurance/phpmd.dist.xml` is a frozen full copy instead: PHPMD resolves `<rule ref>` against the process's working directory rather than the referencing ruleset file's own directory, so a redirect ref there would resolve incorrectly for a real consumer running phpmd from its own project root. Every shim carries a one-line comment marking it as frozen; edit the canonical file under `php/quality-assurance/`, never the shim.

Migrating an include path to the canonical location is optional and may happen at a consumer's own pace — the old paths are a permanent compatibility tier, not deprecated-with-a-cutoff. `quality-tools/phpcs.xml.dist` is unrelated to this move: it is a separate, older, intentionally different ruleset (see [Migrating to the quality-assurance ruleset](docs/migrating-to-quality-assurance.md)) and stays exactly where it is. `docker/` and `composer/` are unaffected by this layout.

## PHPMD

PHPMD is retained for backwards compatibility with existing consumers. The PHPStan strict stack is the static-analysis configuration for new projects.

## Reusable workflows

See [Reusable workflows](docs/workflows.md) for the `workflow_call` inputs and caller examples for each workflow.

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
