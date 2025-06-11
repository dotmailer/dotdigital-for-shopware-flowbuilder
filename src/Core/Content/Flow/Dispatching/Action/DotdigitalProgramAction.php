<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Dotdigital\Flow\Core\Framework\Event\DotdigitalProgramAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactDataFieldsInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveProgramInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\Event\MailAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DotdigitalProgramAction extends FlowAction implements EventSubscriberInterface
{
    private DotdigitalClientFactory $dotdigitalClientFactory;

    private ResolveContactInterface $resolveContact;

    private ResolveContactDataFieldsInterface $resolveContactDataFields;

    private ResolveProgramInterface $resolveProgram;

    private LoggerInterface $logger;

    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory,
        ResolveContactInterface $resolveContact,
        ResolveContactDataFieldsInterface $resolveContactDataFields,
        ResolveProgramInterface $resolveProgram,
        LoggerInterface $logger
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
        $this->resolveContact = $resolveContact;
        $this->resolveContactDataFields = $resolveContactDataFields;
        $this->resolveProgram = $resolveProgram;
        $this->logger = $logger;
    }

    /**
     * Get subscribed events.
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            self::getName() => 'handle',
        ];
    }

    /**
     * Requirements.
     *
     * @return string[]
     */
    public function requirements(): array
    {
        return [DotdigitalProgramAware::class, MailAware::class];
    }

    /**
     * Handle Dotdigital action.
     *
     * @throws GuzzleException|\InvalidArgumentException|\ReflectionException|\Exception
     */
    public function handleFlow(StorableFlow $flow): void
    {
        try {
            $contactCollection = $this->resolveContact->resolve($flow);
            $dataFieldCollection = $this->resolveContactDataFields->resolve($flow);
            $programCollection = $this->resolveProgram->resolve($flow);
            $contactCollection->first()->setDataFields($dataFieldCollection->jsonSerialize());
            $salesChannelId = $flow->getData('salesChannelId');

            $apiContact = $this->dotdigitalClientFactory
                ->createClient($salesChannelId)
                ->createOrUpdateContact($contactCollection->first());

            $this->dotdigitalClientFactory
                ->createClient($salesChannelId)
                ->enrolContactToProgram($apiContact, $programCollection->first());
        } catch (\Throwable $e) {
            $this->logger->error('DotdigitalProgramAction failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public static function getName(): string
    {
        return 'action.create.dotdigital_program';
    }
}
