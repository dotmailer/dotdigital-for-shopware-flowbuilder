import { ACTION, CONTACT_ACTION, PROGRAM_ACTION } from '../../constant/dotdigital-flow.constant';

const { Component } = Shopware;

Component.override('sw-flow-sequence-action', {
    computed: {

        groups() {
            this.actionGroups.unshift('Dotdigital');
            return this.$super('groups');
        },

        modalName() {
            switch (this.selectedAction) {
                case ACTION.HANDLE:
                    return ACTION.COMPONENT_NAME;
                case CONTACT_ACTION.HANDLE:
                    return CONTACT_ACTION.COMPONENT_NAME;
                case PROGRAM_ACTION.HANDLE:
                    return PROGRAM_ACTION.COMPONENT_NAME;
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
                [PROGRAM_ACTION.HANDLE]: (config) => this.getDotdigitalProgramDescription(config),
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

        getDotdigitalProgramDescription(config) {
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

            const programId = (config.programId) ? config.programId : 'Not specified';
            const programDescription = this.$tc('sw-flow.actions.program.sequence-description.program', 0, {
                programId: `<strong>${programId}</strong>`,
            });

            if (programId) {
                description += `<br>${programDescription}`;
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
                        icon: ACTION.ICON,
                        label: this.$tc(ACTION.LABEL),
                    };
                case CONTACT_ACTION.HANDLE:
                    return {
                        group: 'Dotdigital',
                        value: actionName,
                        icon: CONTACT_ACTION.ICON,
                        label: this.$tc(CONTACT_ACTION.LABEL),
                    };
                case PROGRAM_ACTION.HANDLE:
                    return {
                        group: 'Dotdigital',
                        value: actionName,
                        icon: PROGRAM_ACTION.ICON,
                        label: this.$tc(PROGRAM_ACTION.LABEL),
                    };
                default:
                    return this.$super('getActionTitle', actionName);
            }
        },
    },
});
