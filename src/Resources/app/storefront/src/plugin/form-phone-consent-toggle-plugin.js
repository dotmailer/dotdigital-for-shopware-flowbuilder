import Plugin from "src/plugin-system/plugin.class";
import DomAccess from "src/helper/dom-access.helper";

export default class FormPhoneConsentTogglePlugin extends Plugin {

	static options = {
		...Plugin.options,
		consentContainer: '[data-form-phone-consent]',
		checkboxIdentifier: '[data-consent-checkbox]',
		containerIdentifier: '[data-consent-container]',
		phoneInputAttr: '[data-form-validation-phone-valid]'
	};

	init() {
		this.$phoneField = DomAccess.querySelector(this.el, this.options.phoneInputAttr);
		this.$consentCheckbox =  DomAccess.querySelector(this.el, this.options.checkboxIdentifier);
        this.$consentCheckbox.checked = false;
		this.$consent =   DomAccess.querySelector(this.el, this.options.containerIdentifier);
		this._registerEvents();
	}

	_registerEvents() {
		this.$consentCheckbox.addEventListener('change', this._onConsentCheckboxChange.bind(this));
	}
	_onConsentCheckboxChange(event) {
		if (this.$consentCheckbox.checked) {
			this.$consent.classList.remove('d-none');
			this.$phoneField.setAttribute('required', true);
			this.$phoneField.setAttribute('data-form-validation-phone-valid', true);
			this.$phoneField.dispatchEvent(new Event('change'));
		} else {
			this.$consent.classList.add('d-none');
			this.$phoneField.removeAttribute('required');
			this.$phoneField.removeAttribute('data-form-validation-phone-valid');
			this.$phoneField.dispatchEvent(new Event('change'));
		}
	}

}
