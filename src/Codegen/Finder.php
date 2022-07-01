<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

interface Finder
{
    /**
     * Return a map from the paths/names of GraphQL documents to their contents.
     *
     * @return array<string, string>
     */
    public function documents(): array;
}
