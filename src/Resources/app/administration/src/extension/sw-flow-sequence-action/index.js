import { ACTION,CONTACT_ACTION } from '../../constant/dotdigital-flow.constant';

const { Component } = Shopware;

Component.override('sw-flow-sequence-action', {
    computed: {
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
            return this.$tc(`
                Contact action
            `);
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
                        value: actionName,
                        icon: 'regular-envelope',
                        label: this.$tc(ACTION.LABEL),
                    };
                case CONTACT_ACTION.HANDLE:
                    return {
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
