import template from './dotdigital-flow-contact-modal.html.twig';
import './dotdigital-flow-contact-modal.scss';

const { Component, Mixin } = Shopware;

Component.register('dotdigital-flow-contact-modal', {
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
            tempData: {
                contactEmail: 'test@email.xyz',
                addressBook: '4584905',
                contactDataFields: [
                    {
                        key: 'FirstName',
                        value: 'TEST FIRSTNAME',
                    },
                    {
                        key: 'LastName',
                        value: 'TEST LASTNaME ',
                    },
                ],
                contactOptIn: true,
                resubscribe: true,
            },
        };
    },

    computed: {

        helpLink() {
            return '#';
        },

        modalTitle() {
            return this.$tc('Dotdigital ContactStruct');
        },

        modalSubTitle() {
            return this.$tc('Some description');
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
        this.DotdigitalApiService.getAddressBooks().then(response => { console.log(response); });
        this.DotdigitalApiService.getDataFields().then(response => { console.log(response); });
        this.createdComponent();
    },

    methods: {

        /**
         * Shopware sequence hook for created component
         */
        createdComponent() {
        },

        /**
         * Shopware sequence hook to do all the things
         */
        onAddAction() {
            const sequence = {
                ...this.sequence,
                config: this.tempData,
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
