<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use GraphQL\Error\ClientAware;
use Spawnia\Sailor\Configuration;

/**
 * @mixin ClientAware
 */
trait OriginatesFromEndpoint
{
    /** Path to the config file the endpoint is defined in. */
    public string $configFile;

    /** Name of the endpoint this error originates from. */
    public string $endpointName;

    public function isClientSafe(): bool
    {
        if (! isset($this->configFile, $this->endpointName)) {
            return false;
        }

        return Configuration::endpoint($this->configFile, $this->endpointName)
            ->errorsAreClientSafe();
    }

    /** TODO remove when upgrading to graphql-php:15. */
    public function getCategory(): string
    {
        return 'sailor';
    }
}
