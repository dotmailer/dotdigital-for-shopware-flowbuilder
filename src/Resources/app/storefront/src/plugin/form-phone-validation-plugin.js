import FormValidation from "src/plugin/forms/form-validation.plugin";
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

	_getIntlField(field) {
		try {
			return window.intlTelInputGlobals.getInstance(field);
		} catch (e) {
			console.warn(e)
		}
	}

	_onValidatePhone(event) {
		const intlField = this._getIntlField(event.target);
		const field = intlField.telInput;
		const value = field.value.trim();

		if(value){
			intlField.setNumber(value);
		}

		if (value && !intlField.isValidNumber() && this.$checkBox.checked) {
			field.setAttribute(
				'value',
				intlField.getNumber()
			)
			field.setAttribute(
				'data-form-validation-phone-valid-message',
				this.errorMap[intlField.getValidationError()]
			);
			this._setFieldToInvalid(field, this.options.phoneAttr);

		} else {
		 	this._setFieldToValid(field, this.options.phoneAttr);
		 }

		this.$emitter.publish('onValidatePhone');
	}
}
