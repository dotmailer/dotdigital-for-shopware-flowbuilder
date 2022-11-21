<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Dotdigital\Flow\Core\Framework\DataTypes\ContactDataCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactDataStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactStruct;
use Dotdigital\Flow\Core\Framework\DataTypes\ProgramStruct;
use Dotdigital\Flow\Core\Framework\Event\DotdigitalProgramAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\RecipientResolver;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DotdigitalProgramAction extends FlowAction
{
    private DotdigitalClientFactory $dotdigitalClientFactory;

    private RecipientResolver $recipientResolver;

    private LoggerInterface $logger;

    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory,
        RecipientResolver $recipientResolver,
        LoggerInterface $logger
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
        $this->recipientResolver = $recipientResolver;
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
    public function handle(FlowEvent $event): void
    {
        if (!$event->getEvent() instanceof MailAware) {
            throw new \Exception('Not an instance of MailAware', 422);
        }

        $context = $event->getContext();
        /** @var SalesChannelContext $channelContext */
        $channelContext = $context->getSource();
        $eventContact = $this->buildAndValidateContactStruct($event);
        $eventProgram = $this->buildAndValidateProgramStruct($event);

        if ($eventContact) {
            $apiContact = $this->dotdigitalClientFactory->createClient($channelContext->getSalesChannelId())
                ->createOrUpdateContact($eventContact);

            $this->dotdigitalClientFactory
                ->createClient($channelContext->getSalesChannelId())
                ->enrolContactToProgram($apiContact, $eventProgram);
        }
    }

    public static function getName(): string
    {
        return 'action.create.dotdigital_program';
    }

    /**
     * Build data fields.
     */
    private function buildAndValidateContactDataFieldCollection(FlowEvent $event): ContactDataCollection
    {
        $eventData = $event->getConfig();
        $dataFieldCollection = new ContactDataCollection();
        if (!\array_key_exists('dataFields', $eventData)) {
            throw new \InvalidArgumentException('The dataFields value in the flow action configuration is invalid or missing.', 422);
        }
        foreach ($eventData['dataFields'] as $dataField) {
            $dataFieldCollection->add(
                new ContactDataStruct(
                    $dataField['key'],
                    $dataField['value']
                )
            );
        }

        return $dataFieldCollection;
    }

    /**
     * Build contact struct.
     */
    private function buildAndValidateContactStruct(FlowEvent $event): ?ContactStruct
    {
        $eventData = $event->getConfig();

        if (!\array_key_exists('recipient', $eventData)) {
            throw new \InvalidArgumentException('The contactEmail value in the flow action configuration is invalid or missing.', 422);
        }

        try {
            $recipient = $this
                ->recipientResolver
                ->getRecipients($eventData['recipient'], $event)
                ->first();
        } catch (\Exception $exception) {
            $this->logger->error(
                'Dotdigital recipients collection error',
                ['exception' => $exception]
            );

            return null;
        }

        $dataFields = $this->buildAndValidateContactDataFieldCollection($event)->reduce(
            function ($list, ContactDataStruct $contactDataStruct) {
                $list[] = [
                    'key' => $contactDataStruct->getKey(),
                    'value' => $contactDataStruct->getValue(),
                ];

                return $list;
            },
            []
        );

        return (new ContactStruct())
            ->setEmail($recipient->getEmail())
            ->setDataFields($dataFields);
    }

    /**
     * Build program struct.
     */
    private function buildAndValidateProgramStruct(FlowEvent $event): ProgramStruct
    {
        $eventData = $event->getConfig();
        if (!\array_key_exists('programId', $eventData)) {
            throw new \InvalidArgumentException('The program value in the flow action configuration is invalid or missing.', 422);
        }

        return new ProgramStruct((int) $eventData['programId']);
    }
}
