<?php
declare(strict_types=1);

namespace Dotdigital\Tests\Service;

use DG\BypassFinals;
use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ProgramCollection;
use Dotdigital\Flow\Service\Client\DotdigitalClient;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Setting\Settings;
use Dotdigital\Tests\Traits\InteractWithAddressBooksTrait;
use Dotdigital\Tests\Traits\InteractWithCampaignsTrait;
use Dotdigital\Tests\Traits\InteractWithContactPersonalisationTrait;
use Dotdigital\Tests\Traits\InteractWithContactsTrait;
use Dotdigital\Tests\Traits\InteractWithRecipientsTrait;
use Dotdigital\Tests\Utility\PHPUnitClassUtility;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class DotdigitalClientTest extends TestCase
{
    use InteractWithAddressBooksTrait;
    use InteractWithCampaignsTrait;
    use InteractWithContactPersonalisationTrait;
    use InteractWithContactsTrait;
    use InteractWithRecipientsTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|SalesChannelEntity
     */
    private $mockSalesChannel;

    /**
     * @var DotdigitalClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mockDotdigitalClient;

    /**
     * @var Logger|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mockLogger;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|SystemConfigService
     */
    private $mockSystemConfig;

    /**
     * Setup test prerequisites
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockLogger = $this->createMock(Logger::class);
        $this->mockSystemConfig = $this->createMock(SystemConfigService::class);
        $this->mockSalesChannel = $this->createMock(SalesChannelEntity::class);
        $this->mockDotdigitalClient = $this->createMock(DotdigitalClient::class);
    }

    public function testClientFactoryWithSalesChannel(): void
    {
        $factory = new DotdigitalClientFactory(
            $this->mockLogger,
            $this->mockSystemConfig
        );
        $client = $factory->createClient($this->mockSalesChannel->getId());
        static::assertInstanceOf(DotdigitalClient::class, $client);
    }

    public function testClientFactoryWithoutSalesChannel(): void
    {
        $factory = new DotdigitalClientFactory(
            $this->mockLogger,
            $this->mockSystemConfig
        );
        $client = $factory->createClient();
        static::assertInstanceOf(DotdigitalClient::class, $client);
    }

    public function testGetBaseUrlMethod(): void
    {
        $this->mockSystemConfig
            ->expects(static::atLeastOnce())
            ->method('getString')
            ->withConsecutive(
                [Settings::HOST_REGION_CONFIG_KEY],
                [Settings::AUTHENTICATION_USERNAME_CONFIG_KEY],
                [Settings::AUTHENTICATION_PASSWORD_CONFIG_KEY],
                [Settings::HOST_REGION_CONFIG_KEY],
            )->willReturnOnConsecutiveCalls(
                'mock-region',
                'mock-username',
                'mock-password',
                'mock-region',
            );

        $factory = new DotdigitalClientFactory(
            $this->mockLogger,
            $this->mockSystemConfig
        );

        $client = $factory->createClient();

        static::assertEquals(
            'https://mock-region-api.dotdigital.com',
            PHPUnitClassUtility::callMethod($client, 'getApiEndpoint', [])
        );
    }

    public function testAuthorizationTokenMethod(): void
    {
        $this->mockSystemConfig
            ->expects(static::atLeastOnce())
            ->method('getString')
            ->withConsecutive(
                [Settings::HOST_REGION_CONFIG_KEY],
                [Settings::AUTHENTICATION_USERNAME_CONFIG_KEY],
                [Settings::AUTHENTICATION_PASSWORD_CONFIG_KEY],
                [Settings::AUTHENTICATION_USERNAME_CONFIG_KEY],
                [Settings::AUTHENTICATION_PASSWORD_CONFIG_KEY]
            )->willReturnOnConsecutiveCalls(
                'mock-region',
                'mock-username',
                'mock-password',
                'mock-username',
                'mock-password'
            );

        $factory = new DotdigitalClientFactory(
            $this->mockLogger,
            $this->mockSystemConfig
        );

        $client = $factory->createClient();

        static::assertEquals(
            base64_encode('mock-username:mock-password'),
            $client->generateScopedAuthorizationToken()
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testSendEmailMethod(): void
    {
        $contactCollection = $this->generateContactCollection();
        $campaign = $this->generateCampaign();
        $contactPersonalisationCollection = $this->generateContactPersonalisationCollection();

        $this->mockDotdigitalClient->expects(static::atLeastOnce())
            ->method('sendEmail')
            ->with($contactCollection, $campaign, $contactPersonalisationCollection);

        $this->mockDotdigitalClient->sendEmail(
            $contactCollection,
            $campaign,
            $contactPersonalisationCollection
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function testAddContactToAddressBookMethod(): void
    {
        BypassFinals::enable();
        $contact = $this->generateContact();
        $addressBook = $this->generateAddressBook();

        $this->mockDotdigitalClient->expects(static::atLeastOnce())
            ->method('addContactToAddressBook')
            ->with($contact, $addressBook);

        $this->mockDotdigitalClient->addContactToAddressBook(
            $contact,
            $addressBook
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function testResubscribeContactToAddressBookMethod(): void
    {
        BypassFinals::enable();
        $contact = $this->generateContact();
        $addresbook = $this->generateAddressBook();

        $this->mockDotdigitalClient->expects(static::atLeastOnce())
            ->method('resubscribeContactToAddressBook')
            ->with($contact, $addresbook);

        $this->mockDotdigitalClient->resubscribeContactToAddressBook(
            $contact,
            $addresbook
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function testResubscribeContactMethod(): void
    {
        BypassFinals::enable();
        $contact = $this->generateContact();

        $this->mockDotdigitalClient->expects(static::atLeastOnce())
            ->method('resubscribeContact')
            ->with($contact);

        $this->mockDotdigitalClient->resubscribeContact(
            $contact
        );
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function testCreateOrUpdateContactMethod(): void
    {
        BypassFinals::enable();
        $contact = $this->generateContact();

        $this->mockDotdigitalClient->expects(static::atLeastOnce())
            ->method('createOrUpdateContact')
            ->with($contact);

        $this->mockDotdigitalClient->createOrUpdateContact(
            $contact
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function testGetAddressBooksMethod(): void
    {
        BypassFinals::enable();
        $this->mockDotdigitalClient->expects(static::atLeastOnce())
            ->method('getAddressBooks');

        $response = $this->mockDotdigitalClient->getAddressBooks();
        static::assertInstanceOf(AddressBookCollection::class, $response);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function testProgramsMethod(): void
    {
        BypassFinals::enable();
        $this->mockDotdigitalClient->expects(static::atLeastOnce())
            ->method('getPrograms');

        $response = $this->mockDotdigitalClient->getPrograms();
        static::assertInstanceOf(ProgramCollection::class, $response);
    }
}
