<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Storefront\Controller;

use Dotdigital\Flow\Core\Framework\DataTypes\SmsConsent\SmsConsentDataBag;
use Dotdigital\Flow\Service\Client\SmsConsentService;
use Dotdigital\Flow\Setting\Settings;
use Dotdigital\Flow\Storefront\Page\SmsConsent\SmsConsentPageLoader;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class SmsConsentCaptureController extends StorefrontController
{
    public function __construct(
        private SmsConsentPageLoader $smsConsentPageLoader,
        private SmsConsentService $smsConsentService,
        private SystemConfigService $systemConfigService
    ) {
    }

    #[Route(
        path: '/dotdigital/sms-consent',
        name: 'dotdigital.account.sms-consent',
        options: ['seo' => false],
        defaults: [
            '_loginRequired' => true,
            '_noStore' => true,
            'XmlHttpRequest' => true,
        ],
        methods: ['POST']
    )]
    public function save(Request $request, SalesChannelContext $context): Response
    {
        if ((bool) $this->systemConfigService->get(Settings::SHOW_ACCOUNT_SMS_CONSENT) !== true) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }
        $data = new SmsConsentDataBag($request->request->all());

        /**
         * @var CustomerEntity $customer
         */
        $customer = $context->getCustomer();
        $data->set('email', $customer->getEmail());
        $data->set('firstName', $customer->getFirstName());
        $data->set('lastName', $customer->getLastName());

        if (!$data->has('ddg_sms_subscribed_name')) {
            $this->smsConsentService->unSubscribe($data, $context->getSalesChannel()->getId());
            $this->addFlash('success', 'You have been unsubscribed from SMS marketing.');
        }

        if ($data->has('ddg_sms_subscribed_name')) {
            $this->smsConsentService->subscribe($data, $context->getSalesChannel()->getId());
            $this->addFlash('success', 'You have been subscribed to SMS marketing.');
        }

        return new Response('', Response::HTTP_OK);
    }

    #[Route(
        path: '/account/dotdigital/subscriptions',
        name: 'dotdigital.account.subscriptions',
        options: ['seo' => false],
        defaults: [
            '_loginRequired' => true,
            '_noStore' => true,
            'XmlHttpRequest' => true,
        ],
        methods: ['GET']
    )]
    public function view(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        if ((bool) $this->systemConfigService->get(Settings::SHOW_ACCOUNT_SMS_CONSENT) !== true) {
            return $this->redirectToRoute('frontend.account.home.page');
        }
        $page = $this->smsConsentPageLoader->load($request, $context, $customer);

        return $this->renderStorefront('@DotdigitalFlow/storefront/page/account/subscriptions.html.twig', ['page' => $page]);
    }
}
