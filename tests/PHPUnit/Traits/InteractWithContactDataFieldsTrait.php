<?php declare(strict_types=1);

namespace Dotdigital\Tests\Traits;

trait InteractWithContactDataFieldsTrait
{
    use UtilitiesTrait;

    protected function generateContactDataFieldArray(int $count = 1): array
    {
        $contactDataFields = [];
        for ($i = 0; $i < $count; ++$i) {
            $contactDataFields[] = [
                'key' => $this->generateRandomString(),
                'value' => $this->generateRandomString(),
            ];
        }

        return $contactDataFields;
    }
}
