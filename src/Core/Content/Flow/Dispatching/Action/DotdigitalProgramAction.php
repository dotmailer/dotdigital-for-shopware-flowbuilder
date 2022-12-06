<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Dotdigital\Flow\Core\Framework\Event\DotdigitalProgramAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactDataFieldsInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveContactInterface;
use Dotdigital\Flow\Service\EventDataResolver\ResolveProgramInterface;
use GuzzleHttp\Exception\GuzzleException;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DotdigitalProgramAction extends FlowAction
{
    private DotdigitalClientFactory $dotdigitalClientFactory;

    private ResolveContactInterface $resolveContact;

    private ResolveContactDataFieldsInterface $resolveContactDataFields;

    private ResolveProgramInterface $resolveProgram;

    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory,
        ResolveContactInterface $resolveContact,
        ResolveContactDataFieldsInterface $resolveContactDataFields,
        ResolveProgramInterface $resolveProgram
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
        $this->resolveContact = $resolveContact;
        $this->resolveContactDataFields = $resolveContactDataFields;
        $this->resolveProgram = $resolveProgram;
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
    public function handle(FlowEvent $event): void
    {
        if (!$event->getEvent() instanceof MailAware) {
            throw new \Exception('Not an instance of MailAware', 422);
        }
        $contactCollection = $this->resolveContact->resolve($event);
        $dataFieldCollection = $this->resolveContactDataFields->resolve($event);
        $programCollection = $this->resolveProgram->resolve($event);
        $contactCollection->first()->setDataFields($dataFieldCollection->jsonSerialize());
        $context = $event->getContext();
        /** @var SalesChannelContext $channelContext */
        $channelContext = $context->getSource();

        $apiContact = $this->dotdigitalClientFactory
            ->createClient($channelContext->getSalesChannelId())
            ->createOrUpdateContact($contactCollection->first());

        $this->dotdigitalClientFactory
            ->createClient($channelContext->getSalesChannelId())
            ->enrolContactToProgram($apiContact, $programCollection->first());
    }

    public static function getName(): string
    {
        return 'action.create.dotdigital_program';
    }
}
