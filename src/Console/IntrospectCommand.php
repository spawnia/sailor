<?php declare(strict_types=1);

namespace Spawnia\Sailor\Console;

use Spawnia\Sailor\Introspector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IntrospectCommand extends Command
{
    use InteractsWithEndpoints;

    protected static $defaultName = 'introspect';

    protected function configure(): void
    {
        $this->setDescription('Download a remote schema through introspection.');
        $this->configureEndpoints();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endpoints = $this->endpoints($input);

        $configFile = $this->configFile($input);

        foreach ($endpoints as $endpointName => $endpoint) {
            echo "Running introspection on endpoint {$endpointName}...\n";
            (new Introspector($endpoint, $configFile, $endpointName))->introspect();
        }

        echo "Successfully introspected. Rerun codegen with: vendor/bin/sailor\n";

        return 0;
    }
}
