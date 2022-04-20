<?php declare(strict_types=1);

namespace Spawnia\Sailor\Console;

use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @mixin Command
 */
trait InteractsWithEndpoints
{
    /**
     * @return array<string, EndpointConfig>
     */
    protected function endpoints(InputInterface $input): array
    {
        $configFile = $this->configFile($input);

        $endpointName = $input->getArgument('endpoint');

        return is_string($endpointName)
            ? [$endpointName => Configuration::endpoint($configFile, $endpointName)]
            : Configuration::endpoints($configFile);
    }

    protected function configureEndpoints(): void
    {
        $this->addArgument(
            'endpoint',
            InputArgument::OPTIONAL,
            'You may choose a specific endpoint. Uses all by default.'
        );
        $this->addOption(
            'config',
            'c',
            InputArgument::OPTIONAL,
            'Path to a configuration file. Default `sailor.php`.',
            'sailor.php'
        );
    }

    protected function configFile(InputInterface $input): string
    {
        $configFile = $input->getOption('config');
        assert(is_string($configFile));

        return $configFile;
    }
}
