<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\Traits;

trait InteractsWithResponseTrait
{
    /**
     * @var array
     */
    private $_messages = [];

    public static function createFromResponse(array $response): self
    {
        try {
            $reflector = new \ReflectionClass(static::class);
            $reflectorInstance = $reflector->newInstanceWithoutConstructor();
            if (empty($response)) {
                $reflectorInstance->_messages[] = 'Response body is empty';
            }
            if (\array_key_exists('message', $response)) {
                $reflectorInstance->_messages[] = $response['message'];
            }
            $properties = $reflector->getProperties();
            foreach ($properties as $property) {
                if (\array_key_exists($property->getName(), $response)) {
                    $reflectorInstance->assignProperty($property, $response[$property->getName()]);
                }
            }
        } catch (\ReflectionException $exception) {
            $reflectorInstance->_messages[] = $exception->getMessage();
        }

        return $reflectorInstance;
    }

    /**
     * @param $property
     * @param $value
     */
    private function assignProperty($property, $value): void
    {
        try {
            if ($property->isProtected() or $property->isPrivate()) {
                if (method_exists($this, 'set' . ucfirst($property->getName()))) {
                    $method = 'set' . ucfirst($property->getName());
                    $this->{'set' . ucfirst($property->getName())}($value);
                } else {
                    $this->_messages[] = 'The property (' . $property->getName() . ') of class ( ' . static::class . ' ) can not be assigned!';
                }
            } else {
                $this->{$property} = $value;
            }
        } catch (\ReflectionException $exception) {
            $this->_messages[] = $exception->getMessage();
        }
    }
}
