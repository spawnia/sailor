<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use GraphQL\Error\ClientAware;

/**
 * @mixin ClientAware
 */
trait WrapsEndpointError
{
    /**
     * Is it safe to display this error to clients?
     */
    public bool $isClientSafe = false;

    public function isClientSafe(): bool
    {
        return $this->isClientSafe;
    }

    /**
     * TODO remove when upgrading to graphql-php:15.
     */
    public function getCategory(): string
    {
        return 'sailor';
    }
}
