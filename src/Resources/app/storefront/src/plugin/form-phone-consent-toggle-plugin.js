import Plugin from "src/plugin-system/plugin.class";
import DomAccess from "src/helper/dom-access.helper";

export default class FormPhoneConsentTogglePlugin extends Plugin {

	static options = {
		...Plugin.options,
		consentContainer: '[data-form-phone-consent]',
		checkboxIdentifier: '[data-consent-checkbox]',
		containerIdentifier: '[data-consent-container]',
		phoneInputAttr: '[data-form-validation-phone-valid="true"]',
	};

	init() {
		this.$phoneField = DomAccess.querySelector(this.el, this.options.phoneInputAttr);
		this.$consentCheckobox =  DomAccess.querySelector(this.el, this.options.checkboxIdentifier);
		this.$consent =   DomAccess.querySelector(this.el, this.options.containerIdentifier);
		this._registerEvents();
	}

	_registerEvents() {
		this.$consentCheckobox.addEventListener('change', this._onConsentCheckboxChange.bind(this));
	}
	_onConsentCheckboxChange() {
		if (this.$consentCheckobox.checked) {
			this.$phoneField.required = true;
			this.$consent.classList.remove('d-none');
			this.$phoneField.setAttribute('data-form-validation-phone-valid', true);
			this.$phoneField.dispatchEvent(new Event('change'));
		} else {
			this.$phoneField.required = false;
			this.$consent.classList.add('d-none');
			this.$phoneField.setAttribute('data-form-validation-phone-valid', false);
			this.$phoneField.dispatchEvent(new Event('change'));
		}
	}

}
