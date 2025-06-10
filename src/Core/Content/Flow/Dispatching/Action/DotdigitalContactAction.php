<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Dotdigital\Flow\Core\Framework\Event\DotdigitalContactAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\EventDataResolver\ResolveAddressBookInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactDataFieldsInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactInterface;
use GuzzleHttp\Exception\GuzzleException;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\Event\MailAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

class DotdigitalContactAction extends FlowAction implements EventSubscriberInterface
{
    private DotdigitalClientFactory $dotdigitalClientFactory;

    private ResolveAddressBookInterface $resolveAddressBook;

    private ResolveContactInterface $resolveContact;

    private ResolveContactDataFieldsInterface $resolveContactDataFields;

    private LoggerInterface $logger;

    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory,
        ResolveAddressBookInterface $resolveAddressBook,
        ResolveContactInterface $resolveContact,
        ResolveContactDataFieldsInterface $resolveContactDataFields,
        LoggerInterface $logger
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
        $this->resolveAddressBook = $resolveAddressBook;
        $this->resolveContact = $resolveContact;
        $this->resolveContactDataFields = $resolveContactDataFields;
        $this->logger = $logger;
    }

    /**
     * Get subscribed events.
     *
     * @deprecated since Shopware 6.5
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
    public function handleFlow(StorableFlow $flow): void
    {
        try{
            $flowConfig = $flow->getConfig();

            $contact = $this->resolveContact->resolve($flow)->first();
            $contactDataFieldsCollection = $this->resolveContactDataFields->resolve($flow);
            $contact->setDataFields($contactDataFieldsCollection->jsonSerialize());
            $addressBook = $this->resolveAddressBook->resolve($flow)->first();
            $salesChannelId = $flow->getData('salesChannelId');

            if ($flowConfig['contactOptIn']) {
                $contact->setOptInType('Double');
            }

            switch (true) {
                case $flowConfig['resubscribe'] && $addressBook->isApiReady():
                    $this->dotdigitalClientFactory
                        ->createClient($salesChannelId)
                        ->resubscribeContactToAddressBook($contact, $addressBook);

                    break;
                case !$flowConfig['resubscribe'] && $addressBook->isApiReady():
                    $this->dotdigitalClientFactory
                        ->createClient($salesChannelId)
                        ->addContactToAddressBook($contact, $addressBook);

                    break;
                case $flowConfig['resubscribe'] && !$addressBook->isApiReady():
                    $this->dotdigitalClientFactory
                        ->createClient($salesChannelId)
                        ->resubscribeContact($contact);

                    break;
                default:
                    $this->dotdigitalClientFactory
                        ->createClient($salesChannelId)
                        ->createOrUpdateContact($contact);

                    break;
            }
        } catch (\Throwable $e) {
            $this->logger->error('DotdigitalContactAction failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public static function getName(): string
    {
        return 'action.create.dotdigital_contact';
    }
}
