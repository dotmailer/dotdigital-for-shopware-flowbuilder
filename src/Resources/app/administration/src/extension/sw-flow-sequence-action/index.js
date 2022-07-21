import { ACTION } from '../../constant/dotdigital-flow.constant';

const { Component } = Shopware;

Component.override('sw-flow-sequence-action', {
    computed: {
        modalName() {
            if (this.selectedAction === ACTION.CREATE_DOTDIGITAL_EMAIL_SENDER) {
                return 'dotdigital-flow-modal';
            }

            return this.$super('modalName');
        },

        actionDescription() {
            const actionDescriptionList = this.$super('actionDescription');

            return {
                ...actionDescriptionList,
                [ACTION.CREATE_DOTDIGITAL_EMAIL_SENDER]: (config) => this.getDotdigitalEmailSenderDescription(config),
            };
        },
    },

    methods: {
        getDotdigitalEmailSenderDescription(config) {
            const recipient = config.recipient;
            const campaignId = config.campaignId;

            return this.$tc(`Recipient: ${recipient}, Campaign ID: ${campaignId}`);
        },

        getActionTitle(actionName) {
            if (actionName === ACTION.CREATE_DOTDIGITAL_EMAIL_SENDER) {
                return {
                    value: actionName,
                    icon: 'default-badge-help',
                    label: this.$tc(ACTION.LABEL),
                };
            }

            return this.$super('getActionTitle', actionName);
        },
    },
});
