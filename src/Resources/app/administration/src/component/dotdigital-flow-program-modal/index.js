import { ref, computed, onMounted, nextTick, inject, getCurrentInstance } from 'vue';
import template from './dotdigital-flow-program-modal.html.twig';
import '../shared/scss/dd-flow-modal.scss';

const { Component, Mixin } = Shopware;
Component.register('dotdigital-flow-program-modal', {
    template,
    mixins: [Mixin.getByName('notification')],
    props: {
        sequence: {
            type: Object,
            required: true,
        },
    },

    emits: ['process-finish', 'modal-close'],

    setup(props, { emit }) {
        // Get services via inject
        const DotdigitalApiService = inject('DotdigitalApiService');
        // Use current instance for notification service
        const { proxy } = getCurrentInstance();
        // Shopware's translation service
        const $tc = (key, ...args) => Shopware.Snippet.tc(key, ...args);

        // Reactive state
        const sequenceReady = ref(false);
        const programList = ref([]);
        const dataFieldList = ref([]);
        const contactEmail = ref(null);
        const programId = ref(null);
        const dataFields = ref([]);

        // Computed properties
        const availablePrograms = computed(() => {
            return programList.value.map((program) => {
                return {
                    value: program.id,
                    label: `${program.name} (${program.status})`,
                };
            });
        });

        const availableDataFields = computed(() => {
            return dataFieldList.value
                .map((dataField) => {
                    return {
                        label: dataField.name,
                        value: {
                            name: dataField.name,
                            type: dataField.type,
                        },
                    };
                });
        });

        const isNew = computed(() => !props.sequence?.id);

        const helpLink = computed(() => 'https://support.dotdigital.com/hc/en-gb/articles/9682026340498');

        const modalTitle = computed(() => $tc('sw-flow.actions.program.title'));

        const modalSubTitle = computed(() => $tc('sw-flow.actions.program.subtitle'));

        const entityAware = computed(() => [
            'CustomerAware',
            'UserAware',
            'OrderAware',
            'CustomerGroupAware',
        ]);

        // Methods
        const handleRecipientSelection = (event) => {
            contactEmail.value = event.payload;
        };

        const handleProgramSelection = (selectedProgramId) => {
            programId.value = selectedProgramId;
        };

        const handleDataFieldSelection = (event) => {
            dataFields.value = event.payload;
        };

        const createdComponent = async () => {
            const { config } = props.sequence;
            if (!isNew.value) {
                contactEmail.value = config.recipient;
                programId.value = config.programId;
                dataFields.value = config.dataFields;
            }

            dataFieldList.value = await DotdigitalApiService.getDataFields();
            programList.value = await DotdigitalApiService.getPrograms();
            return props.sequence;
        };

        const onAddAction = () => {
            const sequence = {
                ...props.sequence,
                config: {
                    ...props.sequence.config,
                    programId: programId.value,
                    dataFields: dataFields.value,
                    recipient: contactEmail.value,
                },
            };

            nextTick(() => {
                emit('process-finish', sequence);
            });
        };

        const onClose = () => {
            emit('modal-close');
        };

        // Lifecycle hook
        onMounted(() => {
            sequenceReady.value = false;

            createdComponent()
                .finally(() => {
                    sequenceReady.value = true;
                })
                .catch((error) => {
                    proxy.createNotificationError({
                        title: $tc('Error'),
                        message: error.message,
                    });
                });
        });

        return {
            $tc,
            sequenceReady,
            contactEmail,
            programId,
            dataFields,
            availablePrograms,
            availableDataFields,
            isNew,
            helpLink,
            modalTitle,
            modalSubTitle,
            entityAware,
            handleRecipientSelection,
            handleProgramSelection,
            handleDataFieldSelection,
            onAddAction,
            onClose,
        };
    },
});
