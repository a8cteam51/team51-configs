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

## Compatibility shims

[`php/quality-assurance/`](php/quality-assurance) is the canonical home for the PHPCS, PHPStan, and PHPMD configuration. The pre-move `quality-assurance/` paths are frozen compatibility shims, listed in the README's [Layout](README.md#layout) table. A shim's content — the redirect ref/include, or, for `phpmd.dist.xml`, the frozen full copy — is itself part of the BC contract: edit the canonical file under `php/quality-assurance/`, never a shim. The `shim-integrity` job in [`.github/workflows/quality.yml`](.github/workflows/quality.yml) fails CI if a shim's content drifts from its expected form.

## Reusable workflow inputs

An existing `workflow_call` input name is frozen once a consumer references it. Renaming it can prevent the consumer's `with: <old-name>:` value from taking effect or make the call invalid. Adding a new optional input with a default is permitted. Renaming or removing an existing input requires a major or breaking-change discussion first, not a silent pull request.

## CI gates

Changes under `.github/workflows/**` must pass `actionlint` and `zizmor`. [`.github/workflows/reusable-workflow-checks.yml`](.github/workflows/reusable-workflow-checks.yml) runs both checks, and [`.github/workflows/workflow-checks.yml`](.github/workflows/workflow-checks.yml) invokes it.

## Consumer validation

Before merging changes to PHPCS, PHPStan, or PHPMD configuration under `quality-assurance/`, run the affected configuration against a real consumer repository. Diff review alone is not sufficient. The reference consumer is `a8cteam51/team51-plugin-scaffold`.

## Versioning

Consumers continue to resolve this package at `dev-trunk` by default. Semantic-version tags in the form `vX.Y.Z` provide an additional immutable pin at meaningful points on `trunk`.

Classify tagged releases as follows:

- PATCH: Bug fixes, documentation-only changes, and non-behavioral fixes.
- MINOR: Additive or strictly permissive changes allowed by the backwards-compatibility contract, including a new optional `workflow_call` input with a default, a new ruleset or file, a new opt-in rule, or a loosened rule.
- MAJOR: Reserve for the sanctioned paths described under [Version floors](#version-floors) and [Reusable workflow inputs](#reusable-workflow-inputs), including version-floor raises and changes that require a major or breaking-change discussion.

Tag after a meaningful merge to `trunk` that a consumer could plausibly want to pin. A tag is not required for every commit. Create the tag from `trunk`. Never rewrite or force-move a pushed tag.

A human maintainer creates each tag as a deliberate, manual action. Do not automate tagging in CI.
