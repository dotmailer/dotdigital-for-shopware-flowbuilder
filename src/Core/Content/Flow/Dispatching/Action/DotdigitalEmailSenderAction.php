<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Dotdigital\Flow\Core\Framework\DataTypes\ContactCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactPersonalisationCollection;
use Dotdigital\Flow\Core\Framework\Event\DotdigitalEmailSenderAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\EventDataResolver\ResolveCampaignInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolvePersonalisedValuesInterface;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\Event\MailAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

class DotdigitalEmailSenderAction extends FlowAction implements EventSubscriberInterface
{
    private DotdigitalClientFactory $dotdigitalClientFactory;

    private ResolveContactInterface $resolveContact;

    private ResolveCampaignInterface $resolveCampaign;

    private ResolvePersonalisedValuesInterface $resolvePersonalisedValues;

    private LoggerInterface $logger;

    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory,
        ResolveContactInterface $resolveContact,
        ResolveCampaignInterface $resolveCampaign,
        ResolvePersonalisedValuesInterface $resolvePersonalisedValues,
        LoggerInterface $logger
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
        $this->resolveContact = $resolveContact;
        $this->resolveCampaign = $resolveCampaign;
        $this->resolvePersonalisedValues = $resolvePersonalisedValues;
        $this->logger = $logger;
    }

    /**
     * Get subscribed events.
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            self::getName() => 'handle',
        ];
    }

    /**
     * Requirements.
     *
     * @return string[]
     */
    public function requirements(): array
    {
        return [DotdigitalEmailSenderAware::class, MailAware::class];
    }

    /**
     * Handle Dotdigital action.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleFlow(StorableFlow $flow): void
    {
        try {
            $campaignCollection = $this->resolveCampaign->resolve($flow);

            /** @var ContactCollection $contactCollection */
            $contactCollection = $this->resolveContact->resolve($flow);

            /** @var ContactPersonalisationCollection $personalisedValues */
            $personalisedValues = $this->resolvePersonalisedValues->resolve($flow);
            $salesChannelId = $flow->getData('salesChannelId');

            $this->dotdigitalClientFactory
                ->createClient($salesChannelId)
                ->sendEmail(
                    $contactCollection,
                    $campaignCollection->first(),
                    $personalisedValues
                );
        } catch (\Throwable $e) {
            $this->logger->error('DotdigitalEmailSenderAction failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public static function getName(): string
    {
        return 'action.create.dotdigital_mail_sender';
    }
}
