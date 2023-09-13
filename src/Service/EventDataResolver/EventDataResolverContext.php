<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver;

use Dotdigital\Flow\Service\EventDataResolver\BuildStrategies\BuildStrategyInterface;
use Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies\ValidateStrategyInterface;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\Struct\Collection;

class EventDataResolverContext implements ResolveContactInterface,
    ResolveContactDataFieldsInterface,
    ResolveCampaignInterface,
    ResolvePersonalisedValuesInterface,
    ResolveAddressBookInterface,
    ResolveProgramInterface
{
    private BuildStrategyInterface $buildStrategy;

    private ?ValidateStrategyInterface $validateStrategy;

    /**
     * @param EventDataResolverExceptionHandler $exceptionHandler
     */
    private EventDataResolverExceptionHandler $exceptionHandler;

    public function __construct(
        EventDataResolverExceptionHandler $exceptionHandler,
        BuildStrategyInterface $buildStrategy,
        ?ValidateStrategyInterface $validateStrategy = null
    ) {
        $this->exceptionHandler = $exceptionHandler;
        $this->setBuildStrategy($buildStrategy);
        $this->setValidateStrategy($validateStrategy);
    }

    /**
     * Set the build strategy
     *
     * @return $this
     */
    public function setBuildStrategy(BuildStrategyInterface $buildStrategy): self
    {
        $this->buildStrategy = $buildStrategy;

        return $this;
    }

    /**
     * Set validate strategy
     *
     * @return $this
     */
    public function setValidateStrategy(?ValidateStrategyInterface $validateStrategy): self
    {
        $this->validateStrategy = $validateStrategy;

        return $this;
    }

    /**
     * Resolve event data
     *
     * @throws \Exception
     */
    public function resolve(StorableFlow $flow): Collection
    {
        try {
            if ($this->validateStrategy !== null) {
                $this->validateStrategy->validate($flow);
            }

            return $this->buildStrategy->build($flow);
        } catch (\Exception $exception) {
            $this->exceptionHandler->handle($exception);

            throw $exception;
        }
    }
}
