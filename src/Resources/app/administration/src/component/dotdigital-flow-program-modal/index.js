import template from './dotdigital-flow-program-modal.html.twig';
import '../shared/scss/dd-flow-modal.scss';

const { Component, Mixin } = Shopware;

Component.register('dotdigital-flow-program-modal', { // eslint-disable-line
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
            programList: [],
            dataFieldList: [],
            contactEmail: null,
            programId: null,
            dataFields: [],
        };
    },

    computed: {

        /**
         * Get and mutate program list
         * @returns {*[]}
         */
        availablePrograms() {
            return this.programList.map((program) => {
                return {
                    value: program.id,
                    label: `${program.name} (${program.status})`,
                };
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
            return 'https://support.dotdigital.com/hc/en-gb/articles/9682026340498';
        },

        /**
         * Get title of modal
         * @returns {*}
         */
        modalTitle() {
            return this.$tc('sw-flow.actions.program.title');
        },

        /**
         * Get subtitle of modal
         * @returns {*}
         */
        modalSubTitle() {
            return this.$tc('sw-flow.actions.program.subtitle');
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
         * handle update event from recipient component
         * @param event
         */
        handleRecipientSelection(event) {
            this.contactEmail = event.payload;
        },

        /**
         * handle update event from program component
         * @param programId
         */
        handleProgramSelection(programId) {
            this.programId = programId;
        },

        /**
         * handle update event from data field component
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
                this.programId = config.programId;
                this.dataFields = config.dataFields;
            }

            this.dataFieldList = await this.DotdigitalApiService.getDataFields();
            this.programList = await this.DotdigitalApiService.getPrograms();
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
                    programId: this.programId,
                    dataFields: this.dataFields,
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
