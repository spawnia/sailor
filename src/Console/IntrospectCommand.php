<?php declare(strict_types=1);

namespace Spawnia\Sailor\Console;

use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\Introspector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IntrospectCommand extends Command
{
    protected static $defaultName = 'introspect';

    protected function configure(): void
    {
        $this->setDescription('Download a remote schema through introspection.');
        $this->addArgument(
            'endpoint',
            InputArgument::OPTIONAL,
            'You may choose a specific endpoint. Uses all by default.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endpoint = $input->getArgument('endpoint');
        if (null !== $endpoint) {
            $endpointNames = (array) $endpoint;
        } else {
            $endpointNames = array_keys(Configuration::endpoints());
        }

        foreach ($endpointNames as $endpointName) {
            if (! is_string($endpointName)) {
                $notString = gettype($endpointName);
                throw new \InvalidArgumentException("Expected --endpoint to be one or more strings, got {$notString}.");
            }

            echo "Running introspection on endpoint {$endpointName}...\n";
            (new Introspector(
                Configuration::endpoint($endpointName),
                $endpointName
            ))->introspect();
        }

        echo "Successfully introspected. You might want to rerun codegen: vendor/bin/sailor\n";

        return 0;
    }
}
