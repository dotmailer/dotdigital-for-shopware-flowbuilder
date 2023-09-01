<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Setting;

class Defaults
{
    /**
     * General default values
     * We have use undefined as Unknown is a valid status in EC
     *
     * @var string
     */
    public const DEFAULT_UNDEFINED_VALUE = 'undefined';
    public const DEFAULT_DATETIME_VALUE = '0000-00-00 00:00:00';
    public const DEFAULT_NUMERIC_VALUE = 0;
}
