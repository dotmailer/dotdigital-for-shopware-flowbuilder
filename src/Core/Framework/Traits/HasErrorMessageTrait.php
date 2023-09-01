<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\Traits;

trait HasErrorMessageTrait
{
    /**
     * @var array<string>
     */
    private $_messages = [];

    public function pushErrorMessage(string $message): void
    {
        $this->_messages[] = $message;
    }
}
