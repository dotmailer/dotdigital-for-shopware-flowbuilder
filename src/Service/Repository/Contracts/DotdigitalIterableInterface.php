<?php

namespace Dotdigital\Flow\Service\Repository\Contracts;

interface DotdigitalIterableInterface
{
	public function getIteratorLimit(): int;

	public function getIterationUpperLimit(): int;
}
