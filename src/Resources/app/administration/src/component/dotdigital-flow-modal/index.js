import template from './dotdigital-flow-modal.html.twig';
import '../shared/scss/dd-flow-modal.scss';

const { Component, Utils, Mixin, Classes: { ShopwareError } } = Shopware;
const { mapState } = Component.getComponentHelper();

Component.register('dotdigital-flow-modal', { // eslint-disable-line
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
            showRecipientEmails: false,
            mailRecipient: null,
            recipients: [],
            selectedRecipient: null,
            recipientGridError: null,
            campaignId: 0,
            validation_errors: [],
        };
    },

    computed: {

        mailRecipientDescription() {
            let description = '';
            if (this.mailRecipient === 'custom') {
                description += this.$tc(`
                    Shopware variables differ depending on the trigger you use.
                `);
                description +=
                    ` <a href="https://support.dotdigital.com/hc/en-gb/articles/7101774577298" target="_blank">
                        ${this.$tc('Learn more')}
                      </a>
                    `;
            }
            return description;
        },

        modalTitle() {
            return this.$tc('Send email with Dotdigital');
        },

        modalSubTitle() {
            return this.$tc('Automatically send your Dotdigital triggered campaign content as a transactional email. ');
        },

        /**
         * Is campaign ID valid
         *
         * @returns {boolean}
         */
        isValid() {
            return !this.validation_errors.length > 0;
        },

        /**
         * Is new recipient entity?
         *
         * @returns {boolean}
         */
        isNewMail() {
            return !this.sequence?.id;
        },

        /**
         * Get recipient for customer aware sequence
         *
         * @returns {[{label: *, value: string}]}
         */
        recipientCustomer() {
            return [
                {
                    value: 'default',
                    label: this.$tc('sw-flow.modals.mail.labelCustomer'),
                },
            ];
        },


        /**
         * Get recipient for admin aware sequence
         *
         * @returns {[{label: *, value: string}]}
         */
        recipientAdmin() {
            return [
                {
                    value: 'admin',
                    label: this.$tc('sw-flow.modals.mail.labelAdmin'),
                },
            ];
        },

        /**
         * Get recipient for custom aware sequence
         *
         * @returns {[{label: *, value: string}]}
         */
        recipientCustom() {
            return [
                {
                    value: 'custom',
                    label: this.$tc('Custom'),
                },
            ];
        },

        /**
         * Get recipient for default aware sequence
         *
         * @returns {[{label: *, value: string}]}
         */
        recipientDefault() {
            return [
                {
                    value: 'default',
                    label: this.$tc('sw-flow.modals.mail.labelDefault'),
                },
            ];
        },

        /**
         * Get recipient for contact aware form sequence
         *
         * @returns {[{label: *, value: string}]}
         */
        recipientContactFormMail() {
            return [
                {
                    value: 'contactFormMail',
                    label: this.$tc('sw-flow.modals.mail.labelContactFormMail'),
                },
            ];
        },

        /**
         * Get recipient for contact aware form sequence
         *
         * @returns {[{label: *, value: string}]}
         */
        recipientFromNewsLetterForm() {
            return [
                {
                    value: 'default',
                    label: this.$tc('Newsletter recipient'),
                },
            ];
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

        /**
         * Get recipient type options
         *
         * @returns {*[]|(function(): [{label: *, value: string}])}
         */
        recipientOptions() {
            const allowedAwareOrigin = this.triggerEvent.aware ?? [];
            const allowAwareConverted = [];
            allowedAwareOrigin.forEach(aware => {
                allowAwareConverted.push(aware.slice(aware.lastIndexOf('\\') + 1));
            });

            if (allowAwareConverted.length === 0) {
                return this.recipientCustom;
            }

            if (this.triggerEvent.name === 'contact_form.send') {
                return [
                    ...this.recipientDefault,
                    ...this.recipientContactFormMail,
                    ...this.recipientAdmin,
                    ...this.recipientCustom,
                ];
            }

            if (this.triggerEvent.name === 'newsletter.confirm') {
                return [
                    ...this.recipientFromNewsLetterForm,
                    ...this.recipientAdmin,
                    ...this.recipientCustom,
                ];
            }

            if (
                this.triggerEvent.name === 'newsletter.confirm' ||
                this.triggerEvent.name === 'newsletter.register'
            ) {
                return [
                    ...this.recipientFromNewsLetterForm,
                    ...this.recipientAdmin,
                    ...this.recipientCustom,
                ];
            }

            const hasEntityAware = allowAwareConverted.some(allowedAware => this.entityAware.includes(allowedAware));

            if (hasEntityAware) {
                return [
                    ...this.recipientCustomer,
                    ...this.recipientAdmin,
                    ...this.recipientCustom,
                ];
            }

            return [
                ...this.recipientAdmin,
                ...this.recipientCustom,
            ];
        },

        /**
         * Get recipient columns
         *
         * @returns {[{property: string, inlineEdit: string, label: *}]}
         */
        recipientColumns() {
            return [{
                property: 'email',
                label: this.$tc('Email address or Shopware variable'),
                inlineEdit: 'string',
            }];
        },

        ...mapState('swFlowState', ['triggerEvent', 'triggerActions']),

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
         * Shopware sequence hook
         */
        createdComponent() {
            this.campaignId = this.sequence?.config?.campaignId || 0;
            this.mailRecipient = this.recipientOptions[0].value;

            if (!this.isNewMail) {
                const { config } = this.sequence;

                this.mailRecipient = config.recipient?.type;

                if (config.recipient?.type === 'custom') {
                    Object.values(config.recipient.data)
                        .forEach(value => {
                            const newId = Utils.createId();
                            this.recipients.push({
                                id: newId,
                                email: value,
                                isNew: false,
                            });
                        });

                    this.addRecipient();
                    this.showRecipientEmails = true;
                }
            }
        },

        /**
         * On Modal closed event hook
         */
        onClose() {
            this.$emit('modal-close');
        },

        /**
         * Get recipient data from grid
         *
         * @returns object
         */
        getRecipientData() {
            const recipientData = {};
            if (this.mailRecipient !== 'custom') {
                return recipientData;
            }

            this.recipients.forEach(recipient => {
                if (!recipient.email) {
                    return;
                }

                Object.assign(recipientData, {
                    [recipient.id]: recipient.email,
                });
            });
            return recipientData;
        },

        /**
         * Get recipient data from grid
         *
         * @returns {boolean}
         */
        isRecipientGridError() {
            if (this.mailRecipient !== 'custom') {
                return false;
            }

            if (this.recipients.length === 1 && !this.recipients[0].email) {
                this.validateRecipient(this.recipients[0], 0);
                return true;
            }

            const invalidItemIndex = this.recipients.filter(item => !item.isNew)
                .findIndex(recipient => !recipient.email);

            if (invalidItemIndex >= 0) {
                this.validateRecipient(this.recipients[invalidItemIndex], invalidItemIndex);
            }

            return invalidItemIndex >= 0;
        },

        /**
         * Validate recipient and emit event
         */
        onAddAction() {
            this.recipientGridError = this.isRecipientGridError();
            if (this.recipientGridError || !this.isValid) {
                return;
            }

            this.resetError();

            const sequence = {
                ...this.sequence,
                config: {

                    campaignId: this.campaignId,
                    recipient: {
                        type: this.mailRecipient,
                        data: this.getRecipientData(),
                    },
                },
            };

            this.$nextTick(() => {
                this.$emit('process-finish', sequence);
            });
        },

        /**
         * Handle change event of recipient type
         *
         * @param recipient
         */
        onChangeRecipient(recipient) {
            if (recipient === 'custom') {
                this.showRecipientEmails = true;
                this.addRecipient();
            } else {
                this.showRecipientEmails = false;
            }
        },

        /**
         * Add new recipient to grid
         */
        addRecipient() {
            const newId = Utils.createId();

            this.recipients.push({
                id: newId,
                email: '',
                isNew: true,
            });

            this.$nextTick(() => {
                this.$refs.recipientsGrid.currentInlineEditId = newId;
                this.$refs.recipientsGrid.enableInlineEdit();
            });
        },

        /**
         * Remove recipient from grid
         *
         * @param recipient
         */
        saveRecipient(recipient) {
            const index = this.recipients.findIndex((item) => {
                return item.id === recipient.id;
            });

            if (this.validateRecipient(recipient, index)) {
                this.$nextTick(() => {
                    this.$refs.recipientsGrid.currentInlineEditId = recipient.id;
                    this.$refs.recipientsGrid.enableInlineEdit();
                });
                return;
            }

            if (recipient.isNew) {
                this.addRecipient();
                this.recipients[index].isNew = false;
            }

            this.resetError();
        },

        /**
         * Cancel edit of recipient
         *
         * @param recipient
         */
        cancelSaveRecipient(recipient) {
            if (!recipient.isNew) {
                const index = this.recipients.findIndex((item) => {
                    return item.id === this.selectedRecipient.id;
                });

                // Reset data when saving is cancelled
                this.recipients[index] = this.selectedRecipient;
            } else {
                recipient.email = '';
            }

            this.resetError();
        },

        /**
         * Update recipient data
         *
         * @param item
         */
        onEditRecipient(item) {
            const index = this.recipients.findIndex((recipient) => {
                return item.id === recipient.id;
            });

            // Recheck error in current item
            if (!item.email) {
                this.$set(this.recipients, index, { ...item, errorMail: null });
            } else {
                this.validateRecipient(item, index);
            }

            this.$refs.recipientsGrid.currentInlineEditId = item.id;
            this.$refs.recipientsGrid.enableInlineEdit();
            this.selectedRecipient = { ...item };
        },

        /**
         * Remove recipient from grid
         *
         * @param itemIndex
         */
        onDeleteRecipient(itemIndex) {
            this.recipients.splice(itemIndex, 1);
        },

        fieldError(text) {
            if (!text) {
                return new ShopwareError({
                    code: 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
                });
            }

            return null;
        },

        /**
         * Set error message for recipient
         *
         * @param mail
         * @returns {null}
         */
        setMailError(mail) {
            let error = null;

            if (!mail) {
                error = new ShopwareError({
                    code: 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
                });
            }

            return error;
        },

        /**
         * Validate recipient and set error message
         *
         * @param item
         * @param itemIndex
         * @returns {*}
         */
        validateRecipient(item, itemIndex) {
            const errorMail = this.setMailError(item.email);

            this.$set(this.recipients, itemIndex, {
                ...item,
                errorMail,
            });

            return errorMail;
        },

        /**
         * Reset error message
         */
        resetError() {
            this.recipientGridError = null;
            this.recipients.forEach(item => {
                item.errorMail = null;
            });
        },

        /**
         * Can delete recipient
         *
         * @param itemIndex
         * @returns {boolean}
         */
        allowDeleteRecipient(itemIndex) {
            return itemIndex !== this.recipients.length - 1;
        },
    },
});
