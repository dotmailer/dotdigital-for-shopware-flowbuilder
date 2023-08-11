import FormValidation from "src/plugin/forms/form-validation.plugin";
import intlTelInput from "@intl-tel-input";
import DomAccess from "src/helper/dom-access.helper";

export default class FormPhoneValidationPlugin extends FormValidation {

	static options = {
		...FormValidation.options,
		phoneAttr: 'data-form-validation-phone-valid',
		checkboxSelector: '#sms_subscribed'
	}

	errorMap = [
		"Invalid number",
		"Invalid country code",
		"Too short",
		"Too long",
		"Invalid number"
	]

	_registerEvents() {
		super._registerEvents();
		this.$checkBox = DomAccess.querySelector(document, this.options.checkboxSelector);
		this._registerValidationListener(this.options.phoneAttr, this._onValidatePhone.bind(this), ['change']);
	}

	_onValidatePhone(event) {
		const field = event.target;
		const element = intlTelInput(event.target);
		if (field.value.trim() && !element.isValidNumber() && this.$checkBox.checked) {
			field.setAttribute(
				'data-form-validation-phone-valid-message',
				this.errorMap[element.getValidationError()]
			);
			this._setFieldToInvalid(field, this.options.phoneAttr);
		} else {
		 	this._setFieldToValid(field, this.options.phoneAttr);
		 }

		this.$emitter.publish('onValidatePhone');
	}
}
