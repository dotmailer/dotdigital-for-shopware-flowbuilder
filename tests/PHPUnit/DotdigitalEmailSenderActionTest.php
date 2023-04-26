<?php declare(strict_types=1);

namespace Dotdigital\Tests;

use DG\BypassFinals;
use Doctrine\DBAL\Connection;
use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalEmailSenderAction;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\EventDataResolver\EventDataResolverContext;
use Dotdigital\Flow\Service\RecipientResolver;
use Dotdigital\Tests\Traits\InteractWithCampaignsTrait;
use Dotdigital\Tests\Traits\InteractWithContactPersonalisationTrait;
use Dotdigital\Tests\Traits\InteractWithContactsTrait;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Context\ContextSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;

class DotdigitalEmailSenderActionTest extends TestCase
{
    use InteractWithCampaignsTrait;
    use InteractWithContactPersonalisationTrait;
    use InteractWithContactsTrait;

    /**
     * @var DotdigitalEmailSenderAction
     */
    private $dotdigitalSenderAction;

    /**
     * @var DotdigitalClientFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dotdigitalClientFactoryMock;

    /**
     * @var Connection|\PHPUnit\Framework\MockObject\MockObject
     */
    private $connectionMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|MailRecipientStruct
     */
    private $mailRecipientStructMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|StorableFlow
     */
    private $flowMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Context
     */
    private $contextMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ContextSource
     */
    private $contextSourceMock;

	/**
	 * @var RecipientResolver|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $recipientResolverMock;

	/**
	 * @var EventDataResolverContext|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $eventContactResolverMock;

	/**
	 * @var EventDataResolverContext|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $eventCampaignResolverMock;

	/**
	 * @var EventDataResolverContext|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $eventPersonalisedValuesResolverMock;

    protected function setUp(): void
    {
        BypassFinals::enable();
        $this->dotdigitalClientFactoryMock = $this->createMock(DotdigitalClientFactory::class);
        $this->mailRecipientStructMock = $this->createMock(MailRecipientStruct::class);
        $this->flowMock = $this->createMock(StorableFlow::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->recipientResolverMock = $this->createMock(RecipientResolver::class);
        /* @phpstan-ignore-next-line */
        $this->contextSourceMock = $this->getMockBuilder(ContextSource::class)
            ->addMethods(['getSalesChannelId'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventContactResolverMock = $this->createMock(EventDataResolverContext::class);
        $this->eventCampaignResolverMock = $this->createMock(EventDataResolverContext::class);
        $this->eventPersonalisedValuesResolverMock = $this->createMock(EventDataResolverContext::class);

        $this->eventCampaignResolverMock->expects(static::atLeastOnce())
            ->method('resolve')
            ->willReturn($this->generateCampaignCollection());

        $this->eventContactResolverMock->expects(static::atLeastOnce())
            ->method('resolve')
            ->willReturn($this->generateContactCollection());

        $this->eventPersonalisedValuesResolverMock->expects(static::atLeastOnce())
            ->method('resolve')
            ->willReturn($this->generateContactPersonalisationCollection());

        $this->dotdigitalSenderAction = new DotdigitalEmailSenderAction(
            $this->dotdigitalClientFactoryMock,
            $this->eventContactResolverMock,
            $this->eventCampaignResolverMock,
            $this->eventPersonalisedValuesResolverMock
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDotdigitalEmailSenderCustomerEmail(): void
    {
        $this->flowMock->expects(static::once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->dotdigitalSenderAction->handleFlow($this->flowMock);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDotdigitalEmailSenderContactFormEmail(): void
    {
        $this->flowMock->expects(static::once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->dotdigitalSenderAction->handleFlow($this->flowMock);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDotdigitalEmailSenderAdminEmail(): void
    {
        $this->flowMock->expects(static::once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->dotdigitalSenderAction->handleFlow($this->flowMock);
    }
}
