<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Storefront\Page\SmsConsent;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\PageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;

class SmsConsentPageLoadedEvent extends PageLoadedEvent
{
    /**
     * @var SmsConsentPage
     */
    private $page;

    public function __construct(SmsConsentPage $page, SalesChannelContext $salesChannelContext, Request $request)
    {
        $this->page = $page;
        parent::__construct($salesChannelContext, $request);
    }

    public function getPage(): SmsConsentPage
    {
        return $this->page;
    }
}
