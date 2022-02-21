<?php declare(strict_types=1);

namespace Spawnia\Sailor\Console;

use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Codegen\Writer;
use Spawnia\Sailor\Configuration;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodegenCommand extends Command
{
    private const OPTION_CONFIGURATION = 'configuration';

    protected static $defaultName = 'codegen';

    protected function configure(): void
    {
        $this->setDescription('Generate code from your GraphQL files.');
        $this->addArgument('endpoint', InputArgument::OPTIONAL, 'You may choose a specific endpoint. Uses all by default.');
        $this->addOption(
            self::OPTION_CONFIGURATION,
            'c',
            InputArgument::OPTIONAL,
            'Path to a configuration file. Default `sailor.php`.',
            'sailor.php'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endpoint = $input->getArgument('endpoint');
        $configurationFile = $input->getOption(self::OPTION_CONFIGURATION);
        assert(is_string($configurationFile));

        $configuration = new Configuration(new SplFileInfo($configurationFile));

        if (null !== $endpoint) {
            $endpointNames = (array) $endpoint;
        } else {
            $endpointNames = array_keys(Configuration::endpoints());
        }

        /** @var string $endpointName */
        foreach ($endpointNames as $endpointName) {
            echo "Generating code for endpoint {$endpointName}...\n";

            $endpointConfig = $configuration::endpoint($endpointName);

            $generator = new Generator($endpointConfig, $endpointName);
            $files = $generator->generate();

            $writer = new Writer($endpointConfig);
            $writer->write($files);
        }

        echo "Successfully generated code, query ahead!\n";

        return 0;
    }
}
