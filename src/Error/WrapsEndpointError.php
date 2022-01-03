<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use GraphQL\Error\ClientAware;
use Spawnia\Sailor\Configuration;

/**
 * @mixin ClientAware
 */
trait WrapsEndpointError
{
    /**
     * Name of the endpoint this error orignated from.
     */
    public string $endpointName;

    public function isClientSafe(): bool
    {
        if (! isset($this->endpointName)) {
            return false;
        }

        return Configuration::endpoint($this->endpointName)
            ->errorsAreClientSafe();
    }

    /**
     * TODO remove when upgrading to graphql-php:15.
     */
    public function getCategory(): string
    {
        return 'sailor';
    }
}
