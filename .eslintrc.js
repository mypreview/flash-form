module.exports = {
	root: true,
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	rules: {
		'@wordpress/no-unsafe-wp-apis': 'off',
		'import/no-unresolved': 'off',
		'jsdoc/check-line-alignment': 'off',
		'no-nested-ternary': 'off',
	},
};
