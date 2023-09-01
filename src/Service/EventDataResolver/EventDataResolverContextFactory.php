<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver;

use Dotdigital\Flow\Service\EventDataResolver\BuildStrategies\AddressBookBuildStrategy;
use Dotdigital\Flow\Service\EventDataResolver\BuildStrategies\CampaignBuildStrategy;
use Dotdigital\Flow\Service\EventDataResolver\BuildStrategies\ContactBuildStrategy;
use Dotdigital\Flow\Service\EventDataResolver\BuildStrategies\ContactDataFieldsBuildStrategy;
use Dotdigital\Flow\Service\EventDataResolver\BuildStrategies\PersonalisedValuesBuildStrategy;
use Dotdigital\Flow\Service\EventDataResolver\BuildStrategies\ProgramBuildStrategy;
use Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies\AddressBookValidateStrategy;
use Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies\CampaignValidateStrategy;
use Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies\ContactDataFieldsValidateStrategy;
use Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies\ContactValidateStrategy;
use Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies\ProgramValidateStrategy;

class EventDataResolverContextFactory
{
    private EventDataResolverExceptionHandler $exceptionHandler;

    private PersonalisedValuesBuildStrategy $personalisedValuesBuildStrategy;

    private ContactBuildStrategy $contactBuildStrategy;

    private ContactValidateStrategy $contactValidateStrategy;

    private ContactDataFieldsValidateStrategy $contactDataFieldsValidateStrategy;

    private ContactDataFieldsBuildStrategy $contactDataFieldsBuildStrategy;

    private CampaignBuildStrategy $campaignBuildStrategy;

    private CampaignValidateStrategy $campaignValidateStrategy;

    private AddressBookBuildStrategy $addressBookBuildStrategy;

    private AddressBookValidateStrategy $addressBookValidateStrategy;

    private ProgramBuildStrategy $programBuildStrategy;

    private ProgramValidateStrategy $programValidateStrategy;

    public function __construct(
        EventDataResolverExceptionHandler $exceptionHandler,
        PersonalisedValuesBuildStrategy $personalisedValuesBuildStrategy,
        ContactBuildStrategy $contactBuildStrategy,
        ContactValidateStrategy $contactValidateStrategy,
        ContactDataFieldsBuildStrategy $contactDataFieldsBuildStrategy,
        ContactDataFieldsValidateStrategy $contactDataFieldsValidateStrategy,
        CampaignBuildStrategy $campaignBuildStrategy,
        CampaignValidateStrategy $campaignValidateStrategy,
        AddressBookBuildStrategy $addressBookBuildStrategy,
        AddressBookValidateStrategy $addressBookValidateStrategy,
        ProgramBuildStrategy $programBuildStrategy,
        ProgramValidateStrategy $programValidateStrategy
    ) {
        $this->exceptionHandler = $exceptionHandler;
        $this->personalisedValuesBuildStrategy = $personalisedValuesBuildStrategy;
        $this->contactBuildStrategy = $contactBuildStrategy;
        $this->contactValidateStrategy = $contactValidateStrategy;
        $this->contactDataFieldsBuildStrategy = $contactDataFieldsBuildStrategy;
        $this->contactDataFieldsValidateStrategy = $contactDataFieldsValidateStrategy;
        $this->campaignBuildStrategy = $campaignBuildStrategy;
        $this->campaignValidateStrategy = $campaignValidateStrategy;
        $this->addressBookBuildStrategy = $addressBookBuildStrategy;
        $this->addressBookValidateStrategy = $addressBookValidateStrategy;
        $this->programBuildStrategy = $programBuildStrategy;
        $this->programValidateStrategy = $programValidateStrategy;
    }

    /**
     * Build a resolver for contact
     */
    public function createContactResolver(): EventDataResolverContext
    {
        return new EventDataResolverContext(
            $this->exceptionHandler,
            $this->contactBuildStrategy,
            $this->contactValidateStrategy
        );
    }

    /**
     * Build a resolver for contact data fields
     */
    public function createContactDataFieldsResolver(): EventDataResolverContext
    {
        return new EventDataResolverContext(
            $this->exceptionHandler,
            $this->contactDataFieldsBuildStrategy,
            $this->contactDataFieldsValidateStrategy
        );
    }

    /**
     * Build a resolver for campaign
     */
    public function createCampaignResolver(): EventDataResolverContext
    {
        return new EventDataResolverContext(
            $this->exceptionHandler,
            $this->campaignBuildStrategy,
            $this->campaignValidateStrategy
        );
    }

    /**
     * Build a resolver for address book
     */
    public function createPersonalisedValuesResolver(): EventDataResolverContext
    {
        return new EventDataResolverContext(
            $this->exceptionHandler,
            $this->personalisedValuesBuildStrategy
        );
    }

    /**
     * Build a resolver for address book
     */
    public function createAddressBookResolver(): EventDataResolverContext
    {
        return new EventDataResolverContext(
            $this->exceptionHandler,
            $this->addressBookBuildStrategy,
            $this->addressBookValidateStrategy
        );
    }

    /**
     * Build a resolver for program
     */
    public function createProgramResolver(): EventDataResolverContext
    {
        return new EventDataResolverContext(
            $this->exceptionHandler,
            $this->programBuildStrategy,
            $this->programValidateStrategy
        );
    }
}
