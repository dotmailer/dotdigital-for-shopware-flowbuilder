<?php

namespace Dotdigital\Flow\Core\Content\Flow\Dispatching\Action;

use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;
use Dotdigital\Flow\Core\Framework\Event\DotdigitalEmailSenderAware;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DotdigitalEmailSenderAction extends FlowAction
{
    /**
     * @var DotdigitalClientFactory
     */
    private $dotdigitalClientFactory;

    /**
     * @var StringTemplateRenderer
     */
    private StringTemplateRenderer $stringTemplateRenderer;

    /**
     * @var BusinessEventEncoder
     */
    private BusinessEventEncoder $businessEventEncoder;

    /**
     * Constructor.
     *
     * @param DotdigitalClientFactory $dotdigitalClientFactory
     * @param StringTemplateRenderer $stringTemplateRenderer
     * @param BusinessEventEncoder $businessEventEncoder
     */
    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory,
        StringTemplateRenderer $stringTemplateRenderer,
        BusinessEventEncoder $businessEventEncoder
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
        $this->stringTemplateRenderer = $stringTemplateRenderer;
        $this->businessEventEncoder = $businessEventEncoder;
    }

    /**
     * Get subscribed events.
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
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
        return [DotdigitalEmailSenderAware::class];
    }

    /**
     * Handle Dotdigital action.
     *
     * @param FlowEvent $event
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(FlowEvent $event): void
    {
        $config = $event->getConfig();

        // make sure your required config data exists
        if (!\array_key_exists('recipient', $config) || !\array_key_exists('campaignId', $config)) {
            return;
        }

        $availableData = $this->businessEventEncoder->encode($event->getEvent());

        $personalisedValues = [];

        foreach ($availableData as $key => $data ) {
            $personalisedValues[] = [
                "name" => $key,
                "value" => $data
            ];
        }


        $context = $event->getContext();
        /** @var SalesChannelContext $channelContext */
        $channelContext = $context->getSource();
        $customerEmail = $this->stringTemplateRenderer->render($config["recipient"], $availableData, $context);
        $this->dotdigitalClientFactory
            ->createClient($channelContext->getSalesChannelId())
            ->sendEmail($customerEmail, $config["campaignId"], json_encode($personalisedValues));
    }

    /**
     * Get Name.
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'action.create.dotdigital_mail_sender';
    }
}
