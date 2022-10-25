<?php

namespace Dotdigital\Tests;

use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalContactAction;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalEmailSenderAction;
use Dotdigital\Tests\Traits\InteractWithContactDataFieldsTrait;
use Dotdigital\Tests\Traits\UtilitiesTrait;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Api\Context\ContextSource;
use Shopware\Core\Content\ContactForm\Event\ContactFormEvent;
use Doctrine\DBAL\Connection;
use DG\BypassFinals;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

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
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDotdigitalContactResubscribeHandler()
    {
        $this->eventMock->expects($this->atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->mailAwareMock);

        $this->eventMock->expects($this->once())
            ->method('getConfig')
            ->willReturn([
                'contactEmail' => $this->generateValidEmail(),
                'addressBook' => $this->generateInteger(),
                'resubscribe' => true,
                'contactOptIn' => 'Single',
                'contactDataFields' => $this->generateContactDataFieldArray(10),

            ]);

        $this->eventMock->expects($this->once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects($this->once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->dotdigitalContactAction->handle($this->eventMock);
    }


}
