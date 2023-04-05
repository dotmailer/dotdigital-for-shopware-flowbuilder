<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Dotdigital\Flow\Core\Framework\Event\DotdigitalContactAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\EventDataResolver\ResolveAddressBookInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactDataFieldsInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DotdigitalContactAction extends FlowAction implements EventSubscriberInterface
{
    private DotdigitalClientFactory $dotdigitalClientFactory;

    private ResolveAddressBookInterface $resolveAddressBook;

    private ResolveContactInterface $resolveContact;

    private ResolveContactDataFieldsInterface $resolveContactDataFields;

    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory,
        ResolveAddressBookInterface $resolveAddressBook,
        ResolveContactInterface $resolveContact,
        ResolveContactDataFieldsInterface $resolveContactDataFields
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
        $this->resolveAddressBook = $resolveAddressBook;
        $this->resolveContact = $resolveContact;
        $this->resolveContactDataFields = $resolveContactDataFields;
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
	 * @param StorableFlow $flow
	 *
	 * @throws GuzzleException|\InvalidArgumentException
	 */
	public function handleFlow(StorableFlow $flow): void
	{
		if (!$flow->hasData('mailStruct')) {
			throw new \Exception('Not an instance of MailAware', 422);
		}

		$flowConfig = $flow->getConfig();
		$contact = $this->resolveContact->resolve($flow)->first();
		$contactDataFieldsCollection = $this->resolveContactDataFields->resolve($flow);
		$contact->setDataFields($contactDataFieldsCollection->jsonSerialize());
		$addressBook = $this->resolveAddressBook->resolve($flow)->first();
		$context = $flow->getContext();
		/** @var SalesChannelContext $channelContext */
		$channelContext = $context->getSource();

		if ($flowConfig['contactOptIn']) {
			$contact->setOptInType('Double');
		}

		switch (true) {
			case $flowConfig['resubscribe'] && $addressBook->isApiReady():
				$this->dotdigitalClientFactory
					->createClient($channelContext->getSalesChannelId())
					->resubscribeContactToAddressBook($contact, $addressBook);

				break;
			case !$flowConfig['resubscribe'] && $addressBook->isApiReady():
				$this->dotdigitalClientFactory
					->createClient($channelContext->getSalesChannelId())
					->addContactToAddressBook($contact, $addressBook);

				break;
			case $flowConfig['resubscribe'] && !$addressBook->isApiReady():
				$this->dotdigitalClientFactory
					->createClient($channelContext->getSalesChannelId())
					->resubscribeContact($contact);

				break;
			default:
				$this->dotdigitalClientFactory
					->createClient($channelContext->getSalesChannelId())
					->createOrUpdateContact($contact);

				break;
		}
	}

    public static function getName(): string
    {
        return 'action.create.dotdigital_contact';
    }
}
