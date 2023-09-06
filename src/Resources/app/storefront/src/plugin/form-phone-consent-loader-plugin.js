import Plugin from "src/plugin-system/plugin.class";
import DomAccess from "src/helper/dom-access.helper";
import intlTelInput from "@intl-tel-input";
import HttpClient from "src/service/http-client.service";
import ElementLoadingIndicatorUtil from "src/utility/loading-indicator/element-loading-indicator.util";
export default class FormPhoneConsentLoaderPlugin extends Plugin {
	static options = {
		...Plugin.options,
		reloadWindow: false,
		autoSubmit: false,
		phoneNumber: '',
		checked: false,
		loaderWrapper: `[data-consent-container]`,
		consentContainer: '[data-consent-container]',
		checkboxIdentifier: '[data-consent-checkbox]',
		phoneIdentifier: '[data-form-validation-phone-valid]',
		phoneUtilityScript: "/bundles/dotdigitalflow/static/js/intl-tel-input/utils.js",
		alwaysShowInput: false,
		showFormLoader:false

	};

	init() {
		this.$phoneFormInput = DomAccess.querySelector(this.el, this.options.phoneIdentifier);
		this.$phoneInput = DomAccess.querySelector(this.el, this.options.phoneIdentifier);
		this.$checkbox = DomAccess.querySelector(this.el, this.options.checkboxIdentifier);
		this.$consent = DomAccess.querySelector(this.el, this.options.consentContainer);
		this.$wrapper = DomAccess.querySelector(document, this.options.loaderWrapper);
		this.$phoneFormParent = this.$phoneFormInput.parentNode;
		this._client = new HttpClient();
		this._registerDependencies(this.$phoneFormInput);
		this._registerEvents();
	}

	_registerEvents() {
		if(this.options.autoSubmit){
			this.el.$emitter.subscribe('change', this._validateForm.bind(this))
		}
		this.$emitter.subscribe('beforeSubmit', this._beforeSubmit.bind(this));
		this.$emitter.subscribe('onAfterAjaxSubmit', this._afterSubmit.bind(this));
	}

	_afterSubmit(event) {
		if (this.options.reloadWindow) {
			window.location.reload()
		}

		if (!this.options.reloadWindow) {
			ElementLoadingIndicatorUtil.remove(this.el);
		}
	}

	_beforeSubmit(event) {
		if(this.el.checkValidity() && this.options.showFormLoader){
			ElementLoadingIndicatorUtil.create(this.el);
		}
	}

	_validateForm(event) {
		this.el.checkValidity();
	}

	_registerDependencies() {
		ElementLoadingIndicatorUtil.create(this.$wrapper);
		this._fetchCountryContext()
			.then((response) => {
				const context = JSON.parse(response);
				this.$phoneFormInput = intlTelInput(this.$phoneFormInput,{
					separateDialCode: false,
					autoInsertDialCode: true,
					formatOnDisplay: false,
					utilsScript: this.options.phoneUtilityScript,
					onlyCountries: context.allowed,
					preferredCountries: context.default,
				});
			})
			.catch( error => console.warn )
			.finally(() => {

				this.$phoneFormInput.setNumber(this.options.phoneNumber)
				this.$phoneFormInput.telInput.value = this.options.phoneNumber
				this.$phoneInput.value = this.options.phoneNumber
                this.$checkbox.checked = this.options.checked;

				if(this.options.alwaysShowInput) {
					this.$consent.classList.remove('d-none');
					this.$phoneInput.required = true;
				}

				if(this.options.checked) {
					this.$checkbox.checked = true;
					this.$consent.classList.remove('d-none');
					this.$phoneInput.setAttribute('required', true);
				}

				if(!this.options.checked){
					this.$phoneInput.removeAttribute('required');
					this.$phoneInput.removeAttribute('data-form-validation-phone-valid');
				}

				if(!this.options.checked) {
					this.$phoneInput.dispatchEvent(new Event('change'));
				}

				ElementLoadingIndicatorUtil.remove(this.$wrapper)
			});
	}

	_fetchCountryContext() {
		return new Promise((resolve, reject) => {
			this._client.get('/context', (response) => resolve(response));
		});
	}
}
