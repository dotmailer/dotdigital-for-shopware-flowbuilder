import template from './datafield-selector.html.twig';
import './datafield-selector.scss';

const { Component, Utils } = Shopware;
const { mapState } = Component.getComponentHelper();

Component.register('dotdigital-data-field-selector', {// eslint-disable-line
    template,
    mixins: [],
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
    data() {
        return {
            showDataFields: false,
            selectedDataField: null,
            dataFieldGridError: null,
            dataFieldsGridData: [],
            entityAwareness: [
                'CustomerAware',
                'UserAware',
                'OrderAware',
                'CustomerGroupAware',
            ],
        };
    },

    watch: {
        loading: {
            handler(value) {
                if (!value) this.addDataField();
            },
        },
    },

    computed: {

        /**
         * Has max limit been reached
         * @returns {boolean}
         */
        isLimitReached() {
            return this.limit < this.dataFieldsGridData.length
                || this.dataFieldsGridData.length >= this.dataFieldOptions.length;
        },

        /**
         * Insure data fields are not duplicated
         * @returns {*}
         */
        availableDataFieldOptions() {
            if (!this.unique) return this.dataFieldOptions;
            return this.dataFieldOptions.filter(dataFieldOption => {
                return !this.dataFieldsGridData.find(dataField => {
                    if (this.selectedDataField && this.selectedDataField.key === dataFieldOption.value) return false;
                    return dataField.key === dataFieldOption.value;
                });
            });
        },

        /**
         * Get awareness of the current sequence
         * @returns {*[]}
         */
        entityAware() {
            return [...this.aware, ...this.entityAwareness];
        },

        /**
         * Is new dataField entity?
         * @returns {boolean}
         */
        isNew() {
            return !this.dataFieldsGridData.length > 0;
        },

        /**
         * Get dataField columns
         * @returns {[{property: string, inlineEdit: string, label: *}]}
         */
        dataFieldColumns() {
            return [
                {
                    property: 'key',
                    label: this.$tc('sw-flow.shared.data-field-selector.grid.columns.key.header'),
                    inlineEdit: 'string',
                },
                {
                    property: 'type',
                    label: this.$tc('sw-flow.shared.data-field-selector.grid.columns.type.header'),
                    inlineEdit: 'string',
                },
                {
                    property: 'value',
                    label: this.$tc('sw-flow.shared.data-field-selector.grid.columns.value.header'),
                    inlineEdit: 'string',
                },
            ];
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
            this.addDataField();
            this.dataFieldsGridData = [...this.dataFields];
        },


        /**
         * Add dataField
         */
        handleDataFieldSelection(dataField) {
            this.selectedDataField = {
                ...this.selectedDataField,
                ...{ key: dataField.name },
                ...{ type: dataField.type },
            };
        },

        /**
         * Filter dataFields and emit to parent component
         */
        emit() {
            this.$emit('selected-data-field', {
                payload: this.getDataFields(),
            });
        },

        getDataFields() {
            return this.dataFieldsGridData.filter(dataField => {
                return dataField.key && dataField.value && !dataField.isNew;
            });
        },

        /**
         * Add new dataField to grid
         */
        addDataField() {
            if (this.isLimitReached) return;
            const newId = Utils.createId();
            this.dataFieldsGridData.push({
                id: newId,
                opt: {
                    dataFieldSelection: null,
                },
                isNew: true,
                value: null,
                key: null,
                type: null,
            });

            const index = this.dataFieldsGridData.findIndex((item) => {
                return item.id === newId;
            });

            this.$nextTick(() => {
                this.$refs.dataFieldsGrid.currentInlineEditId = newId;
                this.$refs.dataFieldsGrid.enableInlineEdit();
                this.selectedDataField = { ...this.dataFieldsGridData[index] };
            });
        },

        /**
         * Remove dataField from grid
         * @param dataField
         */
        saveDataField(dataField) {
            const index = this.dataFieldsGridData.findIndex((item) => {
                return item.id === dataField.id;
            });

            this.dataFieldsGridData[index] = {
                ...this.dataFieldsGridData[index],
                ...this.selectedDataField,
            };

            if (dataField.isNew) {
                this.dataFieldsGridData[index].isNew = false;
            }

            this.addDataField();
            this.emit();
        },

        /**
         * Cancel edit of dataField
         * @param dataField
         */
        cancelSaveDataField(dataField) {
            if (!dataField.isNew) {
                const index = this.dataFieldsGridData.findIndex((item) => {
                    return item.id === this.selectedDataField.id;
                });

                // Reset data when saving is cancelled
                this.dataFieldsGridData[index] = this.selectedDataField;
            }
            this.emit();
        },

        /**
         * Update dataField data
         * @param item
         */
        onEditDataField(item) {
            const index = this.dataFieldsGridData.findIndex((dataField) => {
                return item.id === dataField.id;
            });

            this.$set(this.dataFieldsGridData, index, { ...item, errorMail: null });
            this.$refs.dataFieldsGrid.currentInlineEditId = item.id;
            this.$refs.dataFieldsGrid.enableInlineEdit();
            this.selectedDataField = { ...item };
            this.emit();
        },

        /**
         * Remove dataField from grid
         * @param itemIndex
         */
        onDeleteDataField(itemIndex) {
            this.dataFieldsGridData.splice(itemIndex, 1);
            this.emit();
        },

        /**
         * Can delete dataField
         * @param itemIndex
         * @returns {boolean}
         */
        allowDeleteDataField(itemIndex) {
            return itemIndex !== this.dataFieldsGridData.length - 1;
        },
    },
});
