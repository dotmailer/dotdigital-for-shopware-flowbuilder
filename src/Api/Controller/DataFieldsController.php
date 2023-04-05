<?php declare(strict_types=1);

namespace Dotdigital\Flow\Api\Controller;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class DataFieldsController extends AbstractController
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
     *     "/api/dotdigital/data-fields",
     *     name="api.action.dotdigital.data.fields",
     *     methods={"GET"}
     * )
     */
    public function showDataFields(): JsonResponse
    {
        $dataFields = $this->dotdigitalClientFactory->createClient()->getDataFields();

        return new JsonResponse($this->dotdigitalCollectionToArray($dataFields));
    }
}
