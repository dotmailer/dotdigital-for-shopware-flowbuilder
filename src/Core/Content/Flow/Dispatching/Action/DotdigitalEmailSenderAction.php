<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Dotdigital\Flow\Core\Framework\Event\DotdigitalEmailSenderAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\RecipientResolver;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Content\MailTemplate\Exception\MailEventConfigurationException;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DotdigitalEmailSenderAction extends FlowAction
{
    private DotdigitalClientFactory $dotdigitalClientFactory;

    private BusinessEventEncoder $businessEventEncoder;

    private LoggerInterface $logger;

    private RecipientResolver $recipientResolver;

    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory,
        BusinessEventEncoder $businessEventEncoder,
        LoggerInterface $logger,
        RecipientResolver $recipientResolver
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
        $this->businessEventEncoder = $businessEventEncoder;
        $this->logger = $logger;
        $this->recipientResolver = $recipientResolver;
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
    public function handle(FlowEvent $event): void
    {
        if (!$event->getEvent() instanceof MailAware) {
            throw new MailEventConfigurationException('Not an instance of MailAware', \get_class($event->getEvent()));
        }

        $eventConfig = $event->getConfig();

        if (empty($eventConfig['recipient'])) {
            throw new MailEventConfigurationException('The recipient value in the flow action configuration is missing.', \get_class($event));
        }

        try {
            $recipients = $this->recipientResolver->getRecipients($eventConfig['recipient'], $event);
        } catch (\Exception $exception) {
            $this->logger->error(
                'Dotdigital recipients collection error',
                ['exception' => $exception]
            );

            return;
        }

        if (!\array_key_exists('recipient', $eventConfig) || $recipients->count() === 0) {
            return;
        }

        $availableData = $this->businessEventEncoder->encode($event->getEvent());
        $personalisedValues = [];
        foreach ($availableData as $key => $data) {
            $personalisedValues[] = [
                'name' => $key,
                'value' => $data,
            ];
        }
        $context = $event->getContext();
        /** @var SalesChannelContext $channelContext */
        $channelContext = $context->getSource();
        $this->dotdigitalClientFactory
            ->createClient($channelContext->getSalesChannelId())
            ->sendEmail($recipients, $eventConfig['campaignId'], $personalisedValues);
    }

    public static function getName(): string
    {
        return 'action.create.dotdigital_mail_sender';
    }
}
