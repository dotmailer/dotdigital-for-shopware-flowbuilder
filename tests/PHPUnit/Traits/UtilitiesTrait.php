<?php

namespace Dotdigital\Tests\Traits;

trait UtilitiesTrait
{
    /**
     * @param float $length
     * @return false|string
     */
    protected function generateRandomString(float $length = 10.0) {
        /* @phpstan-ignore-next-line */
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * @return string
     */
    protected function generateValidEmail():string
    {
        return $this->generateRandomString(5).'@'.$this->generateRandomString(5).'.test';
    }

    /**
     * @param int $from
     * @param int $to
     * @return int
     * @throws \Exception
     */
    protected function generateInteger(int $from = 1, int $to = 999999): int
    {
        return random_int($from, $to);
    }
}
