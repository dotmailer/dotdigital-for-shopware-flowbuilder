<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Storefront\Controller;

use Dotdigital\Flow\Storefront\Page\SmsConsent\SmsConsentPageLoader;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
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
        private SmsConsentPageLoader $smsConsentPageLoader
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
        return new Response(null);
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
        $page = $this->smsConsentPageLoader->load($request, $context, $customer);

        return $this->renderStorefront('@DotdigitalFlow/storefront/page/account/subscriptions.html.twig', ['page' => $page]);
    }
}
