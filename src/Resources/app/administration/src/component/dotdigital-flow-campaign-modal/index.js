import { ref, computed, onMounted, nextTick, inject, getCurrentInstance } from 'vue';
import template from './dotdigital-flow-campaign-modal.html.twig';
import '../shared/scss/dd-flow-modal.scss';

const { Component, Mixin } = Shopware;
Component.register('dotdigital-flow-campaign-modal', {
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
        const campaignList = ref([]);
        const contactEmail = ref(null);
        const campaignId = ref('');

        // Computed properties
        const availableCampaigns = computed(() => {
            // Format options in the exact structure sw-single-select expects
            return campaignList.value.map(campaign => ({
                id: String(campaign.id),
                name: campaign.name,
                value: String(campaign.id),
                label: campaign.name,
            }));
        });

        const isNew = computed(() => !props.sequence?.id);

        const modalTitle = computed(() => $tc('sw-flow.actions.campaign.title'));
        const modalSubTitle = computed(() => $tc('sw-flow.actions.campaign.subtitle'));

        const entityAware = computed(() => [
            'CustomerAware', 'UserAware', 'OrderAware', 'CustomerGroupAware',
        ]);

        // Methods
        const handleCampaignSelection = (selectedValue) => {
            campaignId.value = selectedValue;
        };

        const handleRecipientSelection = (event) => {
            contactEmail.value = event.payload;
        };

        const loadCampaigns = async () => {
            try {
                if (DotdigitalApiService?.getCampaigns) {
                    const campaigns = await DotdigitalApiService.getCampaigns();
                    return campaigns || [];
                }
                return [];
            } catch (error) {
                console.error('Failed to load campaigns:', error);
                return [];
            }
        };

        const createdComponent = async () => {
            const { config } = props.sequence || {};
            if (!isNew.value && config) {
                contactEmail.value = config.recipient;
                campaignId.value = config.campaignId ? String(config.campaignId) : '';
            }

            campaignList.value = await loadCampaigns();
        };

        const onAddAction = () => {
            const sequence = {
                ...props.sequence,
                config: {
                    ...(props.sequence.config || {}),
                    campaignId: campaignId.value,
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
            campaignId,
            availableCampaigns,
            isNew,
            modalTitle,
            modalSubTitle,
            entityAware,
            handleCampaignSelection,
            handleRecipientSelection,
            onAddAction,
            onClose,
        };
    },
});
