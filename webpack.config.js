const { resolve } = require( 'path' );
const defaultConfig = require( './node_modules/@wordpress/scripts/config/webpack.config.js' );
const WebpackRTLPlugin = require( 'webpack-rtl-plugin' );
const WebpackNotifierPlugin = require( 'webpack-notifier' );
const LicenseCheckerWebpackPlugin = require( 'license-checker-webpack-plugin' );
const getWebpackEntryPoints = defaultConfig.entry();

module.exports = {
	...defaultConfig,
	entry: {
		...getWebpackEntryPoints,
		script: resolve( process.cwd(), process.env.WP_SRC_DIRECTORY, 'script.js' ),
	},
	plugins: [
		...defaultConfig.plugins,
		new WebpackRTLPlugin( {
			filename: '[name]-rtl.css',
		} ),
		new LicenseCheckerWebpackPlugin( {
			outputFilename: '../credits.txt',
		} ),
		new WebpackNotifierPlugin( {
			title: 'Flash Form',
			emoji: true,
			alwaysNotify: true,
			skipFirstNotification: true,
		} ),
	],
};
