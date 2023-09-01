<?php
declare(strict_types=1);

namespace Dotdigital\Flow;

use Composer\Autoload\ClassLoader;
use Shopware\Core\Framework\Parameter\AdditionalBundleParameters;
use Shopware\Core\Framework\Plugin;

class DotdigitalFlow extends Plugin
{
    public function getAdditionalBundles(AdditionalBundleParameters $parameters): array
    {
        self::classLoader();

        return parent::getAdditionalBundles($parameters);
    }

    public static function classLoader(): void
    {
        $file = __DIR__ . '/../vendor/autoload.php';
        if (!is_file($file)) {
            return;
        }

        /** @noinspection UsingInclusionOnceReturnValueInspection */
        $classLoader = require_once $file;

        if (!$classLoader instanceof ClassLoader) {
            return;
        }

        $classLoader->unregister();
        $classLoader->register(false);
    }
}
