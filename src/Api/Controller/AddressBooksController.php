<?php declare(strict_types=1);

namespace Dotdigital\Flow\Api\Controller;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;
use Dotdigital\Flow\Service\Client\AbstractClient;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Shopware\Core\Framework\Routing\Annotation\Acl;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class AddressBooksController extends AbstractController
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
     *     "/api/dotdigital/address-books",
     *     name="api.action.dotdigital.address.books",
     *     methods={"GET"}
     * )
     * @Acl({"sales_channel.editor"})
     */
    public function showAddressBooks(): JsonResponse
    {
        $addressBooks = [];
        do {
            $addressBooksCollection = $this->dotdigitalClientFactory
                ->createClient()
                ->getAddressBooks(\count($addressBooks));

            $addressBooks = [
                ...$addressBooks,
                ...$this->dotdigitalCollectionToArray($addressBooksCollection),
            ];
        } while (\count($addressBooksCollection) === AbstractClient::SELECT_LIMIT);

        return new JsonResponse($addressBooks);
    }
}
