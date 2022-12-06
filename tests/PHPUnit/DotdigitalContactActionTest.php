<?php declare(strict_types=1);

namespace Dotdigital\Tests;

use DG\BypassFinals;
use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalContactAction;
use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalEmailSenderAction;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\EventDataResolver\EventDataResolverContext;
use Dotdigital\Tests\Traits\InteractWithAddressBooksTrait;
use Dotdigital\Tests\Traits\InteractWithContactDataFieldsTrait;
use Dotdigital\Tests\Traits\InteractWithContactsTrait;
use Dotdigital\Tests\Traits\UtilitiesTrait;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Context\ContextSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;

class DotdigitalContactActionTest extends TestCase
{
    use UtilitiesTrait;
    use InteractWithContactDataFieldsTrait;
    use InteractWithContactsTrait;
    use InteractWithAddressBooksTrait;

    /**
     * @var DotdigitalEmailSenderAction
     */
    private $dotdigitalContactAction;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|MailAware
     */
    private $mailAwareMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|FlowEvent
     */
    private $eventMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Context
     */
    private $contextMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ContextSource
     */
    private $contextSourceMock;

    protected function setUp(): void
    {
        BypassFinals::enable();
        $dotdigitalClientFactoryMock = $this->createMock(DotdigitalClientFactory::class);
        $this->mailAwareMock = $this->createMock(MailAware::class);
        $this->eventMock = $this->createMock(FlowEvent::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->contextSourceMock = $this->getMockBuilder(ContextSource::class)
            ->addMethods(['getSalesChannelId'])
            ->getMock();
        $this->eventContactResolverMock = $this->createMock(EventDataResolverContext::class);
        $this->eventAddressBookResolverMock = $this->createMock(EventDataResolverContext::class);
        $this->resolveContactDataFieldsMock = $this->createMock(EventDataResolverContext::class);
        $this->dotdigitalContactAction = new DotdigitalContactAction(
            $dotdigitalClientFactoryMock,
            $this->eventAddressBookResolverMock,
            $this->eventContactResolverMock,
            $this->resolveContactDataFieldsMock
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function testDotdigitalContactResubscribeHandler(): void
    {
        $this->eventContactResolverMock->expects(static::atLeastOnce())
            ->method('resolve')
            ->willReturn($this->generateContactCollection());

        $this->eventAddressBookResolverMock->expects(static::atLeastOnce())
            ->method('resolve')
            ->willReturn($this->generateAddressBookCollection());

        $this->resolveContactDataFieldsMock->expects(static::atLeastOnce())
            ->method('resolve')
            ->willReturn($this->generateContactDataFieldCollection());

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->mailAwareMock);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::atLeastOnce())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->contextSourceMock->expects(static::atLeastOnce())
            ->method('getSalesChannelId')
            ->willReturn('salesChannelId');

        $this->eventMock->expects(static::once())
            ->method('getConfig')
            ->willReturn([
                'recipient' => [
                    'type' => 'default',
                    'data' => [],
                ],
                'addressBook' => $this->generateInteger(),
                'resubscribe' => true,
                'contactOptIn' => 'Single',
                'dataFields' => $this->generateContactDataFieldArray(10),
            ]);

        $this->dotdigitalContactAction->handle($this->eventMock);
    }
}
