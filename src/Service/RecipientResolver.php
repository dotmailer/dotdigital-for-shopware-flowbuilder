<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service;

use Doctrine\DBAL\Connection;
use Dotdigital\Flow\Core\Framework\DataTypes\RecipientCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\RecipientStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\ContactForm\Event\ContactFormEvent;
use Shopware\Core\Framework\Adapter\Twig\Exception\StringTemplateRenderingException;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;

class RecipientResolver
{
    private const RECIPIENT_CONFIG_ADMIN = 'admin';
    private const RECIPIENT_CONFIG_CUSTOM = 'custom';
    private const RECIPIENT_CONFIG_CONTACT_FORM_MAIL = 'contactFormMail';

    private StringTemplateRenderer $stringTemplateRenderer;

    private BusinessEventEncoder $businessEventEncoder;

    private Connection $connection;

    private LoggerInterface $logger;

    public function __construct(
        StringTemplateRenderer $stringTemplateRenderer,
        BusinessEventEncoder $businessEventEncoder,
        Connection $connection,
        LoggerInterface $logger
    ) {
        $this->stringTemplateRenderer = $stringTemplateRenderer;
        $this->businessEventEncoder = $businessEventEncoder;
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * @param array<string,mixed> $recipients
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRecipients(array $recipients, FlowEvent $event): RecipientCollection
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
