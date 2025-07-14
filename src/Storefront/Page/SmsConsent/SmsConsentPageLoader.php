<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Storefront\Page\SmsConsent;

use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Setting\Settings;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class SmsConsentPageLoader
{
    public function __construct(
        private readonly GenericPageLoaderInterface $genericLoader,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly DotdigitalClientFactory $dotdigitalClientFactory,
        private readonly SystemConfigService $systemConfigService,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws \Dotdigital\Exception\ResponseValidationException
     * @throws \Dotdigital\Exception\ValidationException
     */
    public function load(Request $request, SalesChannelContext $salesChannelContext, CustomerEntity $customer): SmsConsentPage
    {
        $page = $this->genericLoader->load($request, $salesChannelContext);
        $page = SmsConsentPage::createFrom($page);

        $systemList = $this->systemConfigService->get(Settings::LIST);
        $salesChannelId = $salesChannelContext->getSalesChannel()->getId();

        try {
            $contact = $this->dotdigitalClientFactory
                ->createV3Client($salesChannelId)
                ->getClient()
                ->contacts
                ->getByIdentifier($customer->getEmail());
        } catch (\Dotdigital\Exception\ResponseValidationException|\Dotdigital\Exception\ValidationException $e) {
            $this->logger->debug(
                \sprintf('Error fetching contact %s', $customer->getEmail()),
                [$e]
            );
            $contact = null;
        }

        $page->setContact($contact);
        $page->setSystemAssignedList($systemList);
        $page->setCustomer($customer);
        $this->eventDispatcher->dispatch(
            new SmsConsentPageLoadedEvent($page, $salesChannelContext, $request)
        );

        return $page;
    }
}
