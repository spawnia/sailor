<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

class DirectoriesFinder implements Finder
{
    /** @var array<DirectoryFinder> */
    protected array $directoryFinders;

    /** @param array<DirectoryFinder> $directoryFinders */
    public function __construct(array $directoryFinders)
    {
        $this->directoryFinders = $directoryFinders;
    }

    public function documents(): array
    {
        $documents = [];
        foreach ($this->directoryFinders as $directory) {
            // Merge ensures uniqueness of the found paths
            $documents = array_merge($documents, $directory->documents());
        }

        return $documents;
    }
}
