<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Api\Controller;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;
use Dotdigital\Flow\Service\Client\AbstractClient;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class CampaignsController extends AbstractController
{
    use InteractsWithResponseTrait;

    private DotdigitalClientFactory $dotdigitalClientFactory;

    public function __construct(
        DotdigitalClientFactory $dotdigitalClientFactory
    ) {
        $this->dotdigitalClientFactory = $dotdigitalClientFactory;
    }

    /**
     * @Route(
     *     "/api/dotdigital/campaigns",
     *     name="api.action.dotdigital.campaigns",
     *     methods={"GET"}
     * )
     *
     * @throws GuzzleException
     */
    public function showCampaigns(): JsonResponse
    {
        $campaigns = [];
        do {
            $campaignsCollection = $this->dotdigitalClientFactory
                ->createClient()
                ->getCampaigns(\count($campaigns));

            $campaigns = [
                ...$campaigns,
                ...$this->dotdigitalCollectionToArray($campaignsCollection),
            ];
        } while (\count($campaignsCollection) === AbstractClient::SELECT_LIMIT);

        return new JsonResponse($campaigns);
    }
}
