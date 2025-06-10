import { ref, computed, nextTick, watch } from 'vue';
import template from './contact-selector.html.twig';
import './contact-selector.scss';

const { Component, Utils, Mixin, Classes: { ShopwareError } } = Shopware;

Component.register('dotdigital-recipient-selector', {
    name: 'dotdigital-recipient-selector',
    template,
    mixins: [Mixin.getByName('notification')],
    props: {
        limit: {
            type: Number,
            required: false,
            default: () => Number.MAX_SAFE_INTEGER,
        },
        aware: {
            type: Array,
            required: false,
            default() {
                return [];
            },
        },
        exclude: {
            type: Array,
            required: false,
            default() {
                return [];
            },
        },
        recipient: {
            type: Object,
            required: false,
        },
    },

    emits: ['selected-recipient'],

    setup(props, { emit }) {
        const triggerEvent = computed(() => {
            try {
                return Shopware.Store.get('swFlow')?.triggerEvent;
            } catch (e) {
                console.error('Error accessing flow state:', e);
                return undefined;
            }
        });

        // Shopware's translation service
        const $tc = (key, ...args) => Shopware.Snippet.tc(key, ...args);

        // Reactive state
        const showRecipientEmails = ref(false);
        const mailRecipient = ref(null);
        const selectedRecipient = ref(null);
        const recipientGridError = ref(null);
        const recipients = ref([]);
        const recipientsGrid = ref(null);

        // Constants
        const entityAwareness = [
            'CustomerAware',
            'UserAware',
            'OrderAware',
            'CustomerGroupAware',
        ];

        // Helper functions that need to be defined early
        const setMailError = (mail) => {
            let error = null;

            if (!mail) {
                error = new ShopwareError({
                    code: 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
                });
            }

            return error;
        };

        const isRecipientExcluded = (recipientType) => {
            return props.exclude.includes(
                recipientType[0].value,
            );
        };

        // Computed properties
        const isLimitReached = computed(() => props.limit === recipients.value.length);

        const entityAware = computed(() => [...props.aware, ...entityAwareness]);

        const mailRecipientDescription = computed(() => {
            let description = '';
            switch (mailRecipient.value) {
                case 'custom':
                    description += $tc('sw-flow.shared.recipient-selector.fields.recipients.description');
                    description +=
                        ` <a href="https://support.dotdigital.com/hc/en-gb/articles/7101774577298" target="_blank">
                                ${$tc('sw-flow.shared.recipient-selector.fields.recipients.help-link')}
                          </a>
                        `;
                    break;
                default:
                    description += '';
                    break;
            }
            return description;
        });

        const isNew = computed(() => !props.recipient?.type);

        const recipientCustomer = computed(() => [
            {
                value: 'default',
                label: $tc('sw-flow.modals.mail.labelCustomer'),
            },
        ]);

        const recipientAdmin = computed(() => [
            {
                value: 'admin',
                label: $tc('sw-flow.modals.mail.labelAdmin'),
            },
        ]);

        const recipientCustom = computed(() => [
            {
                value: 'custom',
                label: $tc('sw-flow.shared.recipient-selector.fields.recipients.options.custom'),
            },
        ]);

        const recipientDefault = computed(() => [
            {
                value: 'default',
                label: $tc('sw-flow.modals.mail.labelDefault'),
            },
        ]);

        const recipientContactFormMail = computed(() => [
            {
                value: 'contactFormMail',
                label: $tc('sw-flow.modals.mail.labelContactFormMail'),
            },
        ]);

        const recipientFromNewsLetterForm = computed(() => [
            {
                value: 'default',
                label: $tc('sw-flow.shared.recipient-selector.fields.recipients.options.newsletter'),
            },
        ]);

        const recipientOptions = computed(() => {
            const triggerEventData = triggerEvent.value;
            const allowedAwareOrigin = triggerEventData?.aware ?? [];
            const allowAwareConverted = [];

            allowedAwareOrigin.forEach(aware => {
                allowAwareConverted.push(aware.slice(aware.lastIndexOf('\\') + 1));
            });

            if (allowAwareConverted.length === 0) {
                return recipientCustom.value;
            }

            if (triggerEventData?.name === 'contact_form.send') {
                return [
                    ...isRecipientExcluded(recipientContactFormMail.value) ? [] : recipientContactFormMail.value,
                    ...isRecipientExcluded(recipientAdmin.value) ? [] : recipientAdmin.value,
                    ...isRecipientExcluded(recipientCustom.value) ? [] : recipientCustom.value,
                ];
            }

            if (triggerEventData?.name === 'newsletter.confirm') {
                return [
                    ...isRecipientExcluded(recipientFromNewsLetterForm.value) ? [] : recipientFromNewsLetterForm.value,
                    ...isRecipientExcluded(recipientAdmin.value) ? [] : recipientAdmin.value,
                    ...isRecipientExcluded(recipientCustom.value) ? [] : recipientCustom.value,
                ];
            }

            if (
                triggerEventData?.name === 'newsletter.confirm' ||
                triggerEventData?.name === 'newsletter.register'
            ) {
                return [
                    ...isRecipientExcluded(recipientFromNewsLetterForm.value) ? [] : recipientFromNewsLetterForm.value,
                    ...isRecipientExcluded(recipientAdmin.value) ? [] : recipientAdmin.value,
                    ...isRecipientExcluded(recipientCustom.value) ? [] : recipientCustom.value,
                ];
            }

            const hasEntityAware = allowAwareConverted.some(allowedAware => entityAware.value.includes(allowedAware));

            if (hasEntityAware) {
                return [
                    ...isRecipientExcluded(recipientCustomer.value) ? [] : recipientCustomer.value,
                    ...isRecipientExcluded(recipientAdmin.value) ? [] : recipientAdmin.value,
                    ...isRecipientExcluded(recipientCustom.value) ? [] : recipientCustom.value,
                ];
            }

            return [
                ...isRecipientExcluded(recipientAdmin.value) ? [] : recipientAdmin.value,
                ...isRecipientExcluded(recipientCustom.value) ? [] : recipientCustom.value,
            ];
        });

        const recipientColumns = computed(() => [{
            property: 'email',
            label: $tc('sw-flow.shared.recipient-selector.grid.columns.email.header'),
            inlineEdit: 'string',
        }]);

        // Methods
        const getRecipientData = () => {
            const recipientData = {};
            if (mailRecipient.value !== 'custom') {
                return recipientData;
            }

            recipients.value.forEach(recipient => {
                if (!recipient.email) {
                    return;
                }

                Object.assign(recipientData, {
                    [recipient.id]: recipient.email,
                });
            });
            return recipientData;
        };

        const validateRecipient = (item, itemIndex) => {
            const errorMail = setMailError(item.email);

            recipients.value[itemIndex] = {
                ...item,
                errorMail,
            };

            return errorMail;
        };

        const isRecipientGridError = () => {
            if (mailRecipient.value !== 'custom') {
                return false;
            }

            const invalidItemIndex = recipients.value.filter(item => !item.isNew)
                .findIndex(recipient => !recipient.email);

            if (invalidItemIndex >= 0) {
                validateRecipient(recipients.value[invalidItemIndex], invalidItemIndex);
            }

            return invalidItemIndex >= 0;
        };

        const emitRecipient = () => {
            recipientGridError.value = isRecipientGridError();
            if (recipientGridError.value) {
                return;
            }
            emit('selected-recipient', {
                payload: {
                    type: mailRecipient.value,
                    data: getRecipientData(),
                },
            });
        };

        const addRecipient = () => {
            if (isLimitReached.value) return;
            const newId = Utils.createId();

            recipients.value.push({
                id: newId,
                email: '',
                isNew: true,
            });

            nextTick(() => {
                if (recipientsGrid.value) {
                    recipientsGrid.value.currentInlineEditId = newId;
                    recipientsGrid.value.enableInlineEdit();
                }
            });
        };

        const resetError = () => {
            recipientGridError.value = null;
            recipients.value.forEach(item => {
                item.errorMail = null;
            });
        };

        const fieldError = (text) => {
            if (!text) {
                return new ShopwareError({
                    code: 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
                });
            }

            return null;
        };
        const onChangeRecipient = (recipient) => {
            mailRecipient.value = recipient;

            if (recipient === 'custom') {
                showRecipientEmails.value = true;
                // Only add a recipient if there are none
                if (recipients.value.length === 0) {
                    addRecipient();
                }
            } else {
                showRecipientEmails.value = false;
            }

            nextTick(() => {
                emitRecipient();
            });
        };

        const saveRecipient = (recipient) => {
            const index = recipients.value.findIndex((item) => {
                return item.id === recipient.id;
            });

            if (validateRecipient(recipient, index)) {
                nextTick(() => {
                    if (recipientsGrid.value) {
                        recipientsGrid.value.currentInlineEditId = recipient.id;
                        recipientsGrid.value.enableInlineEdit();
                    }
                });
                return;
            }

            if (recipient.isNew) {
                addRecipient();
                recipients.value[index].isNew = false;
            }

            resetError();
            emitRecipient();
        };

        const cancelSaveRecipient = (recipient) => {
            if (!recipient.isNew) {
                const index = recipients.value.findIndex((item) => {
                    return item.id === selectedRecipient.value.id;
                });

                // Reset data when saving is cancelled
                recipients.value[index] = selectedRecipient.value;
            } else {
                recipient.email = '';
            }

            resetError();
            emitRecipient();
        };

        const onEditRecipient = (item) => {
            const index = recipients.value.findIndex((recipient) => {
                return item.id === recipient.id;
            });

            // Recheck error in current item
            if (!item.email) {
                recipients.value[index] = { ...item, errorMail: null };
            } else {
                validateRecipient(item, index);
            }

            if (recipientsGrid.value) {
                recipientsGrid.value.currentInlineEditId = item.id;
                recipientsGrid.value.enableInlineEdit();
            }
            selectedRecipient.value = { ...item };
            emitRecipient();
        };

        const onDeleteRecipient = (itemIndex) => {
            recipients.value.splice(itemIndex, 1);
            emitRecipient();
        };

        const allowDeleteRecipient = (itemIndex) => {
            return itemIndex !== recipients.value.length - 1;
        };

        watch(() => props.recipient, (newRecipient) => {
            // Skip if recipient is null (happens during modal transitions)
            if (!newRecipient) return;

            if (newRecipient.type) {
                mailRecipient.value = newRecipient.type;

                if (newRecipient.type === 'custom') {
                    if (recipients.value.length === 0 ||
                        (recipients.value.length === 1 && recipients.value[0].isNew && !recipients.value[0].email)) {
                        recipients.value = [];

                        // Add existing recipients from saved data
                        if (newRecipient.data && Object.values(newRecipient.data).length > 0) {
                            Object.values(newRecipient.data).forEach(value => {
                                recipients.value.push({
                                    id: Utils.createId(),
                                    email: value,
                                    isNew: false,
                                });
                            });
                        }

                        addRecipient();
                    }

                    showRecipientEmails.value = true;
                } else {
                    showRecipientEmails.value = false;
                }
            } else if (recipientOptions.value.length > 0) {
                mailRecipient.value = recipientOptions.value[0].value;
            }
        }, { immediate: true });

        return {
            $tc,
            showRecipientEmails,
            mailRecipient,
            selectedRecipient,
            recipientGridError,
            recipients,
            recipientsGrid,
            isLimitReached,
            entityAware,
            mailRecipientDescription,
            isNew,
            recipientCustomer,
            recipientAdmin,
            recipientCustom,
            recipientDefault,
            recipientContactFormMail,
            recipientFromNewsLetterForm,
            recipientOptions,
            recipientColumns,
            getRecipientData,
            isRecipientGridError,
            emitRecipient,
            onChangeRecipient,
            saveRecipient,
            cancelSaveRecipient,
            onEditRecipient,
            onDeleteRecipient,
            fieldError,
            setMailError,
            validateRecipient,
            resetError,
            isRecipientExcluded,
            allowDeleteRecipient,
            addRecipient,
        };
    },
});
