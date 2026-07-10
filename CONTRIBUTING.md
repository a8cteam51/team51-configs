# Contributing

## Workflow

Submit changes through a pull request against `trunk`. Do not push directly to `trunk`.

## Backwards-compatibility contract

Consumers resolve this package through Composer at `dev-trunk`. Every merge to `trunk` is therefore treated as a tagged release, with no intermediate staging step before consumption.

Changes must be additive or strictly permissive. Permitted changes include:

- Adding a new optional `workflow_call` input with a default.
- Adding a PHPCS, PHPStan, or PHPMD rule that is opt-in or applies only to new code paths, without making currently passing consumer code fail.
- Loosening an existing rule, such as widening an `exclude-pattern` or lowering a severity.
- Adding a new file.

Breaking narrowing is not permitted. Examples include:

- Renaming or removing an existing `workflow_call` input.
- Removing a ruleset file.
- Narrowing a rule or configuration that a passing consumer relies on without providing an escape hatch.

### Version floors

Minimum PHP and WordPress version floors in the PHPCS ruleset are the one sanctioned exception. A floor may be raised even when a consumer still targets an older version, provided that consumer has a documented override path.

PHPCS treats an included ruleset's `<config>` values as fixed once set. A consuming ruleset's own `<config>` tag cannot override them in either declaration order. Only `--runtime-set` on the PHPCS CLI invocation takes precedence over ruleset-level configuration.

A consumer may override either or both values by adding the corresponding flags to its `composer.json` lint script:

```json
"lint:php:phpcs": "phpcs --standard=./.phpcs.xml --runtime-set testVersion 8.3- --runtime-set minimum_wp_version 6.7 --basepath=. ./ -v"
```

## Reusable workflow inputs

An existing `workflow_call` input name is frozen once a consumer references it. Renaming it can prevent the consumer's `with: <old-name>:` value from taking effect or make the call invalid. Adding a new optional input with a default is permitted. Renaming or removing an existing input requires a major or breaking-change discussion first, not a silent pull request.

## CI gates

Changes under `.github/workflows/**` must pass `actionlint` and `zizmor`. [`.github/workflows/reusable-workflow-checks.yml`](.github/workflows/reusable-workflow-checks.yml) runs both checks, and [`.github/workflows/workflow-checks.yml`](.github/workflows/workflow-checks.yml) invokes it.

## Consumer validation

Before merging changes to PHPCS, PHPStan, or PHPMD configuration under `quality-assurance/`, run the affected configuration against a real consumer repository. Diff review alone is not sufficient. The reference consumer is `a8cteam51/team51-plugin-scaffold`.
