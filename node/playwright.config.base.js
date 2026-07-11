/**
 * Spread nested keys (`use`, `webServer`, `projects`) individually when overriding them.
 */

const wpBaseConfig = require('@wordpress/scripts/config/playwright.config.js');

module.exports = {
	...wpBaseConfig,
	testDir: 'tests/EndToEnd',
};
