import template from './dotdigital-flow-modal.html.twig';

const { Component, Mixin } = Shopware;

Component.register('dotdigital-flow-modal', {
    template,
    mixins: [Mixin.getByName('notification')],
    props: {
        sequence: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            recipient: '',
            campaignId: 0,
            validation_errors: [],
        };
    },
    computed: {
        /**
         * Is form Valid
         *
         * @returns {boolean}
         */
        isValid() {
            return !this.validation_errors.length > 0;
        },
    },

    /**
     * Called component create life cycle hook
     */
    created() {
        this.createdComponent();
    },

    methods: {

        /**
         * Handle Validation events
         *
         * @param event
         */
        handleValidationEvent(event) {
            if (event.passes) {
                this.validation_errors = this.validation_errors.filter((error) => {
                    return error.key !== event.key;
                });
            } else {
                this.validation_errors.push({ key: event.key, message: event.error });
            }
        },

        /**
         * Override default on save
         * action to apply validation
         */
        onSave() {
            if (!this.isValid || this.sequence?.propsAppFlowAction?.app?.active) {
                return this.createNotificationError({
                    message: this.$tc('Form has invalid input'),
                });
            }

            const sequence = {
                ...this.sequence,
                config: {
                    ...this.config,
                    recipient: this.recipient,
                    campaignId: this.campaignId,
                },
            };

            this.$emit('process-finish', sequence);
            this.onClose();
            return true;
        },

        /**
         * Shopware sequence hook
         */
        createdComponent() {
            this.recipient = this.sequence?.config?.recipient || '';
            this.campaignId = this.sequence?.config?.campaignId || 0;
        },

        /**
         * On Modal closed event hook
         */
        onClose() {
            this.$emit('modal-close');
        },
    },
});
