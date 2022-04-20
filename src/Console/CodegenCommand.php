<?php declare(strict_types=1);

namespace Spawnia\Sailor\Console;

use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Codegen\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodegenCommand extends Command
{
    use InteractsWithEndpoints;

    protected static $defaultName = 'codegen';

    protected function configure(): void
    {
        $this->setDescription('Generate code from your GraphQL files.');
        $this->configureEndpoints();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endpoints = $this->endpoints($input);

        $configFile = $this->configFile($input);

        foreach ($endpoints as $endpointName => $endpoint) {
            echo "Generating code for endpoint {$endpointName}...\n";

            $generator = new Generator($endpoint, $configFile, $endpointName);
            $files = $generator->generate();

            $writer = new Writer($endpoint);
            $writer->write($files);
        }

        echo "Successfully generated code, query ahead!\n";

        return 0;
    }
}
