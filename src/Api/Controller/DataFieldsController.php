<?php declare(strict_types=1);

namespace Dotdigital\Flow\Api\Controller;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Shopware\Core\Framework\Routing\Annotation\Acl;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
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
     * @Acl({"sales_channel.editor"})
     */
    public function showDataFields(): JsonResponse
    {
        $dataFields = $this->dotdigitalClientFactory->createClient()->getDataFields();

        return new JsonResponse($this->dotdigitalCollectionToArray($dataFields));
    }
}
