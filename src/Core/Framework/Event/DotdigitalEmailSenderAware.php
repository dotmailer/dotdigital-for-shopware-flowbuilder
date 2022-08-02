<?php

namespace Dotdigital\Flow\Core\Framework\Event;

use Shopware\Core\Framework\Event\FlowEventAware;
use Shopware\Core\Framework\Event\MailAware;

interface DotdigitalEmailSenderAware extends FlowEventAware, MailAware
{
}
