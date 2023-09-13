import { CAMPAIGN_ACTION, CONTACT_ACTION, PROGRAM_ACTION } from '../../constant/dotdigital-flow.constant';

const { Component } = Shopware;

Component.override('sw-flow-sequence-action', {
    computed: {

        groups() {
            this.actionGroups.unshift('Dotdigital');
            return this.$super('groups');
        },

        modalName() {
            switch (this.selectedAction) {
                case CAMPAIGN_ACTION.HANDLE:
                    return CAMPAIGN_ACTION.COMPONENT_NAME;
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
                [CAMPAIGN_ACTION.HANDLE]: (config) => this.getDotdigitalEmailSenderDescription(config),
                [CONTACT_ACTION.HANDLE]: (config) => this.getDotdigitalContactDescription(config),
                [PROGRAM_ACTION.HANDLE]: (config) => this.getDotdigitalProgramDescription(config),
            };
        },
    },

    methods: {

        getActionDescriptions(sequence) {
            const actionDescriptionList = this.$super('getActionDescriptions', sequence);
            switch (sequence.actionName) {
                case CAMPAIGN_ACTION.HANDLE:
                    return this.getDotdigitalEmailSenderDescription(sequence.config);
                case CONTACT_ACTION.HANDLE:
                    return this.getDotdigitalContactDescription(sequence.config);
                case PROGRAM_ACTION.HANDLE:
                    return this.getDotdigitalProgramDescription(sequence.config);
                default:
                    return actionDescriptionList;
            }
        },

        getDotdigitalContactDescription(config) {
            let description = '';

            const recipientRaw = config.recipient;
            const recipientType = recipientRaw.type.charAt(0).toUpperCase() + recipientRaw.type.slice(1);
            const contactDescription = this.$tc(
                'sw-flow.actions.contact.sequence-description.contact',
                recipientRaw.data.length,
                {
                    recipient: `<strong>${recipientType}</strong>`,
                },
            );

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
            const contactDescription = this.$tc(
                'sw-flow.actions.contact.sequence-description.contact',
                recipientRaw.data.length,
                {
                    recipient: `<strong>${recipientType}</strong>`,
                },
            );

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
            const recipientRaw = config.recipient;
            const recipientType = recipientRaw.type.charAt(0).toUpperCase() + recipientRaw.type.slice(1);
            const recipientDescription = this.$tc('sw-flow.actions.campaign.sequence-description.recipient', 0, {
                recipient: `<strong>${recipientType}</strong>`,
            });

            const campaignId = config.campaignId;
            const campaignDescription = this.$tc('sw-flow.actions.campaign.sequence-description.campaign', 0, {
                campaignId: `<strong>${campaignId}</strong>`,
            });

            return `
                ${recipientDescription}<br>
                ${campaignDescription}
            `;
        },

        getActionTitle(actionName) {
            switch (actionName) {
                case CAMPAIGN_ACTION.HANDLE:
                    return {
                        group: 'Dotdigital',
                        value: actionName,
                        icon: CAMPAIGN_ACTION.ICON,
                        label: this.$tc(CAMPAIGN_ACTION.LABEL),
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
