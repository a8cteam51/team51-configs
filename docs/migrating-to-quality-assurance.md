# Migrating to the quality-assurance PHPCS ruleset

This guide covers consumer rulesets that reference [`quality-tools/phpcs.xml.dist`](../quality-tools/phpcs.xml.dist) and adopt [`quality-assurance/phpcs.dist.xml`](../quality-assurance/phpcs.dist.xml).

## A separate move: the php/ canonical home

Independently of the `quality-tools` → `quality-assurance` migration this guide covers, the PHPCS, PHPStan, and PHPMD configuration under `quality-assurance/` has a canonical home at [`php/quality-assurance/`](../php/quality-assurance). The `quality-assurance/` paths referenced throughout this guide remain valid — they are frozen compatibility shims that redirect to the canonical files, not removed paths. See [Layout](../README.md#layout) in the README for the full old-path-to-new-path table. A consumer migrating off `quality-tools/phpcs.xml.dist` may reference either `quality-assurance/phpcs.dist.xml` (the shim) or `php/quality-assurance/phpcs.dist.xml` (the canonical file) — both resolve identically.

## Ruleset differences

| Area | `quality-tools/phpcs.xml.dist` | `quality-assurance/phpcs.dist.xml` |
| --- | --- | --- |
| Role | Frozen pre-modernization WPCS v3 ruleset | Ruleset for new and modernized consumers |
| Ruleset identity | `A8CTeam51 Sites` | No `name` attribute |
| PHPCompatibilityWP `testVersion` | `8.1-` | `8.5-` |
| WordPress-Extra `minimum_wp_version` | `6.1` | `7.0` |
| Documentation standards | Explicit `Generic.Commenting.DocComment`, `Squiz.Commenting.FunctionComment`, and `Squiz.Commenting.ClassComment` checks; no `WordPress-Docs` layer | `WordPress-Docs` plus an explicit `Squiz.Commenting.FunctionComment` configuration and `Squiz.Commenting.FunctionCommentThrowTag.WrongNumber` handling |
| Base excludes | `*/bin/*`, `*/vendor/*`, `*/node_modules/*`, `*/*.asset.php`, `*/dependencies/*` | The same base excludes |
| Additional excludes | None | `*/tests/*`, `*/index.php`, `*/languages/*.l10n.php` |

Adopting `quality-assurance/phpcs.dist.xml` raises the PHP and WordPress floors and applies the broader WordPress documentation-standards layer. Code that passes the older ruleset may therefore produce additional PHPCS findings.

## Consumer configuration

In the consumer's `.phpcs.xml` or `.phpcs.xml.dist`, replace `<rule ref="vendor/a8cteam51/team51-configs/quality-tools/phpcs.xml.dist"/>` with the `quality-assurance` path. The path remains relative to the consumer ruleset file:

```xml
<rule ref="vendor/a8cteam51/team51-configs/quality-assurance/phpcs.dist.xml"/>
```

No Composer-script rename is required. A script such as `lint:php:phpcs` continues to invoke `phpcs --standard=./.phpcs.xml ...`; the shared ruleset selection happens inside the consumer's PHPCS XML file.

Run the replacement ruleset against the consumer's full tree locally or in CI before merging the switch. This exposes findings caused by the higher floors and broader documentation checks in one focused change instead of unrelated later work.

## Version floor overrides

PHPCS treats an included ruleset's `<config>` values as fixed after they are set. A consuming ruleset's own `<config>` tag cannot override `testVersion` or `minimum_wp_version`, regardless of declaration order. Only `--runtime-set` on the `phpcs` CLI invocation takes precedence over ruleset-level configuration.

A consumer that adopts `quality-assurance/phpcs.dist.xml` while retaining lower version floors adds the overrides to its Composer lint script:

```json
"lint:php:phpcs": "phpcs --standard=./.phpcs.xml --runtime-set testVersion 8.3- --runtime-set minimum_wp_version 6.7 --basepath=. ./ -v"
```

These two overrides apply the quality-assurance ruleset's documentation and rule layers while deferring only the PHP and WordPress floor changes. See [Version floors](../CONTRIBUTING.md#version-floors) for the canonical rule.

## Legacy ruleset availability

`quality-tools/phpcs.xml.dist` is frozen, accepts no new rules, and remains available for consumers that do not migrate. This guide defines no forced cutover date.
