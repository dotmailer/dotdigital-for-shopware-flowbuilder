<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\Repository;

use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Psr\Cache\InvalidArgumentException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DotdigitalEntitySearcher implements EntitySearcherInterface
{

	/** DotdigitalEntityReader constructor.
	*
	* @param ContainerInterface $container
	* @param DotdigitalClientFactory $clientFactory
	*/
	public function __construct(
		private ContainerInterface $container,
		private DotdigitalEntityResolver $entityResolver
	){}

	/**
	 * Fetch data from Dotdigital API and hydrate it into an EntityCollection
	 *
	 * @param EntityDefinition $definition
	 * @param Criteria $criteria
	 * @param Context $context
	 * @return IdSearchResult
	 * @throws InvalidArgumentException
	 */
	public function search(EntityDefinition $definition, Criteria $criteria, Context $context): IdSearchResult
	{

		$entityCollection = $this->entityResolver->fetch($definition, $criteria, $context);
		$data = [];

		foreach ($entityCollection->getIterator() as $entity) {
			$expression = sprintf("/%s/i", $criteria->getTerm());
			$getter = sprintf("get%s", ucfirst($definition->getSearchKeyword()));
			if (!preg_match($expression, call_user_func([$entity, $getter]))) {
				continue;
			}
			$data[] = [
				'primaryKey' => $entity->getId(),
				'data' => $entity->jsonSerialize()
			];
		}

		return new IdSearchResult(
			$entityCollection->count(),
			$data,
			$criteria,
			$context
		);
	}
}
