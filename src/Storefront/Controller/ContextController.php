<?php

namespace Dotdigital\Flow\Storefront\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/context", name="dotdigial.flow.context", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function getContext(Request $request, SalesChannelContext $context): Response
    {
        return new Response(json_encode([
			'default' => $this->getDefaultCountry( $context ),
	        'allowed' => array_values($this->getAllowedCountries( $context ))
        ]));
    }

	/**
	 * Get default country
	 *
	 * @param SalesChannelContext $context
	 * @return array
	 */
	private function getDefaultCountry( SalesChannelContext $context ): array
	{
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
	 * @param SalesChannelContext $context
	 * @return array
	 */
	private function getAllowedCountries(SalesChannelContext $context ): array
	{
        $detailedSalesChannel = $this->salesChannelRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('id', $context->getSalesChannel()->getId()))
                ->addAssociation('countries')
                ->addAssociation('country')
            ,
            $context->getContext()
        )->first();

        $countries = $detailedSalesChannel->getCountries()->getElements();

        return array_map(function ($country){
            return $country->getIso();
        }, $countries);
	}
}
