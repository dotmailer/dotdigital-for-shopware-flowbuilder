const {
	join,
	resolve
} = require('path');

module.exports = () => {
	return {
		resolve: {
			alias: {
				'@intl-tel-input': resolve(
					join(__dirname, '..', 'node_modules', 'intl-tel-input')
				)
			},
		},
	};
}
