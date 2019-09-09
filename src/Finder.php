<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

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
    public function find()
    {
        $contents = [];
        /** @var \SplFileInfo $fileInfo */
        foreach ($this->fileIterator() as $fileInfo) {
            $path = $fileInfo->getRealPath();
            $contents[$path] = \Safe\file_get_contents($path);
        }

        return $contents;
    }

    /**
     * @return \RegexIterator
     */
    protected function fileIterator(): \RegexIterator
    {
        $directory = new \RecursiveDirectoryIterator($this->rootPath);
        $iterator = new \RecursiveIteratorIterator($directory);

        return new \RegexIterator($iterator, '/^.+\.graphql$/i', \RecursiveRegexIterator::MATCH);
    }
}
