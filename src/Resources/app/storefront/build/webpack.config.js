const {
	join,
	resolve
} = require('path');
const CopyPlugin = require("copy-webpack-plugin");

module.exports = () => {
	return {
		resolve: {
			alias: {
				'@intl-tel-input': resolve(
					join(__dirname, '..', 'node_modules', 'intl-tel-input')
				)
			},
		},
		plugins: [
			new CopyPlugin({
				patterns: [
					{
						from: join(__dirname, '..', 'node_modules', 'intl-tel-input/build/js/utils.js'),
						to: join(__dirname, '../../..', 'public/static/js', "intl-tel-input")
					},
				],
			}),
		],
	};
}
