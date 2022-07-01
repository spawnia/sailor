<?php declare(strict_types=1);

namespace Spawnia\Sailor;

interface BelongsToEndpoint
{
    /**
     * Path to the config file of the endpoint.
     */
    public static function config(): string;

    /**
     * Name of the endpoint this class belongs to.
     */
    public static function endpoint(): string;
}
