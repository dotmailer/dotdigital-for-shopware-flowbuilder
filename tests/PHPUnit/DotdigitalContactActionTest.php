<?php declare(strict_types=1);

namespace Dotdigital\Tests;

use DG\BypassFinals;
use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalContactAction;
use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalEmailSenderAction;
use Dotdigital\Flow\Core\Framework\DataTypes\RecipientCollection;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\RecipientResolver;
use Dotdigital\Tests\Traits\InteractWithContactDataFieldsTrait;
use Dotdigital\Tests\Traits\UtilitiesTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\ContactForm\Event\ContactFormEvent;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Api\Context\ContextSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;

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

    /**
     * @var RecipientResolver|RecipientResolver|\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\MockObject
     */
    private $recipientResolverMock;

    /**
     * @var RecipientCollection|RecipientCollection|\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\MockObject
     */
    private $recipientCollectionMock;

    protected function setUp(): void
    {
        BypassFinals::enable();
        $dotdigitalClientFactoryMock = $this->createMock(DotdigitalClientFactory::class);
        $this->mailAwareMock = $this->createMock(MailAware::class);
        $this->eventMock = $this->createMock(FlowEvent::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->businessEventLoaderMock = $this->createMock(BusinessEventEncoder::class);
        $this->stringTemplateRendereMock = $this->createMock(StringTemplateRenderer::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->recipientCollectionMock = $this->getMockBuilder(RecipientCollection::class)
            ->addMethods(['getEmail'])
            ->onlyMethods(['getElements'])
            ->disableOriginalConstructor()
            ->getMock();
        /* @phpstan-ignore-next-line */
        $this->contactFormEventMock = $this->createMock(ContactFormEvent::class);
        $this->contextSourceMock = $this->getMockBuilder(ContextSource::class)
            ->addMethods(['getSalesChannelId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->recipientResolverMock = $this->createMock(RecipientResolver::class);

        $this->dotdigitalContactAction = new DotdigitalContactAction(
            $dotdigitalClientFactoryMock,
            $this->businessEventLoaderMock,
            $this->stringTemplateRendereMock,
            $this->recipientResolverMock,
            $this->loggerMock
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
                'recipient' => [
                    'type' => 'default',
                    'data' => [],
                ],
                'addressBook' => $this->generateInteger(),
                'resubscribe' => true,
                'contactOptIn' => 'Single',
                'dataFields' => $this->generateContactDataFieldArray(10),
            ]);

        $this->eventMock->expects(static::once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->recipientResolverMock->expects(static::once())
            ->method('getRecipients')
            ->willReturn($this->recipientCollectionMock);

        $this->recipientCollectionMock->expects(static::once())
            ->method('getElements')
            ->willReturn([$this->recipientCollectionMock]);

        $this->recipientCollectionMock->expects(static::once())
            ->method('getEmail')
            ->willReturn('chaz@emailsim.io');

        $this->dotdigitalContactAction->handle($this->eventMock);
    }
}
