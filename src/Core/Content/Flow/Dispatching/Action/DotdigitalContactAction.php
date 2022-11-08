<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactStruct;
use Dotdigital\Flow\Core\Framework\Event\DotdigitalContactAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\RecipientResolver;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DotdigitalContactAction extends FlowAction
{
    private DotdigitalClientFactory $dotdigitalClientFactory;

    private BusinessEventEncoder $businessEventEncoder;

    private StringTemplateRenderer $stringTemplateRenderer;

    private RecipientResolver $recipientResolver;

    private LoggerInterface $logger;

    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory,
        BusinessEventEncoder $businessEventEncoder,
        StringTemplateRenderer $stringTemplateRenderer,
        RecipientResolver $recipientResolver,
        LoggerInterface $logger
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
        $this->businessEventEncoder = $businessEventEncoder;
        $this->stringTemplateRenderer = $stringTemplateRenderer;
        $this->recipientResolver = $recipientResolver;
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
        return [DotdigitalContactAware::class, MailAware::class];
    }

    /**
     * Handle Dotdigital action.
     *
     * @throws GuzzleException|\InvalidArgumentException
     */
    public function handle(FlowEvent $event): void
    {
        if (!$event->getEvent() instanceof MailAware) {
            throw new \Exception('Not an instance of MailAware', 422);
        }

        $eventConfig = $event->getConfig();

        if (!\array_key_exists('recipient', $eventConfig)) {
            throw new \InvalidArgumentException('The contactEmail value in the flow action configuration is invalid or missing.', 422);
        }

        if (!\array_key_exists('addressBook', $eventConfig) && !is_iterable($eventConfig['addressBook'])) {
            throw new \InvalidArgumentException('The addressBook value in the flow action configuration is invalid or missing.', 422);
        }

        if (!\array_key_exists('resubscribe', $eventConfig)) {
            throw new \InvalidArgumentException('The resubscribe value in the flow action configuration is invalid or missing.', 422);
        }

        $availableData = $this->businessEventEncoder->encode($event->getEvent());
        $context = $event->getContext();

        try {
            $recipients = $this->recipientResolver->getRecipients($eventConfig['recipient'], $event);
        } catch (\Exception $exception) {
            $this->logger->error(
                'Dotdigital recipients collection error',
                ['exception' => $exception]
            );

            return;
        }

        /**
         * Template resolver
         */
        $renderedDataFields = [];
        foreach ($eventConfig['dataFields'] as $dataField) {
            $renderedDataFields[] = [
                'key' => $dataField['key'],
                'value' => $this->stringTemplateRenderer->render((string) $dataField['value'], $availableData, $context),
            ];
        }

        /** @var SalesChannelContext $channelContext */
        $channelContext = $context->getSource();
        $addressBook = new AddressBookStruct((int) $eventConfig['addressBook']);
        $recipients = $recipients->getElements();
        $contactEmail = reset($recipients)->getEmail();
        $contact = new ContactStruct(
            null,
            $contactEmail,
            'unknown',
            $eventConfig['contactOptIn'] ? 'unknown' : 'Single',
            'Html',
            $renderedDataFields,
        );

        if ($eventConfig['resubscribe']) {
            $this->dotdigitalClientFactory
                ->createClient($channelContext->getSalesChannelId())
                ->resubscribeContact(
                    $contact,
                    $addressBook
                );
        } else {
            $this->dotdigitalClientFactory
                ->createClient($channelContext->getSalesChannelId())
                ->addContact(
                    $contact,
                    $addressBook
                );
        }
    }

    public static function getName(): string
    {
        return 'action.create.dotdigital_contact';
    }
}
