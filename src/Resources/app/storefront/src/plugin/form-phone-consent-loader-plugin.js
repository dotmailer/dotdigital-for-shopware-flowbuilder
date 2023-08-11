import Plugin from "src/plugin-system/plugin.class";
import DomAccess from "src/helper/dom-access.helper";
import intlTelInput from "@intl-tel-input";
import HttpClient from "src/service/http-client.service";
import ElementLoadingIndicatorUtil from "src/utility/loading-indicator/element-loading-indicator.util";
export default class FormPhoneConsentLoaderPlugin extends Plugin {
	static options = {
		...Plugin.options,
		phoneIdentifier: '[data-form-validation-phone-valid="true"]',
		phoneUtilityScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
	};

	init() {
		this.$phoneFormInput = DomAccess.querySelector(this.el, this.options.phoneIdentifier);
		this.$phoneFormParent = this.$phoneFormInput.parentNode;
		this._client = new HttpClient();
		this._registerDependencies(this.$phoneFormInput);
	}

	_registerDependencies() {
		ElementLoadingIndicatorUtil.create(this.$phoneFormParent);
		this._fetchCountryContext()
			.then((response) => {
				const context = JSON.parse(response);
				this.$phoneFormInput = intlTelInput(this.$phoneFormInput,{
					utilsScript: this.options.phoneUtilityScript,
					onlyCountries: context.allowed,
					preferredCountries: context.default,
				});
			})
			.catch( error => console.warn )
			.finally(() => ElementLoadingIndicatorUtil.remove(this.$phoneFormParent));
	}

	_fetchCountryContext() {
		return new Promise((resolve, reject) => {
			this._client.get('/context', (response) => resolve(response));
		});
	}
}
