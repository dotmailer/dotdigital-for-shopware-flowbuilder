window.PluginManager.register('FormPhoneConsentLoader', () => import('./plugin/form-phone-consent-loader-plugin'), '.register-form');
window.PluginManager.register('FormPhoneConsentLoaderSMS', () => import('./plugin/form-phone-consent-loader-plugin'), '.sms-consent-capture');
window.PluginManager.register('FormPhoneConsentHandlerPlugin', () => import('./plugin/form-phone-consent-toggle-plugin'), '[data-form-phone-consent]');
window.PluginManager.override('FormValidation', () => import('./plugin/form-phone-validation-plugin'), '[data-form-validation]');
window.PluginManager.override('FormHandler', () => import('./plugin/form-phone-validation-plugin'), '[data-form-handler]');
