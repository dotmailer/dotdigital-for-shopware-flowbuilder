<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Subscriber;

use Carbon\Carbon;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\SystemConfigurationTrait;
use Dotdigital\V3\Models\Contact;
use Dotdigital\V3\Models\ContactCollection;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Customer\CustomerEvents;
use Shopware\Core\Framework\Event\DataMappingEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegisterSubscriber implements EventSubscriberInterface
{
    use SystemConfigurationTrait;

    private ?string $salesChannelId;

    public function __construct(
        private DotdigitalClientFactory $dotdigitalClientFactory,
        private LoggerInterface $logger,
        private SystemConfigService $systemConfigService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CustomerEvents::MAPPING_REGISTER_CUSTOMER => 'onRegister',
        ];
    }

    /**
     * @throws \Http\Client\Exception
     *
     * @return true
     */
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
            [
                'matchIdentifier' => 'email',
                'identifiers' => [
                    'email' => $input->get('email'),
                    'mobileNumber' => $input->get('phone'),
                ],
                'dataFields' => [
                    'firstName' => $input->get('firstName'),
                    'lastName' => $input->get('lastName'),
                ],
            ]
        );

        $contact->setConsentRecords(
            [
                [
                    'text' => strip_tags($this->getConsentText()),
                    'dateTimeConsented' => Carbon::now()->toDateString(),
                    'url' => $_SERVER['HTTP_REFERER'] ?? '',
                    'ipAddress' => $_SERVER['REMOTE_ADDR'] ?? '',
                    'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                ],
            ]
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
