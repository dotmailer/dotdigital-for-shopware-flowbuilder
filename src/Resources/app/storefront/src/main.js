import FormPhoneValidationPlugin from "./plugin/form-phone-validation-plugin";
import FormPhoneConsentLoaderPlugin from "./plugin/form-phone-consent-loader-plugin";
import FormPhoneConsentTogglePlugin from "./plugin/form-phone-consent-toggle-plugin";
// Register your plugin via the existing PluginManager

const PluginManager = window.PluginManager;
PluginManager.register('FormPhoneConsentLoader', FormPhoneConsentLoaderPlugin, '.register-form');
PluginManager.register('FormPhoneConsentLoader', FormPhoneConsentLoaderPlugin, '.sms-consent-capture');
PluginManager.register('FormPhoneConsentHandlerPlugin',FormPhoneConsentTogglePlugin,'[data-form-phone-consent]');
PluginManager.override('FormValidation', FormPhoneValidationPlugin, '[data-form-validation]');
