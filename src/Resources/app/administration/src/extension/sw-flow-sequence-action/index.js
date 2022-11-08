import { ACTION, CONTACT_ACTION } from '../../constant/dotdigital-flow.constant';

const { Component } = Shopware;

Component.override('sw-flow-sequence-action', {
    computed: {

        groups() {
            this.actionGroups.unshift('Dotdigital');
            return this.$super('groups');
        },


        modalName() {
            if (this.selectedAction === ACTION.CREATE_DOTDIGITAL_EMAIL_SENDER) {
                return 'dotdigital-flow-modal';
            }

            switch (this.selectedAction) {
                case ACTION.HANDLE:
                    return 'dotdigital-flow-modal';
                case CONTACT_ACTION.HANDLE:
                    return CONTACT_ACTION.COMPONENT_NAME;
                default:
                    return this.$super('modalName');
            }
        },

        actionDescription() {
            const actionDescriptionList = this.$super('actionDescription');

            return {
                ...actionDescriptionList,
                [ACTION.HANDLE]: (config) => this.getDotdigitalEmailSenderDescription(config),
                [CONTACT_ACTION.HANDLE]: (config) => this.getDotdigitalContactDescription(config),
            };
        },
    },

    methods: {

        getDotdigitalContactDescription(config) {
            let description = '';

            const recipientRaw = config.recipient;
            const recipientType = recipientRaw.type.charAt(0).toUpperCase() + recipientRaw.type.slice(1);
            const contactDescription = this.$tc('sw-flow.actions.contact.sequence-description.contact',
                recipientRaw.data.length, {
                    recipient: `<strong>${recipientType}</strong>`,
                });

            if (recipientType) {
                description += `${contactDescription}`;
            }

            const addressBookId = (config.addressBook) ? config.addressBook : 'Not specified';
            const addressBookDescription = this.$tc('sw-flow.actions.contact.sequence-description.address-book', 0, {
                addressBook: `<strong>${addressBookId}</strong>`,
            });

            if (addressBookId) {
                description += `<br>${addressBookDescription}`;
            }

            const optIn = (config.contactOptIn) ? 'Yes' : 'No' || null;
            const optInDescription = this.$tc('sw-flow.actions.contact.sequence-description.opt-in', 0, {
                optIn: `<strong>${optIn}</strong>`,
            });

            if (optIn) {
                description += `<br>${optInDescription}`;
            }

            const resubscribe = (config.resubscribe) ? 'Yes' : 'No' || null;
            const resubscribeDescription = this.$tc('sw-flow.actions.contact.sequence-description.resubscribe', 0, {
                resubscribe: `<strong>${resubscribe}</strong>`,
            });

            if (resubscribe) {
                description += `<br>${resubscribeDescription}`;
            }

            const hasDataFields = (config.dataFields.length > 0) ? 'Yes' : 'No' || null;
            const dataFieldsDescription = this.$tc('sw-flow.actions.contact.sequence-description.data-fields', 0, {
                hasDataFields: `<strong>${hasDataFields}</strong>`,
            });

            if (hasDataFields) {
                description += `<br>${dataFieldsDescription}`;
            }

            return description;
        },

        getDotdigitalEmailSenderDescription(config) {
            const recipient = config.recipient.type;
            const campaignId = config.campaignId;

            return this.$tc(`
                Recipient: ${recipient.charAt(0).toUpperCase() + recipient.slice(1)}, Campaign ID: ${campaignId}
            `);
        },

        getActionTitle(actionName) {
            switch (actionName) {
                case ACTION.HANDLE:
                    return {
                        group: 'Dotdigital',
                        value: actionName,
                        icon: 'regular-envelope',
                        label: this.$tc(ACTION.LABEL),
                    };
                case CONTACT_ACTION.HANDLE:
                    return {
                        group: 'Dotdigital',
                        value: actionName,
                        icon: CONTACT_ACTION.ICON,
                        label: this.$tc(CONTACT_ACTION.LABEL),
                    };
                default:
                    return this.$super('getActionTitle', actionName);
            }
        },
    },
});
