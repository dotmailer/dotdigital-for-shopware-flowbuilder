<?php declare(strict_types=1);

namespace Dotdigital\Tests\Traits;

trait UtilitiesTrait
{
    /**
     * @return false|string
     */
    protected function generateRandomString(int $length = 10)
    {
        /* @phpstan-ignore-next-line */
        return substr(
            str_shuffle(
                str_repeat(
                    $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    (int) ceil($length / \strlen($x))
                )
            ),
            1,
            $length
        );
    }

    protected function generateValidEmail(): string
    {
        return $this->generateRandomString(5) . '@' . $this->generateRandomString(5) . '.test';
    }

    /**
     * @throws \Exception
     */
    protected function generateInteger(int $from = 1, int $to = 999999): int
    {
        return random_int($from, $to);
    }
}
