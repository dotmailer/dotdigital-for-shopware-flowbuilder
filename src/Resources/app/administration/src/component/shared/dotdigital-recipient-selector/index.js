import template from './contact-selector.html.twig';
import './contact-selector.scss';

const { Component, Utils, Mixin, Classes: { ShopwareError } } = Shopware;
const { mapState } = Component.getComponentHelper();

Component.register('dotdigital-recipient-selector', {// eslint-disable-line
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
    data() {
        return {
            showRecipientEmails: false,
            mailRecipient: null,
            selectedRecipient: null,
            recipientGridError: null,
            recipients: [],
            entityAwareness: [
                'CustomerAware',
                'UserAware',
                'OrderAware',
                'CustomerGroupAware',
            ],
        };
    },
    computed: {

        isLimitReached() {
            return this.limit === this.recipients.length;
        },

        /**
         * Get awareness of the current sequence
         * @returns {*[]}
         */
        entityAware() {
            return [...this.aware, ...this.entityAwareness];
        },

        /**
         * Get recipient aware description of the current sequence
         * @returns {string}
         */
        mailRecipientDescription() {
            let description = '';
            switch (this.mailRecipient) {
                case 'custom':
                    description += this.$tc('sw-flow.shared.recipient-selector.fields.recipients.description');
                    description +=
                        ` <a href="https://support.dotdigital.com/hc/en-gb/articles/7101774577298" target="_blank">
                                ${this.$tc('sw-flow.shared.recipient-selector.fields.recipients.help-link')}
                          </a>
                        `;
                    break;
                default:
                    description += '';
                    break;
            }
            return description;
        },

        /**
         * Is new recipient entity?
         *
         * @returns {boolean}
         */
        isNew() {
            return !this.recipient?.type;
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
                    label: this.$tc('sw-flow.shared.recipient-selector.fields.recipients.options.custom'),
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
                    label: this.$tc('sw-flow.shared.recipient-selector.fields.recipients.options.newsletter'),
                },
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
                    ...this.isRecipientExcluded(this.recipientDefault) ? [] : this.recipientDefault,
                    ...this.isRecipientExcluded(this.recipientContactFormMail) ? [] : this.recipientContactFormMail,
                    ...this.isRecipientExcluded(this.recipientAdmin) ? [] : this.recipientAdmin,
                    ...this.isRecipientExcluded(this.recipientCustom) ? [] : this.recipientCustom,
                ];
            }

            if (this.triggerEvent.name === 'newsletter.confirm') {
                return [
                    ...this.isRecipientExcluded(this.recipientFromNewsLetterForm) ? [] : this.recipientFromNewsLetterForm,
                    ...this.isRecipientExcluded(this.recipientAdmin) ? [] : this.recipientAdmin,
                    ...this.isRecipientExcluded(this.recipientCustom) ? [] : this.recipientCustom,
                ];
            }

            if (
                this.triggerEvent.name === 'newsletter.confirm' ||
                this.triggerEvent.name === 'newsletter.register'
            ) {
                return [
                    ...this.isRecipientExcluded(this.recipientFromNewsLetterForm) ? [] : this.recipientFromNewsLetterForm,
                    ...this.isRecipientExcluded(this.recipientAdmin) ? [] : this.recipientAdmin,
                    ...this.isRecipientExcluded(this.recipientCustom) ? [] : this.recipientCustom,
                ];
            }

            const hasEntityAware = allowAwareConverted.some(allowedAware => this.entityAware.includes(allowedAware));

            if (hasEntityAware) {
                return [
                    ...this.isRecipientExcluded(this.recipientCustomer) ? [] : this.recipientCustomer,
                    ...this.isRecipientExcluded(this.recipientAdmin) ? [] : this.recipientAdmin,
                    ...this.isRecipientExcluded(this.recipientCustom) ? [] : this.recipientCustom,
                ];
            }

            return [
                ...this.isRecipientExcluded(this.recipientAdmin) ? [] : this.recipientAdmin,
                ...this.isRecipientExcluded(this.recipientCustom) ? [] : this.recipientCustom,
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
                label: `${this.$tc('sw-flow.shared.recipient-selector.grid.columns.email.header')} (max ${this.limit}) `,
                inlineEdit: 'string',
            }];
        },

        ...mapState('swFlowState', ['triggerEvent']),

    },

    /**
     * Called component create life cycle hook
     */
    created() {
        this.createdComponent();
    },

    methods: {

        /**
         * Shopware sequence hook
         */
        createdComponent() {
            this.mailRecipient = (this.recipient?.type)
                ? this.recipient.type
                : this.recipientOptions[0].value;

            if (!this.isNew) {
                if (this.recipient?.type === 'custom') {
                    Object.values(this.recipient.data)
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
            if (this.mailRecipient !== 'custom') {
                this.emit();
            }
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

            const invalidItemIndex = this.recipients.filter(item => !item.isNew)
                .findIndex(recipient => !recipient.email);

            if (invalidItemIndex >= 0) {
                this.validateRecipient(this.recipients[invalidItemIndex], invalidItemIndex);
            }

            return invalidItemIndex >= 0;
        },


        /**
         * Validate recipient and emit to parent component
         */
        emit() {
            this.recipientGridError = this.isRecipientGridError();
            if (this.recipientGridError) {
                return;
            }
            this.$emit('selected-recipient', {
                payload: {
                    type: this.mailRecipient,
                    data: this.getRecipientData(),
                },
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
                if (this.recipients.length >= 1) return;
                this.addRecipient();
            } else {
                this.showRecipientEmails = false;
            }
            this.emit();
        },

        /**
         * Add new recipient to grid
         */
        addRecipient() {
            if (this.isLimitReached) return;
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
            this.emit();
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
            this.emit();
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
            this.emit();
        },

        /**
         * Remove recipient from grid
         *
         * @param itemIndex
         */
        onDeleteRecipient(itemIndex) {
            this.recipients.splice(itemIndex, 1);
            this.emit();
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
         * Is recipient type allowed.
         * @param recipientType
         * @returns {boolean}
         */
        isRecipientExcluded(recipientType) {
            return this.exclude.includes(
                recipientType.at(0).value,
            );
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
