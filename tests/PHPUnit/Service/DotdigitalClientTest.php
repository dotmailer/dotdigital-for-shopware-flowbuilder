<?php

namespace Dotdigital\Tests\Service;

use Dotdigital\Flow\Service\Client\DotdigitalClient;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Dotdigital\Flow\Setting\Settings;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Dotdigital\Tests\Traits\InteractWithRecipientsTrait;


class DotdigitalClientTest extends TestCase
{
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
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockLogger = $this->createMock(Logger::class);
        $this->mockSystemConfig = $this->createMock(SystemConfigService::class);
        $this->mockSalesChannel = $this->createMock(SalesChannelEntity::class);
        $this->mockDotdigitalClient = $this->createMock(DotdigitalClient::class);
    }

    /**
     * @return void
     */
    public function testClientFactoryWithSalesChannel()
    {
        $factory = new DotdigitalClientFactory(
            $this->mockLogger,
            $this->mockSystemConfig
        );
        $client = $factory->createClient($this->mockSalesChannel->getId());
        $this->assertInstanceOf(DotdigitalClient::class,$client);
    }

    /**
     * @return void
     */
    public function testClientFactoryWithoutSalesChannel()
    {
        $factory = new DotdigitalClientFactory(
            $this->mockLogger,
            $this->mockSystemConfig
        );
        $client = $factory->createClient();
        $this->assertInstanceOf(DotdigitalClient::class,$client);
    }

    /**
     * @return void
     */
    public function testGetBaseUrlMethod()
    {
        $this->mockSystemConfig
            ->expects($this->atLeastOnce())
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

        $this->assertEquals(
            'https://mock-region-api.dotdigital.com',
            $client->getBaseUrl()
        );

    }

    /**
     * @return void
     */
    public function testAuthorizationTokenMethod()
    {
        $this->mockSystemConfig
            ->expects($this->atLeastOnce())
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

        $this->assertEquals(
            base64_encode( "mock-username:mock-password"),
            $client->generateScopedAuthorizationToken()
        );

    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testSendEmailMethod()
    {
        $templateID = 'mock-template-id';
        $recipients = $this->generateValidRecipientCollection();

        $this->mockDotdigitalClient->expects($this->atLeastOnce())
            ->method('sendEmail')
            ->with($recipients,$templateID);

        $this->mockDotdigitalClient->sendEmail(
            $recipients,
            $templateID,
            []
        );

    }

}
