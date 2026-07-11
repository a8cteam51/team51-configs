/**
 * Spread this base into the consumer root config; `extends` drops `ignoreFiles`.
 */

module.exports = {
	extends: ['@wordpress/stylelint-config/scss'],
	ignoreFiles: [
		'assets/**',
		'build/**',
		'vendor/**',
		'node_modules/**',
		'**/*.min.css',
	],
};
