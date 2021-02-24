<?php

declare(strict_types=1);

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
     * Persist the given files onto disk.
     *
     * @param  array<File>  $files
     */
    public function write(array $files): void
    {
        FileSystem::delete($this->endpointConfig->targetPath());
        array_map([self::class, 'writeFile'], $files);
    }

    public static function writeFile(File $file): void
    {
        if (! file_exists($file->directory)) {
            \Safe\mkdir($file->directory, 0777, true);
        }

        \Safe\file_put_contents(
            $file->directory.'/'.$file->name,
            $file->content
        );
    }
}
