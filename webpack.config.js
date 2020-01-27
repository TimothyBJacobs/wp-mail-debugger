const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const autoprefixer = require( 'autoprefixer' );

module.exports = {
	...defaultConfig,
	output: {
		...defaultConfig.output,
		library: [ 'wpMailDebugger', '[name]' ],
		libraryTarget: 'this',
	},

	// We need to extend the module.rules & plugins to add the scss build process.
	module: {
		...defaultConfig.module,
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.css$/,
				use: [
					MiniCssExtractPlugin.loader,
					{
						loader: 'css-loader',
						options: {
							url: false,
						},
					},
					{
						loader: 'postcss-loader',
						options: {
							plugins: [
								autoprefixer,
							],
						},
					},
				],
			},
		],
	},

	plugins: [
		...defaultConfig.plugins,
		new MiniCssExtractPlugin( {
			filename: '[name].css',
		} ),
	],
};
