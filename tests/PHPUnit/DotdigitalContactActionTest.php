<?php declare(strict_types=1);

namespace Dotdigital\Tests;

use DG\BypassFinals;
use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalContactAction;
use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalEmailSenderAction;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Tests\Traits\InteractWithContactDataFieldsTrait;
use Dotdigital\Tests\Traits\UtilitiesTrait;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\ContactForm\Event\ContactFormEvent;
use Shopware\Core\Framework\Api\Context\ContextSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;

class DotdigitalContactActionTest extends TestCase
{
    use UtilitiesTrait;
    use InteractWithContactDataFieldsTrait;

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
     * @var \PHPUnit\Framework\MockObject\MockObject|ContactFormEvent
     */
    private $contactFormEventMock;

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
        /* @phpstan-ignore-next-line */
        $this->contactFormEventMock = $this->createMock(ContactFormEvent::class);
        $this->contextSourceMock = $this->getMockBuilder(ContextSource::class)
            ->addMethods(['getSalesChannelId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->dotdigitalContactAction = new DotdigitalContactAction(
            $dotdigitalClientFactoryMock
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDotdigitalContactResubscribeHandler(): void
    {
        $this->eventMock->expects(static::atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->mailAwareMock);

        $this->eventMock->expects(static::once())
            ->method('getConfig')
            ->willReturn([
                'contactEmail' => $this->generateValidEmail(),
                'addressBook' => $this->generateInteger(),
                'resubscribe' => true,
                'contactOptIn' => 'Single',
                'contactDataFields' => $this->generateContactDataFieldArray(10),
            ]);

        $this->eventMock->expects(static::once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->dotdigitalContactAction->handle($this->eventMock);
    }
}
