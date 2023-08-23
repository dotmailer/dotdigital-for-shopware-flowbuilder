<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\Client;

use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ApiDataFieldCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ApiDataFieldStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\CampaignCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\CampaignStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactDataStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactPersonalisationCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ProgramCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ProgramEnrolmentStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ProgramStruct;
use Dotdigital\Flow\Setting\Settings;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class DotdigitalClient extends AbstractClient
{
    public const CONTACT_ENROLMENT_ENDPOINT = 'v2/programs/enrolments';
    public const RESUBSCRIBE_CONTACT_ENDPOINT = 'v2/contacts/resubscribe';
    public const RESUBSCRIBE_CONTACT_TO_ADDRESS_BOOK_ENDPOINT = 'v2/address-books/%s/contacts/resubscribe';
    public const ADD_CONTACT_ENDPOINT = '/v2/contacts/';
    public const ADD_CONTACT_TO_ADDRESS_BOOK_ENDPOINT = 'v2/address-books/%s/contacts';
    public const GET_ADDRESS_BOOKS_ENDPOINT = 'v2/address-books';
    public const EMAIL_TRIGGERED_CAMPAIGN_ENDPOINT = 'v2/email/triggered-campaign';
    public const GET_DATAFIELDS_ENDPOINT = 'v2/data-fields';
    public const GET_PROGRAMS_ENDPOINT = 'v2/programs';
    public const GET_CAMPAIGNS_ENDPOINT = 'v2/campaigns';

    private SystemConfigService $systemConfigService;

    private ?string $salesChannelId;

    /**
     * DotdigitalClient construct.
     */
    public function __construct(
        SystemConfigService $systemConfigService,
        LoggerInterface $logger,
        ?string $salesChannelId = null
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->salesChannelId = $salesChannelId;

        $client = new Guzzle([
            'base_uri' => $this->getBaseUrl(),
            'headers' => [
                'Accept' => 'text/plain',
                'Authorization' => 'Basic ' . $this->generateScopedAuthorizationToken(),
                'Content-Type' => 'application/json',
            ],
        ]);

        parent::__construct($client, $logger);
    }

    /**
     * Resubscribe contact to address book.
     *
     * @throws GuzzleException
     */
    public function resubscribeContactToAddressBook(
        ContactStruct $contact,
        AddressBookStruct $addressBook
    ): ?ContactStruct {
        $response = $this->post(
            sprintf(self::RESUBSCRIBE_CONTACT_TO_ADDRESS_BOOK_ENDPOINT, $addressBook->getId()),
            [
                'json' => [
                    'unsubscribedContact' => [
                        'email' => $contact->getEmail(),
                        'dataFields' => $contact->getDataFields()->reduce(function ($list, ContactDataStruct $dataField) {
                            $list[] = [
                                'key' => $dataField->getKey(),
                                'value' => $dataField->getValue(),
                            ];

                            return $list;
                        }, []),
                    ],
                ],
            ]
        );

        return ContactStruct::createFromResponse($response);
    }

    /**
     * Resubscribe contact
     *
     * @throws GuzzleException
     */
    public function resubscribeContact(
        ContactStruct $contact
    ): ?ContactStruct {
        $response = $this->post(
            self::RESUBSCRIBE_CONTACT_ENDPOINT,
            [
                'json' => [
                    'unsubscribedContact' => [
                        'email' => $contact->getEmail(),
                        'dataFields' => $contact->getDataFields()->reduce(function ($list, ContactDataStruct $dataField) {
                            $list[] = [
                                'key' => $dataField->getKey(),
                                'value' => $dataField->getValue(),
                            ];

                            return $list;
                        }, []),
                    ],
                ],
            ]
        );

        return ContactStruct::createFromResponse($response);
    }

    /**
     * Create or update contact and return struct
     *
     * @throws GuzzleException
     */
    public function createOrUpdateContact(ContactStruct $contact): ContactStruct
    {
        $response = $this->post(
            self::ADD_CONTACT_ENDPOINT,
            [
                'json' => [
                    'email' => $contact->getEmail(),
                    'dataFields' => $contact->getDataFields()->reduce(
                        function ($list, ContactDataStruct $dataField) {
                            $list[] = [
                                'key' => $dataField->getKey(),
                                'value' => $dataField->getValue(),
                            ];

                            return $list;
                        },
                        []
                    ),
                    ...(!$contact->attributeIsDefault('optInType')) ? ['optInType' => $contact->getOptInType()] : [],
                    ...(!$contact->attributeIsDefault('emailType')) ? ['emailType' => $contact->getEmailType()] : [],
                ],
            ]
        );

        return ContactStruct::createFromResponse($response);
    }

    /**
     * Add contact to address book
     *
     * @throws GuzzleException
     */
    public function addContactToAddressBook(
        ContactStruct $contact,
        AddressBookStruct $addressBook
    ): ?ContactStruct {
        $response = $this->post(
            sprintf(self::ADD_CONTACT_TO_ADDRESS_BOOK_ENDPOINT, $addressBook->getId()),
            [
                'json' => [
                    'email' => $contact->getEmail(),
                    'emailType' => $contact->getEmailType(),
                    'optInType' => $contact->getOptInType(),
                    'dataFields' => $contact->getDataFields()->reduce(function ($list, ContactDataStruct $dataField) {
                        $list[] = [
                            'key' => $dataField->getKey(),
                            'value' => $dataField->getValue(),
                        ];

                        return $list;
                    }, []),
                ],
            ]
        );

        return ContactStruct::createFromResponse($response);
    }

    /**
     * Enroll contact to program
     *
     * @throws GuzzleException
     */
    public function enrolContactToProgram(
        ContactStruct $contact,
        ProgramStruct $program
    ): ?ProgramEnrolmentStruct {
        $response = $this->post(
            self::CONTACT_ENROLMENT_ENDPOINT,
            [
                'json' => [
                    'contacts' => [$contact->getId()],
                    'programId' => $program->getId(),
                ],
            ]
        );

        return ProgramEnrolmentStruct::createFromResponse($response);
    }

    /**
     * Get Collection of address books.
     *
     * @throws GuzzleException
     */
    public function getAddressBooks(int $skip = 0, int $take = 1000): AddressBookCollection
    {
	    $addressBooksResponse = $this->get(
		    sprintf(
			    '%s?select=%s&skip=%s',
			    self::GET_ADDRESS_BOOKS_ENDPOINT,
			    $take,
			    $skip
		    ),
		    []
	    );
	    $addressBooks = new AddressBookCollection();
	    foreach ($addressBooksResponse as $addressBook) {
		    $struct = new AddressBookStruct(
			    $addressBook['id'],
			    $addressBook['name'],
			    $addressBook['visibility'],
			    $addressBook['contacts']
		    );

		    $addressBooks->add($struct);
	    }

	    return $addressBooks;
    }

    /**
     * Get Collection of DataFields.
     *
     * @throws GuzzleException
     */
    public function getDataFields(): ApiDataFieldCollection
    {
        $dataFieldsResponse = $this->get(self::GET_DATAFIELDS_ENDPOINT, []);
        $dataFields = new ApiDataFieldCollection();
        foreach ($dataFieldsResponse as $dataField) {
            $struct = new ApiDataFieldStruct(
                $dataField['name'],
                $dataField['type'],
                $dataField['visibility'],
                $dataField['defaultValue']
            );
            $dataFields->add($struct);
        }

        return $dataFields;
    }

    /**
     * Get Collection of Programs.
     *
     * @throws GuzzleException
     */
    public function getPrograms(int $skipLimit = 0): ProgramCollection
    {
        $programsResponse = $this->get(
            sprintf(
                '%s?select=%s&skip=%s',
                self::GET_PROGRAMS_ENDPOINT,
                self::SELECT_LIMIT,
                $skipLimit
            ),
            []
        );
        $programs = new ProgramCollection();
        foreach ($programsResponse as $program) {
            $struct = ProgramStruct::createFromResponse($program);
            $programs->add($struct);
        }

        return $programs;
    }

    /**
     * Send email triggered campaign
     *
     * @throws GuzzleException
     */
    public function sendEmail(
        ContactCollection $contactCollection,
        CampaignStruct $campaign,
        ContactPersonalisationCollection $contactPersonalisationCollection
    ): void {
        $body = [
            'toAddresses' => $contactCollection->reduce(function (array $list, ContactStruct $contact) {
                $list[] = $contact->getEmail();

                return $list;
            }, []),
            'campaignId' => $campaign->getId(),
            'personalizationValues' => $contactPersonalisationCollection->jsonSerialize(),
        ];

        $payload = json_encode($body);
        $this->post(self::EMAIL_TRIGGERED_CAMPAIGN_ENDPOINT, ['body' => $payload]);
    }

    /**
     * Get Collection of Campaigns
     *
     * @throws GuzzleException
     */
    public function getCampaigns(int $skipLimit = 0): CampaignCollection
    {
        $campaignResponse = $this->get(
            sprintf(
                '%s?select=%s&skip=%s',
                self::GET_CAMPAIGNS_ENDPOINT,
                self::SELECT_LIMIT,
                $skipLimit
            ),
            []
        );
        $campaigns = new CampaignCollection();
        foreach ($campaignResponse as $campaign) {
            $struct = CampaignStruct::createFromResponse($campaign);
            $campaigns->add($struct);
        }

        return $campaigns;
    }

    /**
     * Get base url.
     */
    public function getBaseUrl(): string
    {
        $region = $this->systemConfigService->getString(
            Settings::HOST_REGION_CONFIG_KEY,
            $this->salesChannelId
        );
        $host = Settings::HOST;

        return "https://{$region}-{$host}";
    }

    /**
     * Generate Authorization token.
     */
    public function generateScopedAuthorizationToken(): string
    {
        $usernameConfigurationValue = $this->systemConfigService->getString(
            Settings::AUTHENTICATION_USERNAME_CONFIG_KEY,
            $this->salesChannelId
        );
        $passwordConfigurationValue = $this->systemConfigService->getString(
            Settings::AUTHENTICATION_PASSWORD_CONFIG_KEY,
            $this->salesChannelId
        );

        return base64_encode(
            "{$usernameConfigurationValue}:{$passwordConfigurationValue}"
        );
    }
}
