<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

class Finder
{
    /**
     * @var string
     */
    protected $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * Finds all GraphQL documents in a given path.
     *
     * @return string[]
     */
    public function documents(): array
    {
        $contents = [];
        /** @var \SplFileInfo $fileInfo */
        foreach ($this->fileIterator() as $fileInfo) {
            /** @var string $path We know this file exists, since it was found in search. */
            $path = $fileInfo->getRealPath();
            $contents[$path] = \Safe\file_get_contents($path);
        }

        return $contents;
    }

    protected function fileIterator(): \IteratorIterator
    {
        $directory = new \RecursiveDirectoryIterator($this->rootPath);
        $iterator = new \RecursiveIteratorIterator($directory);

        return new \RegexIterator(
            $iterator,
            // Look for all .graphql files
            // When installing from source, the examples might end up in the critical path
            // so we exclude them from the search
            '/^((?!vendor\/).).+\.graphql$/',
            \RecursiveRegexIterator::MATCH
        );
    }
}
