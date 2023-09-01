<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\Repository\Contracts;

interface DotdigitalIterableInterface
{
    public function getIteratorLimit(): int;

    public function getIterationUpperLimit(): int;
}
