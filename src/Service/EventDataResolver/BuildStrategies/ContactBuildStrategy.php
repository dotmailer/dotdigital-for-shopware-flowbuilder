<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Dotdigital\Flow\Core\Framework\DataTypes\ContactCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactStruct;
use Dotdigital\Flow\Service\RecipientResolver;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;

class ContactBuildStrategy implements BuildStrategyInterface
{
    private RecipientResolver $recipientResolver;

    public function __construct(
        RecipientResolver $recipientResolver
    ) {
        $this->recipientResolver = $recipientResolver;
    }

    /**
     * @inheritDoc
     */
    public function build(StorableFlow $flow): ContactCollection
    {
		$flowData = $flow->getConfig();
        $contactCollection = new ContactCollection();

        $recipients = $this
            ->recipientResolver
            ->getRecipients($flowData['recipient'], $flow);

        foreach ($recipients as $recipient) {
            $contactCollection->add(
                (new ContactStruct())->setEmail($recipient->getEmail())
            );
        }

        return $contactCollection;
    }
}
