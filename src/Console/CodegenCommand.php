<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Console;

use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\Codegen\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodegenCommand extends Command
{
    protected static $defaultName = 'codegen';

    protected function configure()
    {
        $this->setDescription('Generate code from your GraphQL files.');
        $this->addArgument('endpoint', InputArgument::OPTIONAL, 'You may choose a specific endpoint. Uses all by default.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($endpoint = $input->getArgument('endpoint')) {
            $endpoints = [$endpoint];
        } else {
            $endpoints = array_keys(Configuration::getEndpointConfigMap());
        }

        foreach ($endpoints as $endpoint) {
            $generator = new Generator(
                Configuration::forEndpoint($endpoint),
                $endpoint
            );
            $generator->run();
        }
    }
}
