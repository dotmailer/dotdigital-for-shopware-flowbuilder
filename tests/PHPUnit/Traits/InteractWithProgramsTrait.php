<?php
declare(strict_types=1);

namespace Dotdigital\Tests\Traits;

use Dotdigital\Flow\Core\Framework\DataTypes\ProgramCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ProgramStruct;

trait InteractWithProgramsTrait
{
    use UtilitiesTrait;

    protected function generateProgramStruct(): ProgramStruct
    {
        return (new ProgramStruct($this->generateInteger()))
            ->setName($this->generateRandomString())
            ->setStatus($this->generateRandomString());
    }

    protected function generateProgramCollection(int $count = 1): ProgramCollection
    {
        $programCollection = new ProgramCollection();
        for ($i = 0; $i < $count; ++$i) {
            $programCollection->add($this->generateProgramStruct());
        }

        return $programCollection;
    }
}
