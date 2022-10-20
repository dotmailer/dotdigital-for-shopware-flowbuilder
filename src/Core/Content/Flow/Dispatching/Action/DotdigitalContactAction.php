<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactStruct;
use Dotdigital\Flow\Core\Framework\Event\DotdigitalContactAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DotdigitalContactAction extends FlowAction
{
    private DotdigitalClientFactory $dotdigitalClientFactory;

    /**
     * Constructor.
     */
    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
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
     * @throws GuzzleException | \InvalidArgumentException
     */
    public function handle(FlowEvent $event): void
    {
        if (!$event->getEvent() instanceof MailAware) {
            throw new \Exception('Not an instance of MailAware', \get_class($event->getEvent()));
        }

        $eventConfig = $event->getConfig();

        if (!\array_key_exists('contactEmail', $eventConfig) && !\is_string($eventConfig['contactEmail'])) {
            throw new \InvalidArgumentException('The contactEmail value in the flow action configuration is invalid or missing.', \get_class($event));
        }

        if (!\array_key_exists('addressBook', $eventConfig) && !is_iterable($eventConfig['addressBook'])) {
            throw new \InvalidArgumentException('The addressBook value in the flow action configuration is invalid or missing.', \get_class($event));
        }

        if (!\array_key_exists('resubscribe', $eventConfig)) {
            throw new \InvalidArgumentException('The resubscribe value in the flow action configuration is invalid or missing.', \get_class($event));
        }

        if (!\array_key_exists('addressBook', $eventConfig) && !\is_bool($eventConfig['addressBook'])) {
            throw new \InvalidArgumentException('The addressBook value in the flow action configuration is invalid or missing.', \get_class($event));
        }

        $context = $event->getContext();
        /** @var SalesChannelContext $channelContext */
        $channelContext = $context->getSource();
        $contactAddressBook = new AddressBookStruct((int) $eventConfig['addressBook']);
        $contactRecipient = new ContactStruct(
            null,
            $eventConfig['contactEmail'],
            'unknown',
            $eventConfig['contactOptIn'] ? 'unknown' : 'Single',
            'Html',
            $eventConfig['contactDataFields'],
        );

        if ((bool) ($eventConfig['resubscribe'])) {
            $this->dotdigitalClientFactory
                ->createClient($channelContext->getSalesChannelId())
                ->resubscribeContactToAddressBook(
                    $contactRecipient,
                    $contactAddressBook
                );
        } else {
            $this->dotdigitalClientFactory
                ->createClient($channelContext->getSalesChannelId())
                ->addContactToAddressBook(
                    $contactRecipient,
                    $contactAddressBook
                );
        }
    }

    /**
     * Get Name.
     */
    public static function getName(): string
    {
        return 'action.create.dotdigital_contact';
    }
}
