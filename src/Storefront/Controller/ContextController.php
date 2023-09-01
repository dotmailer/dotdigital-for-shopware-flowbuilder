<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Storefront\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class ContextController extends StorefrontController
{
    private EntityRepository $countryRepository;

    private EntityRepository $salesChannelRepository;

    /**
     * @internal
     */
    public function __construct(EntityRepository $countryRepository, EntityRepository $salesChannelRepository)
    {
        $this->countryRepository = $countryRepository;
        $this->salesChannelRepository = $salesChannelRepository;
    }

    /**
     * @Route("/context", name="dotdigital.flow.context", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function getContext(Request $request, SalesChannelContext $context): Response
    {
        $responsePayload = json_encode([
            'default' => $this->getDefaultCountry($context),
            'allowed' => array_values($this->getAllowedCountries($context)),
        ]);

        return new Response(!empty($responsePayload) ? $responsePayload : null);
    }

    /**
     * Get default country
     *
     * @return mixed
     */
    private function getDefaultCountry(SalesChannelContext $context)
    {
        /** @phpstan-ignore-next-line-pattern 'Cannot call method getIso()' */
        $defaultCountry[] = $this->countryRepository
            ->search(
                (new Criteria())
                    ->addFilter(new EqualsFilter('id', $context->getSalesChannel()->getCountryId())),
                $context->getContext()
            )
            ->getEntities()
            ->first()
            ->getIso();

        return $defaultCountry;
    }

    /**
     * Get allowed countries
     *
     * @return mixed
     */
    private function getAllowedCountries(SalesChannelContext $context)
    {
        $detailedSalesChannel = $this->salesChannelRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('id', $context->getSalesChannel()->getId()))
                ->addAssociation('countries')
                ->addAssociation('country'),
            $context->getContext()
        )->first();

        /** @phpstan-ignore-next-line-pattern 'Cannot call method getCountries()' */
        $countries = $detailedSalesChannel->getCountries()->getElements();

        return array_map(function ($country) {
            return $country->getIso();
        }, $countries);
    }
}
