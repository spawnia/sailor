<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use Nette\Utils\FileSystem;
use Spawnia\Sailor\EndpointConfig;

class Writer
{
    protected EndpointConfig $endpointConfig;

    public function __construct(EndpointConfig $endpointConfig)
    {
        $this->endpointConfig = $endpointConfig;
    }

    /**
     * Persist the given files to disk.
     *
     * @param iterable<File> $files
     */
    public function write(iterable $files): void
    {
        FileSystem::delete($this->endpointConfig->targetPath());

        foreach ($files as $file) {
            $this->writeFile($file);
        }
    }

    protected function writeFile(File $file): void
    {
        if (! file_exists($file->directory)) {
            \Safe\mkdir($file->directory, 0777, true);
        }

        \Safe\file_put_contents(
            "{$file->directory}/{$file->name}",
            $file->content
        );
    }
}
