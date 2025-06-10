import { ref, computed, onMounted, nextTick, inject, getCurrentInstance } from 'vue';
import template from './dotdigital-flow-contact-modal.html.twig';
import '../shared/scss/dd-flow-modal.scss';

const { Component, Mixin } = Shopware;
Component.register('dotdigital-flow-contact-modal', {
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
        const addressBookList = ref([]);
        const dataFieldList = ref([]);
        const contactEmail = ref(null);
        const addressBook = ref(null);
        const dataFields = ref([]);
        const contactOptIn = ref(false);
        const resubscribe = ref(false);

        // Computed properties
        const availableAddressBooks = computed(() => {
            return addressBookList.value.map((addressBookOption) => {
                return {
                    value: addressBookOption.id,
                    label: `${addressBookOption.name}`,
                };
            }).filter((addressBookOption) => {
                return addressBookOption.label !== 'Test';
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

        const helpLink = computed(() => 'https://support.dotdigital.com/hc/en-gb/articles/8472407231762');

        const modalTitle = computed(() => $tc('sw-flow.actions.contact.title'));

        const modalSubTitle = computed(() => $tc('sw-flow.actions.contact.subtitle'));

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

        const handleAddressBookSelection = (addressBookId) => {
            addressBook.value = addressBookId;
        };

        const handleDataFieldSelection = (event) => {
            dataFields.value = event.payload;
        };

        const createdComponent = async () => {
            const { config } = props.sequence;
            if (!isNew.value) {
                contactEmail.value = config.recipient;
                addressBook.value = config.addressBook;
                dataFields.value = config.dataFields;
                contactOptIn.value = config.contactOptIn;
                resubscribe.value = config.resubscribe;
            }

            dataFieldList.value = await DotdigitalApiService.getDataFields();
            addressBookList.value = await DotdigitalApiService.getAddressBooks();
            return props.sequence;
        };

        const onAddAction = () => {
            const sequence = {
                ...props.sequence,
                config: {
                    ...props.sequence.config,
                    addressBook: addressBook.value,
                    dataFields: dataFields.value,
                    recipient: contactEmail.value,
                    contactOptIn: contactOptIn.value,
                    resubscribe: resubscribe.value,
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
            addressBook,
            dataFields,
            contactOptIn,
            resubscribe,
            availableAddressBooks,
            availableDataFields,
            isNew,
            helpLink,
            modalTitle,
            modalSubTitle,
            entityAware,
            handleRecipientSelection,
            handleAddressBookSelection,
            handleDataFieldSelection,
            onAddAction,
            onClose,
        };
    },
});
