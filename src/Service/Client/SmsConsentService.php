<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\Client;

use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\SmsConsent\SmsConsentDataBag;
use Dotdigital\Flow\Service\SystemConfigurationTrait;
use Dotdigital\V3\Models\Contact;
use Http\Client\Exception;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class SmsConsentService
{
    use SystemConfigurationTrait;

    protected string $salesChannelId;

    public function __construct(
        private DotdigitalClientFactory $dotdigitalClientFactory,
        private LoggerInterface $logger,
        private SystemConfigService $systemConfigService
    ) {
    }

    /**
     * Subscribe contact
     *
     * @throws Exception
     */
    public function subscribe(SmsConsentDataBag $data, string $salesChannelIdentifier): void
    {
        $this->salesChannelId = $salesChannelIdentifier;
        $contact = new Contact(
            [
                'identifiers' => [
                    'email' => $data->getEmail(),
                    'mobileNumber' => $data->getPhone(),
                ],
                'dataFields' => [
                    'firstName' => $data->getFirstName(),
                    'lastName' => $data->getLastName(),
                ],
            ]
        );
        $contact->setConsentRecords($this->getConsent());

        if ($list = $this->getList()) {
            $contact->setLists([$list]);
        }

        $this->patch($contact, $salesChannelIdentifier);
    }

    /**
     * Unsubscribe contact
     *
     * @throws Exception
     */
    public function unSubscribe(SmsConsentDataBag $data, string $salesChannelIdentifier): void
    {
        $this->salesChannelId = $salesChannelIdentifier;
        $list = new AddressBookStruct();
        $list->setId((int) $this->getList());
        $contact = $this->getV2Contact($data->getEmail(), $salesChannelIdentifier);

        if (empty($contact)) {
            return;
        }

        $this->detachList($contact, $list, $salesChannelIdentifier);
    }

    private function getV2Contact(string $email, string $salesChannelIdentifier): ?ContactStruct
    {
        $client = $this->dotdigitalClientFactory->createClient($salesChannelIdentifier);

        try {
            $response = $client->getContactByEmail($email);
        } catch (\Exception $exception) {
            $this->logger->error(
                'Dotdigital client request error',
                ['exception' => $exception]
            );

            return null;
        }

        return $response;
    }

    /**
     * Detach list
     *
     * @throws Exception
     */
    private function detachList(ContactStruct $contact, AddressBookStruct $list, string $salesChannelIdentifier): void
    {
        $client = $this->dotdigitalClientFactory->createClient($salesChannelIdentifier);

        try {
            $response = $client->detachContactFromList(
                $contact,
                $list
            );
        } catch (\Exception $exception) {
            $this->logger->error(
                'Dotdigital client request error',
                ['exception' => $exception]
            );
        }
    }

    /**
     * Patch contact
     *
     * @throws Exception
     */
    private function patch(Contact $contact, string $salesChannelIdentifier): void
    {
        $client = $this->dotdigitalClientFactory
            ->createV3Client($salesChannelIdentifier)
            ->getClient();

        try {
            $client->contacts->patchByIdentifier(
                $contact->getIdentifiers()->getEmail(),
                $contact
            );
        } catch (\Exception $exception) {
            $this->logger->error(
                'Dotdigital client request error',
                ['exception' => $exception]
            );
        }
    }

    /**
     * Get consent text from system config
     */
    private function getEscapedConsentText(): string
    {
        return strip_tags($this->getConsentText());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getConsent(): array
    {
        return [
            [
                'text' => $this->getEscapedConsentText(),
                'dateTimeConsented' => date(\DateTimeInterface::ATOM),
                'url' => $_SERVER['HTTP_REFERER'] ?? '',
                'ipAddress' => $_SERVER['REMOTE_ADDR'] ?? '',
                'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ],
        ];
    }
}
