<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Doctrine\DBAL\Connection;
use Dotdigital\Flow\Core\Framework\DataTypes\RecipientCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\RecipientStruct;
use Dotdigital\Flow\Core\Framework\Event\DotdigitalEmailSenderAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\ContactForm\Event\ContactFormEvent;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Content\MailTemplate\Exception\MailEventConfigurationException;
use Shopware\Core\Framework\Adapter\Twig\Exception\StringTemplateRenderingException;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DotdigitalEmailSenderAction extends FlowAction
{
    private const RECIPIENT_CONFIG_ADMIN = 'admin';
    private const RECIPIENT_CONFIG_CUSTOM = 'custom';
    private const RECIPIENT_CONFIG_CONTACT_FORM_MAIL = 'contactFormMail';

    private DotdigitalClientFactory $dotdigitalClientFactory;

    private StringTemplateRenderer $stringTemplateRenderer;

    private BusinessEventEncoder $businessEventEncoder;

    private Connection $connection;

    private LoggerInterface $logger;

    /**
     * Constructor.
     */
    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory,
        StringTemplateRenderer $stringTemplateRenderer,
        BusinessEventEncoder $businessEventEncoder,
        Connection $connection,
        LoggerInterface $logger
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
        $this->stringTemplateRenderer = $stringTemplateRenderer;
        $this->businessEventEncoder = $businessEventEncoder;
        $this->connection = $connection;
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
        return [DotdigitalEmailSenderAware::class, MailAware::class];
    }

    /**
     * Handle Dotdigital action.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(FlowEvent $event): void
    {
        if (!$event->getEvent() instanceof MailAware) {
            throw new MailEventConfigurationException('Not an instance of MailAware', \get_class($event->getEvent()));
        }

        $eventConfig = $event->getConfig();

        if (empty($eventConfig['recipient'])) {
            throw new MailEventConfigurationException('The recipient value in the flow action configuration is missing.', \get_class($event));
        }

        try {
            $recipients = $this->getRecipients($eventConfig['recipient'], $event);
        } catch (\Exception $exception) {
            $this->logger->error(
                'Dotdigital recipients collection error',
                ['exception' => $exception]
            );

            return;
        }

        if (!\array_key_exists('recipient', $eventConfig) || $recipients->count() === 0) {
            return;
        }

        $availableData = $this->businessEventEncoder->encode($event->getEvent());
        $personalisedValues = [];
        foreach ($availableData as $key => $data) {
            $personalisedValues[] = [
                'name' => $key,
                'value' => $data,
            ];
        }
        $context = $event->getContext();
        /** @var SalesChannelContext $channelContext */
        $channelContext = $context->getSource();
        $this->dotdigitalClientFactory
            ->createClient($channelContext->getSalesChannelId())
            ->sendEmail($recipients, $eventConfig['campaignId'], $personalisedValues);
    }

    /**
     * Get Name.
     */
    public static function getName(): string
    {
        return 'action.create.dotdigital_mail_sender';
    }

    /**
     * @param array<string,mixed> $recipients
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function getRecipients(array $recipients, FlowEvent $event): RecipientCollection
    {
        /**
         * @var MailAware $mailEvent
         */
        $mailEvent = $event->getEvent();
        $collection = new RecipientCollection();

        switch ($recipients['type']) {
            /*
             * On custom return array values from data structure;
             */
            case self::RECIPIENT_CONFIG_CUSTOM:
                foreach (array_values($recipients['data']) as $recipient) {
                    $data = $this->businessEventEncoder->encode($event->getEvent());

                    try {
                        $collection->add(new RecipientStruct(
                            $this->stringTemplateRenderer->render(
                                $recipient,
                                $data,
                                $event->getContext()
                            )
                        ));
                    } catch (StringTemplateRenderingException $exception) {
                        $this->logger->error(
                            'Dotdigital template render error',
                            ['exception' => $exception]
                        );
                    }
                }

                break;

            /*
             * On admin return the admin email address.
             */
            case self::RECIPIENT_CONFIG_ADMIN:
                $admins = $this->connection->fetchAllAssociative(
                    'SELECT first_name, last_name, email FROM user WHERE admin = true'
                );
                foreach ($admins as $admin) {
                    $collection->add(new RecipientStruct($admin['email']));
                }

                break;

            /*
             * On contact form event return the email address from the event.
             */
            case self::RECIPIENT_CONFIG_CONTACT_FORM_MAIL:
                if (!$mailEvent instanceof ContactFormEvent) {
                    break;
                }
                $data = $mailEvent->getContactFormData();
                if (!\array_key_exists('email', $data)) {
                    break;
                }
                $collection->add(new RecipientStruct($data['email']));

                break;

            /*
             * By default pull keys(email) from MailRecipientStruct::class
             */
            default:
                foreach (array_keys($mailEvent->getMailStruct()->getRecipients()) as $recipient) {
                    $collection->add(new RecipientStruct($recipient));
                }

                break;
        }

        return $collection;
    }
}
