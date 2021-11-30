<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Console;

use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Codegen\Writer;
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
        if (null !== $endpoint) {
            $endpointNames = (array) $endpoint;
        } else {
            $endpointNames = array_keys(Configuration::endpoints());
        }

        /** @var string $endpointName */
        foreach ($endpointNames as $endpointName) {
            echo "Generating code for endpoint {$endpointName}...\n";

            $endpointConfig = Configuration::endpoint($endpointName);
            $generator = new Generator($endpointConfig, $endpointName);

            $files = $generator->generate();

            $writer = new Writer($endpointConfig);
            $writer->write($files);
        }

        echo "Successfully generated code, query ahead!\n";

        return 0;
    }
}
