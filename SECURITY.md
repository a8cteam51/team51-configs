# Security

## Reporting a vulnerability

Use GitHub private vulnerability reporting for `a8cteam51/team51-configs`: open the repository's **Security** tab and select **Report a vulnerability**. This is the documented reporting channel.

## Consumption contract

The Team51 plugin fleet consumes this Composer package at `dev-trunk`. A merge to `trunk` is therefore treated as a tagged release; there is no staging branch between merge and consumption. Every merge must preserve backwards compatibility: changes must be additive or strictly permissive, such as loosening a rule, adding an optional input with a default, or adding a file. A merge must not introduce a breaking narrowing, such as removing or renaming an input or tightening a rule so that an existing consumer cannot pass without changes.

Minimum PHP and WordPress version floors enforced by the PHPCS ruleset, including `testVersion` and `minimum_wp_version` in `quality-assurance/phpcs.dist.xml`, are the one sanctioned exception. PHPCS treats an included ruleset's `<config>` values as fixed once set; a consuming ruleset's own `<config>` tag cannot override them regardless of declaration order. A consumer that is not ready for a raised floor can override either value by passing `--runtime-set` to the `phpcs` CLI invocation, such as in its Composer lint script.

See [CONTRIBUTING.md](CONTRIBUTING.md) for the full backwards-compatibility contract and override example.
