<?php declare(strict_types=1);

namespace Spawnia\Sailor;

interface BelongsToEndpoint
{
    /**
     * The configured endpoint this class belongs to.
     */
    public static function endpoint(): string;
}
