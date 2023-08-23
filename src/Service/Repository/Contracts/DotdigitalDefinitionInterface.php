<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\Repository\Contracts;

interface DotdigitalDefinitionInterface
{
	public function getApiCall(): string;

	public function getEntityName(): string;

	public function getCollectionClass(): string;

	public function getEntityClass(): string;

	public function getSearchKeyword(): string;

	public function getCacheLifetime(): int;

}

