<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Console;

use Nette\Utils\FileSystem;
use Spawnia\Sailor\Codegen\File;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodegenCommand extends Command
{
    protected static $defaultName = 'codegen';

    protected function configure(): void
    {
        $this->setDescription('Generate code from your GraphQL files.');
        $this->addArgument('endpoint', InputArgument::OPTIONAL, 'You may choose a specific endpoint. Uses all by default.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endpoint = $input->getArgument('endpoint');
        if ($endpoint !== null) {
            $endpointNames = (array) $endpoint;
        } else {
            $endpointNames = array_keys(Configuration::getEndpointConfigMap());
        }

        /** @var string $endpointName */
        foreach ($endpointNames as $endpointName) {
            $endpointConfig = Configuration::forEndpoint($endpointName);
            $generator = new Generator($endpointConfig, $endpointName);

            $files = $generator->generate();
            FileSystem::delete($endpointConfig->targetPath());
            array_map([self::class, 'writeFile'], $files);
        }

        return 0;
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
