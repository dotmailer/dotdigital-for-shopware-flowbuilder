<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\Repository;

use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\Repository\Contracts\DotdigitalDefinitionInterface;
use Dotdigital\Flow\Service\Repository\Contracts\DotdigitalIterableInterface;
use GuzzleHttp\Exception\GuzzleException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class DotdigitalEntityResolver
{
    public function __construct(
        private readonly TagAwareCacheInterface $cache,
        private readonly DotdigitalClientFactory $clientFactory
    ) {
    }

    /**
     * Fetch data from Dotdigital API or retrieve it from cache
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *
     * @return mixed
     */
    public function fetch(DotdigitalDefinitionInterface $definition, Criteria $criteria, Context $context)
    {
        return $this->cache->get($definition->getEntityName(), function (ItemInterface $item) use ($definition, $context, $criteria) {
            $result = $this->definitionApiCall($definition, $criteria, $context);
            $item->expiresAfter($definition->getCacheLifetime());
            $item->tag([
                $definition->getSearchKeyword(),
                $definition->getApiCall(),
                'dotdigital-entity',
            ]);

            return $result;
        });
    }

    /**
     * Call the Dotdigital API defined in the $definition results
     *
     * @throws GuzzleException
     *
     * @return mixed
     */
    private function definitionApiCall(DotdigitalDefinitionInterface $definition, Criteria $criteria, Context $context)
    {
        $client = $this->clientFactory->createClient();
        $definitionReflection = new \ReflectionClass($definition);
        if ($definitionReflection->implementsInterface(DotdigitalIterableInterface::class)) {
            $lists = null;
            $skip = 0;
            while (true) {
                /** @var DotdigitalIterableInterface $definition */
                $results = $client->getAddressBooks($skip, $definition->getIteratorLimit());
                $skip += $results->count();

                if (empty($lists)) {
                    $lists = $results;
                } else {
                    $lists->merge($results);
                }

                if ($results->count() < $definition->getIteratorLimit()) {
                    break;
                }

                if ($lists->count() >= $definition->getIterationUpperLimit()) {
                    break;
                }
            }

            return $lists;
        }

        // @phpstan-ignore-next-line-pattern 'Parameter #1 $callback of function call_user_func expects callable(): mixed, array{Dotdigital\Flow\Service\Client\DotdigitalClient, string} given.'
        return \call_user_func([$client, $definition->getApiCall()]);
    }
}
