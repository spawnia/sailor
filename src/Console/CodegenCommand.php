<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Console;

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
        if ($input->hasArgument('endpoint')) {
            $endpointNames = [$input->getArgument('endpoint')];
        } else {
            $endpointNames = array_keys(Configuration::getEndpointConfigMap());
        }

        /** @var string $endpointName */
        foreach ($endpointNames as $endpointName) {
            $generator = new Generator(
                Configuration::forEndpoint($endpointName),
                $endpointName
            );
            $generator->generate();
        }

        return 0;
    }
}
