<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

class DirectoryFinder implements Finder
{
    protected string $rootPath;

    protected string $pattern;

    public function __construct(
        string $rootPath,
        string $pattern = '/^.+\.graphql$/'
    ) {
        $this->rootPath = $rootPath;
        $this->pattern = $pattern;
    }

    public function documents(): array
    {
        $contents = [];
        foreach ($this->fileIterator() as $fileInfo) {
            assert($fileInfo instanceof \SplFileInfo);

            $path = $fileInfo->getRealPath();
            assert(is_string($path), 'We know this file exists, since it was found in search');

            // When installing from source, the examples might end up in the critical path,
            // so we exclude them from the search.
            if (false !== mb_strpos($path, 'vendor/spawnia/sailor/')) {
                continue;
            }

            $contents[$path] = \Safe\file_get_contents($path);
        }

        return $contents;
    }

    // @phpstan-ignore-next-line not providing generic type of RegexIterator
    protected function fileIterator(): \RegexIterator
    {
        if (! \file_exists($this->rootPath)) {
            \Safe\mkdir($this->rootPath, 0777, true);
        }

        $directory = new \RecursiveDirectoryIterator($this->rootPath);
        $iterator = new \RecursiveIteratorIterator($directory);

        return new \RegexIterator($iterator, $this->pattern, \RecursiveRegexIterator::MATCH);
    }
}
