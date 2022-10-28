import template from './dotdigital-flow-contact-modal.html.twig';
import './dotdigital-flow-contact-modal.scss';

const { Component, Mixin} = Shopware;

Component.register('dotdigital-flow-contact-modal', {
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
            tempData: {
                contactEmail: 'chaz-shopware@emailsim.io',
                addressBook: '30203316',
                contactDataFields: [
                    {
                        key: 'FIRSTNAME',
                        value: 'Chaz',
                    },
                    {
                        key: 'LASTNAME',
                        value: 'Kangaroo',
                    },
                ],
                contactOptIn: true,
                resubscribe: true
            }
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
                config: this.tempData
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

    }
});
