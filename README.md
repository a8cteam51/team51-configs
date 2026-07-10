# team51-configs

team51-configs is a shared package of quality-assurance configurations, including PHPCS, PHPStan, and PHPMD rulesets, and reusable GitHub Actions CI workflows for linting, syntax checks, PHPUnit, Playwright E2E, supply-chain audits, and workflow checks across Team51 repositories.

## PHPMD

PHPMD is deprecated for new projects because the PHPStan strict stack supersedes it. It remains in this package for backwards compatibility with existing consumers and will be removed once team51-project-scaffold migrates off it.

## Version pinning

Consumers normally require `a8cteam51/team51-configs` at `dev-trunk` to receive current changes. Once `vX.Y.Z` tags exist, consumers that need an immutable Composer pin can use a semantic-version constraint instead, for example:

```json
"a8cteam51/team51-configs": "^1.0"
```

Reusable workflows currently use a mutable branch reference such as `uses: a8cteam51/team51-configs/.github/workflows/reusable-*.yml@<branch>`. Once fixed `vX` tags exist, consumers can use `@vX` or a full commit SHA instead for an immutable, supply-chain-safer reference.

## PHP 8.x compatibility sniffs

This package deliberately pins `phpcompatibility/phpcompatibility-wp` to `"*"` because Composer stability flags such as `@alpha` and the `minimum-stability` setting are honored only in the root package. A consumer project that needs real PHP 8.x compatibility sniffing must opt in at its own project root by requiring the alpha releases directly:

```json
"phpcompatibility/phpcompatibility-wp": "^3@alpha",
"phpcompatibility/php-compatibility": "^10@alpha"
```

Stability flags are root-only, so this shared package cannot opt in for you.
