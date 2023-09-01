<?php declare(strict_types=1);

namespace Dotdigital\Flow\Subscriber;

use Dotdigital\Flow\Service\SystemConfigurationTrait;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Customer\CustomerEvents;
use Shopware\Core\Framework\Event\DataMappingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Carbon\Carbon;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\V3\Models\Contact;
use Dotdigital\V3\Models\ContactCollection;

class RegisterSubscriber implements EventSubscriberInterface
{
    use SystemConfigurationTrait;

    private ?string $salesChannelId;

    public function __construct(
        private DotdigitalClientFactory $dotdigitalClientFactory,
        private LoggerInterface $logger,
        private SystemConfigService $systemConfigService
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            CustomerEvents::MAPPING_REGISTER_CUSTOMER => 'onRegister'
        ];
    }

    public function onRegister(DataMappingEvent $event)
    {
        $input = $event->getInput();
        $output = $event->getOutput();

        if (!$input->get('ddg_sms_subscribed_name')) {
            $event->setOutput($output);
            return true;
        }

        $this->salesChannelId = $event->getOutput()['salesChannelId'];

        $contact = new Contact(
            array(
                'matchIdentifier' => 'email',
                'identifiers' => array(
                    'email' => $input->get('email'),
                    'mobileNumber' => $input->get('phone'),
                ),
                'dataFields' => array(
                    'firstName' => $input->get('firstName'),
                    'lastName' => $input->get('lastName'),
                ),
            )
        );

        $contact->setConsentRecords(
            array (
                array(
                    'text' => strip_tags($this->getConsentText()),
                    'dateTimeConsented' => Carbon::now()->toDateString(),
                    'url' => $_SERVER['HTTP_REFERER'] ?? '', // phpcs:ignore WordPress.Security
                    'ipAddress' => $_SERVER['REMOTE_ADDR'] ?? '', // phpcs:ignore WordPress.Security
                    'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? '', // phpcs:ignore WordPress.Security
                ),
            )
        );

        $list = $this->getList();

        if ($list) {
            $contact->setLists([$list]);
        }

        $client = $this->dotdigitalClientFactory
            ->createV3Client($this->salesChannelId)
            ->getClient();

        $contactCollection = new ContactCollection();
        $contactCollection->add($contact);

        try {
            $client->contacts->import($contactCollection);
        } catch (\Exception $exception) {
            $this->logger->error(
                'Dotdigital client request error',
                ['exception' => $exception]
            );
        }

        $event->setOutput($output);
        return true;
    }
}
