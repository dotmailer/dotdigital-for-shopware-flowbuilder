import template from './dotdigital-flow-contact-modal.html.twig';
import './dotdigital-flow-contact-modal.scss';

const { Component, Mixin } = Shopware;

Component.register('dotdigital-flow-contact-modal', { // eslint-disable-line
    template,
    inject: ['DotdigitalApiService'],
    mixins: [Mixin.getByName('notification')],
    props: {
        sequence: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            sequenceReady: false,
            addressBookList: [],
            dataFieldList: [],
            contactEmail: null,
            addressBook: null,
            dataFields: [],
            contactOptIn: false,
            resubscribe: false,
        };
    },

    computed: {

        /**
         * Get and mutate address book list
         * @returns {*[]}
         */
        availableAddressBooks() {
            return this.addressBookList.map((addressBook) => {
                return {
                    value: addressBook.id,
                    label: `${addressBook.name}`,
                };
            }).filter((addressBookOption) => {
                return addressBookOption.label !== 'Test';
            });
        },

        /**
         * Get and mutate data filed list
         * @returns {*}
         */
        availableDataFields() {
            return this.dataFieldList
                .map((dataField) => {
                    return {
                        label: dataField.name,
                        value: {
                            name: dataField.name,
                            type: dataField.type,
                        },
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

        /**
         * Get help link
         * @returns {string}
         */
        helpLink() {
            return 'https://support.dotdigital.com/hc/en-gb/articles/8472407231762';
        },

        /**
         * Get title of modal
         * @returns {*}
         */
        modalTitle() {
            return this.$tc('sw-flow.actions.contact.title');
        },

        /**
         * Get subtitle of modal
         * @returns {*}
         */
        modalSubTitle() {
            return this.$tc('sw-flow.actions.contact.subtitle');
        },

        /**
         * Get recipient aware of the current sequence
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
                this.createNotificationError({
                    title: this.$tc('Error'),
                    message: error.message,
                });
            });
    },

    methods: {

        /**
         * handle update event form recipient component
         * @param event
         */
        handleRecipientSelection(event) {
            this.contactEmail = event.payload;
        },

        /**
         * handle update event form address book component
         * @param addressBookId
         */
        handleAddressBookSelection(addressBookId) {
            this.addressBook = addressBookId;
        },

        /**
         * handle update event form data field component
         * @param event
         */
        handleDataFieldSelection(event) {
            this.dataFields = event.payload;
        },

        /**
         * Shopware sequence hook for created component
         */

        async createdComponent() {
            const { config } = this.sequence;
            if (!this.isNew) {
                this.contactEmail = config.recipient;
                this.addressBook = config.addressBook;
                this.dataFields = config.dataFields;
                this.contactOptIn = config.contactOptIn;
                this.resubscribe = config.resubscribe;
            }

            this.dataFieldList = await this.DotdigitalApiService.getDataFields();
            this.addressBookList = await this.DotdigitalApiService.getAddressBooks();
            return this.sequence;
        },

        /**
         * Shopware sequence hook to do all the things
         */
        onAddAction() {
            const sequence = {
                ...this.sequence,
                config: {
                    ...this.sequence.config,
                    addressBook: this.addressBook,
                    dataFields: this.dataFields,
                    recipient: this.contactEmail,
                    contactOptIn: this.contactOptIn,
                    resubscribe: this.resubscribe,
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
