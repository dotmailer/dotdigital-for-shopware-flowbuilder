import { ref, computed, nextTick, onMounted, watch } from 'vue';
import template from './datafield-selector.html.twig';
import './datafield-selector.scss';

const { Component, Utils } = Shopware;

Component.register('dotdigital-data-field-selector', {
    name: 'dotdigital-data-field-selector',
    template,

    props: {
        unique: {
            type: Boolean,
            required: false,
            default: false,
        },
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
        dataFields: {
            type: Array,
            required: false,
            default() {
                return [];
            },
        },
        dataFieldOptions: {
            type: Array,
            required: true,
            default() {
                return [];
            },
        },
        loading: {
            type: Boolean,
            required: false,
            default: true,
        },
    },

    emits: ['selected-data-field'],

    setup(props, { emit }) {
        // Direct access to Shopware's translation service
        const $tc = (key, ...args) => Shopware.Snippet.tc(key, ...args);

        // Reactive state
        const dataFieldsGrid = ref(null);
        const showDataFields = ref(false);
        const selectedDataField = ref(null);
        const dataFieldGridError = ref(null);
        const dataFieldsGridData = ref([]);

        // Constants
        const entityAwareness = [
            'CustomerAware',
            'UserAware',
            'OrderAware',
            'CustomerGroupAware',
        ];

        // Computed properties
        const isLimitReached = computed(() => {
            return props.limit < dataFieldsGridData.value.length
                || dataFieldsGridData.value.length >= props.dataFieldOptions.length;
        });

        const availableDataFieldOptions = computed(() => {
            if (!props.unique) {
                return props.dataFieldOptions;
            }

            // Filter out options that are already used in dataFieldsGridData
            return props.dataFieldOptions.filter(dataFieldOption => {
                const currentEditingKey = selectedDataField.value?.key;

                return !dataFieldsGridData.value.some(dataField => {
                    // If this is the dataField we're currently editing, don't filter it out
                    if (currentEditingKey && dataField.key === currentEditingKey
                        && dataField.id === selectedDataField.value?.id) {
                        return false;
                    }
                    return dataField.key === dataFieldOption.value.name && !dataField.isNew;
                });
            });
        });

        const entityAware = computed(() => {
            return [...props.aware, ...entityAwareness];
        });

        const isNew = computed(() => {
            return !dataFieldsGridData.value.length > 0;
        });

        const dataFieldColumns = computed(() => {
            return [
                {
                    property: 'key',
                    label: $tc('sw-flow.shared.data-field-selector.grid.columns.key.header'),
                    inlineEdit: 'string',
                },
                {
                    property: 'type',
                    label: $tc('sw-flow.shared.data-field-selector.grid.columns.type.header'),
                    inlineEdit: 'string',
                },
                {
                    property: 'value',
                    label: $tc('sw-flow.shared.data-field-selector.grid.columns.value.header'),
                    inlineEdit: 'string',
                },
            ];
        });

        // Methods
        function handleDataFieldSelection(dataFieldName) {
            // Find the complete data field object from props.dataFieldOptions
            const dataFieldOption = props.dataFieldOptions.find(option => option.value.name === dataFieldName);

            if (!dataFieldOption) {
                return;
            }

            const dataField = dataFieldOption.value;

            selectedDataField.value = {
                ...selectedDataField.value,
                key: dataField.name,
                type: evaluateDataFieldTypeDescription(dataField.type),
            };
        }

        function emit$() {
            emit('selected-data-field', {
                payload: getDataFields(),
            });
        }

        function evaluateDataFieldTypeDescription(type = null) {
            if (!type) {
                return $tc('sw-flow.shared.data-field-selector.grid.columns.type.placeholder');
            }
            return $tc(`sw-flow.shared.data-field-selector.grid.columns.type.values.${type.toLowerCase()}`);
        }

        function getDataFields() {
            return dataFieldsGridData.value.filter(dataField => {
                return dataField.key && dataField.value && !dataField.isNew;
            });
        }

        function addDataField() {
            // Check if we've already reached the limit
            if (isLimitReached.value) return;

            // Check if there are any incomplete entries (with empty key or value)
            const hasIncompleteEntries = dataFieldsGridData.value.some(dataField => {
                return (!dataField.key || !dataField.value);
            });

            // Don't add a new field if there are incomplete entries
            if (hasIncompleteEntries) {
                return;
            }

            const newId = Utils.createId();
            dataFieldsGridData.value.push({
                id: newId,
                opt: {
                    dataFieldSelection: null,
                },
                isNew: true,
                value: null,
                key: null,
                type: null,
            });

            const index = dataFieldsGridData.value.findIndex((item) => {
                return item.id === newId;
            });

            nextTick(() => {
                dataFieldsGrid.value.currentInlineEditId = newId;
                dataFieldsGrid.value.enableInlineEdit();
                selectedDataField.value = { ...dataFieldsGridData.value[index] };
            });
        }

        function saveDataField(dataField) {
            const index = dataFieldsGridData.value.findIndex((item) => {
                return item.id === dataField.id;
            });

            dataFieldsGridData.value[index] = {
                ...dataFieldsGridData.value[index],
                ...selectedDataField.value,
            };

            if (dataField.isNew) {
                dataFieldsGridData.value[index].isNew = false;
            }

            addDataField();
            emit$();
        }

        function cancelSaveDataField(dataField) {
            if (!dataField.isNew) {
                const index = dataFieldsGridData.value.findIndex((item) => {
                    return item.id === selectedDataField.value.id;
                });

                // Reset data when saving is cancelled
                dataFieldsGridData.value[index] = selectedDataField.value;
            }
            emit$();
        }

        function onEditDataField(item) {
            if (dataFieldsGrid.value.currentInlineEditId) {
                dataFieldsGrid.value.disableInlineEdit();
            }

            const index = dataFieldsGridData.value.findIndex((dataField) => {
                return item.id === dataField.id;
            });

            dataFieldsGridData.value[index] = { ...item, errorMail: null };

            nextTick(() => {
                dataFieldsGrid.value.currentInlineEditId = item.id;
                dataFieldsGrid.value.enableInlineEdit();
                selectedDataField.value = { ...item };
                emit$();
            });
        }

        function onDeleteDataField(itemIndex) {
            dataFieldsGridData.value.splice(itemIndex, 1);
            emit$();
        }

        function allowDeleteDataField(itemIndex) {
            return itemIndex !== dataFieldsGridData.value.length - 1;
        }

        function initializeComponent() {
            // Clear existing data to prevent duplication
            dataFieldsGridData.value = [];

            // If we have saved dataFields, add them to the grid
            if (props.dataFields && props.dataFields.length > 0) {
                dataFieldsGridData.value = props.dataFields.map(field => {
                    return {
                        ...field,
                        id: field.id || Utils.createId(),
                        isNew: false,
                        opt: {
                            dataFieldSelection: field.key,
                        },
                    };
                });
            }

            // Always add a new empty row for additional entries
            addDataField();
        }

        // Watch for loading state changes
        watch(() => props.loading, (value) => {
            if (!value) {
                initializeComponent();
            }
        });

        // Initial setup on mount
        onMounted(() => {
            if (!props.loading) {
                initializeComponent();
            }
        });

        return {
            $tc,
            dataFieldsGrid,
            showDataFields,
            selectedDataField,
            dataFieldGridError,
            dataFieldsGridData,
            isLimitReached,
            availableDataFieldOptions,
            entityAware,
            isNew,
            dataFieldColumns,
            handleDataFieldSelection,
            addDataField,
            saveDataField,
            cancelSaveDataField,
            onEditDataField,
            onDeleteDataField,
            allowDeleteDataField,
            evaluateDataFieldTypeDescription,
        };
    },
});
