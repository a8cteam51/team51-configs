# Legacy PHPCS ruleset

`phpcs.xml.dist` is the frozen pre-modernization WPCS v3 ruleset for PHP `8.1-` and WordPress `6.1`; no new rules land here. Many external consumers reference it directly, so removing it would break those consumers. [`quality-assurance/phpcs.dist.xml`](../quality-assurance/phpcs.dist.xml) is the ruleset for new and modernized consumers; see the [migration guide](../docs/migrating-to-quality-assurance.md).
