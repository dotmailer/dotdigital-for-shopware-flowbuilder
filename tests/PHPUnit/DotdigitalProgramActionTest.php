<?php declare(strict_types=1);

namespace Dotdigital\Tests;

use DG\BypassFinals;
use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalProgramAction;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ProgramCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ProgramStruct;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactDataFieldsInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveProgramInterface;
use Dotdigital\Flow\Service\RecipientResolver;
use Dotdigital\Tests\Traits\InteractWithContactDataFieldsTrait;
use Dotdigital\Tests\Traits\InteractWithContactsTrait;
use Dotdigital\Tests\Traits\InteractWithProgramsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Api\Context\ContextSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;

class DotdigitalProgramActionTest extends TestCase
{
    use InteractWithContactDataFieldsTrait;
    use InteractWithContactsTrait;
    use InteractWithProgramsTrait;

    /**
     * @var DotdigitalProgramAction
     */
    private $dotdigitalProgramAction;

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
     * @var ContactCollection|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contactCollectionMock;

    /**
     * @var ContactStruct|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contactStructMock;

    /**
     * @var ProgramCollection|\PHPUnit\Framework\MockObject\MockObject
     */
    private $programCollectionMock;

    /**
     * @var ProgramStruct|\PHPUnit\Framework\MockObject\MockObject
     */
    private $programStructMock;

    /**
     * @var ResolveContactInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $resolveContactMock;

    /**
     * @var ResolveContactDataFieldsInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $resolveContactDataFields;

    /**
     * @var ResolveProgramInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $resolveProgramMock;

	/**
	 * @var \PHPUnit\Framework\MockObject\MockObject|BusinessEventEncoder
	 */
	private $businessEventLoaderMock;

	/**
	 * @var \PHPUnit\Framework\MockObject\MockObject|StringTemplateRenderer
	 */
	private $stringTemplateRendererMock;

	/**
	 * @var \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
	 */
	private $loggerMock;

	/**
	 * @var ResolveContactDataFieldsInterface|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $resolveContactDataFieldsMock;

    protected function setUp(): void
    {
        BypassFinals::enable();
        $dotdigitalClientFactoryMock = $this->createMock(DotdigitalClientFactory::class);
		$this->flowMock = $this->createMock(StorableFlow::class);
		$this->contextMock = $this->createMock(Context::class);
        $this->businessEventLoaderMock = $this->createMock(BusinessEventEncoder::class);
        $this->stringTemplateRendererMock = $this->createMock(StringTemplateRenderer::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->contactCollectionMock = $this->createMock(ContactCollection::class);
        $this->contactStructMock = $this->createMock(ContactStruct::class);
        $this->programCollectionMock = $this->createMock(ProgramCollection::class);
        $this->programStructMock = $this->createMock(ProgramStruct::class);
        $this->recipientResolverMock = $this->createMock(RecipientResolver::class);
        $this->resolveContactMock = $this->createMock(ResolveContactInterface::class);
        $this->resolveContactDataFieldsMock = $this->createMock(ResolveContactDataFieldsInterface::class);
        $this->resolveProgramMock = $this->createMock(ResolveProgramInterface::class);

        $this->resolveContactMock->expects(static::once())
            ->method('resolve')
            ->willReturn($this->contactCollectionMock);

        $this->resolveProgramMock->expects(static::once())
            ->method('resolve')
            ->willReturn($this->programCollectionMock);

        $this->contactCollectionMock
            ->method('first')
            ->willReturn($this->contactStructMock);

        $this->programCollectionMock
            ->method('first')
            ->willReturn($this->programStructMock);

        $this->contextSourceMock = $this->getMockBuilder(ContextSource::class)
            ->addMethods(['getSalesChannelId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->dotdigitalProgramAction = new DotdigitalProgramAction(
            $dotdigitalClientFactoryMock,
            $this->resolveContactMock,
            $this->resolveContactDataFieldsMock,
            $this->resolveProgramMock
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDotdigitalProgramEnrolmentDefault(): void
    {
        $this->flowMock->expects(static::once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->dotdigitalProgramAction->handleFlow($this->flowMock);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDotdigitalProgramEnrolmentFromCustom(): void
    {
        $this->flowMock->expects(static::once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->dotdigitalProgramAction->handleFlow($this->flowMock);
    }
}
