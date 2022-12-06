import template from './dotdigital-flow-campaign-modal.html.twig';
import '../shared/scss/dd-flow-modal.scss';

const { Component } = Shopware;

Component.register('dotdigital-flow-campaign-modal', { // eslint-disable-line
    template,
    inject: ['DotdigitalApiService'],
    props: {
        sequence: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            sequenceReady: false,
            campaignList: [],
            contactEmail: null,
            campaignId: 0,
        };
    },

    computed: {

        /**
         * Get and mutate campaign list
         * @returns {*[]}
         */
        availableCampaigns() {
            return this.campaignList.map((campaign) => {
                return {
                    value: campaign.id,
                    label: `${campaign.name}`,
                };
            });
        },

        /**
         * Is this a new flow action?
         * @returns {boolean}
         */
        isNew() {
            return !this.sequence?.id;
        },

        modalTitle() {
            return this.$tc('sw-flow.actions.campaign.title');
        },

        modalSubTitle() {
            return this.$tc('sw-flow.actions.campaign.subtitle');
        },

        /**
         * Get recipient aware of the current sequence
         *
         * @returns {string[]}
         */
        entityAware() {
            return [
                'CustomerAware',
                'UserAware',
                'OrderAware',
                'CustomerGroupAware',
            ];
        },

    },

    /**
     * Called component create life cycle hook
     */
    created() {
        this.createdComponent()
            .finally(() => {
                this.sequenceReady = true;
            })
            .catch((error) => {
                console.error(error);
                this.createNotificationError({
                    title: this.$tc('Error'),
                    message: error.message,
                });
            });
    },

    methods: {

        /**
         * handle update event from campaign selection component
         * @param campaignId
         */
        handleCampaignSelection(campaignId) {
            this.campaignId = campaignId;
        },

        /**
         * handle update event from recipient component
         * @param event
         */
        handleRecipientSelection(event) {
            this.contactEmail = event.payload;
        },

        /**
         * Shopware sequence hook
         */
        async createdComponent() {
            const { config } = this.sequence;
            if (!this.isNew) {
                this.contactEmail = config.recipient;
                this.campaignId = config.campaignId;
            }
            this.campaignList = await this.DotdigitalApiService.getCampaigns();
            return this.sequence;
        },

        /**
         * Validate recipient and emit event
         */
        onAddAction() {
            const sequence = {
                ...this.sequence,
                config: {
                    ...this.sequence.config,
                    campaignId: this.campaignId,
                    recipient: this.contactEmail,
                },
            };

            this.$nextTick(() => {
                this.$emit('process-finish', sequence);
            });
        },

        /**
         * On Modal closed event hook
         */
        onClose() {
            this.$emit('modal-close');
        },


    },
});
