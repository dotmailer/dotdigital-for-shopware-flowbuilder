<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Dotdigital\Flow\Core\Framework\DataTypes\ContactDataCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactDataStruct;
use Shopware\Core\Framework\Adapter\Twig\Exception\StringTemplateRenderingException;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;

class ContactDataFieldsBuildStrategy implements BuildStrategyInterface
{
    private StringTemplateRenderer $stringTemplateRenderer;

    private BusinessEventEncoder $businessEventEncoder;

    public function __construct(
        StringTemplateRenderer $stringTemplateRenderer,
        BusinessEventEncoder $businessEventEncoder
    ) {
        $this->stringTemplateRenderer = $stringTemplateRenderer;
        $this->businessEventEncoder = $businessEventEncoder;
    }

    /**
     * @inheritDoc
     */
    public function build(FlowEvent $flowEvent): ContactDataCollection
    {
        $dataFieldCollection = new ContactDataCollection();
        $context = $flowEvent->getContext();
        $eventData = $flowEvent->getConfig();
        $availableData = $this->businessEventEncoder->encode($flowEvent->getEvent());
        foreach ($eventData['dataFields'] as $dataField) {
            try {
                $value = $this->stringTemplateRenderer->render(
                    $dataField['value'],
                    $availableData,
                    $context
                );
                $dataFieldCollection->add(
                    new ContactDataStruct(
                        $dataField['key'],
                        $value
                    )
                );
            } catch (StringTemplateRenderingException $exception) {
                $dataFieldCollection->pushErrorMessage(sprintf('Data field %s could not be rendered', $dataField['key']));

                continue;
            } catch (\Exception $exception) {
                throw new \RuntimeException('Error while building contact data fields', 0, $exception);
            }
        }

        return $dataFieldCollection;
    }
}
