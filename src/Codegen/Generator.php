<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Error\Error;
use GraphQL\Error\SyntaxError;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\Parser;
use GraphQL\Type\Schema;
use GraphQL\Utils\BuildSchema;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use Spawnia\Sailor\EndpointConfig;

class Generator
{
    protected EndpointConfig $endpointConfig;

    protected string $configFile;

    protected string $endpointName;

    public function __construct(EndpointConfig $endpointConfig, string $configFile, string $endpointName)
    {
        $this->endpointConfig = $endpointConfig;
        $this->configFile = $configFile;
        $this->endpointName = $endpointName;
    }

    /**
     * Generate a list of files to write.
     *
     * @return iterable<File>
     */
    public function generate(): iterable
    {
        $parsedDocuments = $this->parsedDocuments();
        if ([] === $parsedDocuments) {
            return [];
        }

        $schema = $this->schema();
        $document = Merger::combine($parsedDocuments);

        // Validate the document as defined by the user to give them an error
        // message that is more closely related to their source code
        Validator::validate($schema, $document);

        $document = (new FoldFragments($document))->modify();
        AddTypename::modify($document);

        // Validate again to ensure the modifications we made were safe
        Validator::validate($schema, $document);

        foreach ((new OperationGenerator($schema, $document, $this->endpointConfig))->generate() as $class) {
            yield $this->makeFile($class);
        }

        foreach ($this->endpointConfig->configureTypes($schema) as $typeConfig) {
            foreach ($typeConfig->generateClasses() as $class) {
                yield $this->makeFile($class);
            }
        }

        foreach ($this->endpointConfig->generateClasses($schema, $document) as $class) {
            yield $this->makeFile($class);
        }
    }

    protected function makeFile(ClassType $classType): File
    {
        $endpoint = $classType->addMethod('endpoint');
        $endpoint->setStatic();
        $endpoint->setReturnType('string');
        $endpoint->setBody(<<<PHP
            return '{$this->endpointName}';
        PHP);

        $file = new File();

        $phpNamespace = $classType->getNamespace();
        if (null === $phpNamespace) {
            throw new \Exception('Generated classes must have a namespace.');
        }
        $namespace = $phpNamespace->getName();

        $targetDirectory = $this->targetDirectory($namespace);
        $file->directory = $targetDirectory;

        $config = $classType->addMethod('config');
        $config->setStatic();
        $config->setReturnType('string');
        $config->setBody(<<<PHP
            return {$this->configPath($targetDirectory)};
        PHP);

        $file->name = $classType->getName() . '.php';
        $file->content = self::asPhpFile($classType, $phpNamespace);

        return $file;
    }

    protected function targetDirectory(string $namespace): string
    {
        $pathInTarget = self::after($namespace, $this->endpointConfig->namespace());
        $pathInTarget = str_replace('\\', '/', $pathInTarget);

        return $this->endpointConfig->targetPath() . $pathInTarget;
    }

    /**
     * @see https://stackoverflow.com/a/2638272
     */
    protected function configPath(string $directory): string
    {
        $from = explode('/', $directory);
        $to = explode('/', $this->configFile);

        $relativeParts = $to;

        foreach ($from as $depth => $dir) {
            if ($dir === $to[$depth]) {
                array_shift($relativeParts);
            } else {
                $upwards = count($relativeParts) + count($from) - $depth;
                $relativeParts = array_pad($relativeParts, -$upwards, '..');
                break;
            }
        }

        $relative = implode('/', $relativeParts);

        return "\\Safe\\realpath(__DIR__ . '/{$relative}')";
    }

    public static function after(string $subject, string $search): string
    {
        if ('' === $search) {
            return $subject;
        }

        $parts = explode($search, $subject, 2);

        return array_reverse($parts)[0];
    }

    protected static function asPhpFile(ClassType $classType, PhpNamespace $namespace): string
    {
        $printer = new PsrPrinter();

        return <<<PHP
            <?php declare(strict_types=1);

            namespace {$namespace->getName()};
            
            {$printer->printClass($classType, $namespace)}
            PHP;
    }

    /**
     * Parse the raw document contents.
     *
     * @param  array<string, string>  $documents
     *
     * @throws SyntaxError
     *
     * @return array<string, \GraphQL\Language\AST\DocumentNode>
     */
    public static function parseDocuments(array $documents): array
    {
        $parsed = [];
        foreach ($documents as $path => $content) {
            try {
                $parsed[$path] = Parser::parse($content);
            } catch (SyntaxError $error) {
                throw new Error(
                    // Inform the user which file the error occurred in.
                    "Failed to parse {$path}: {$error->getMessage()}.",
                    null,
                    $error->getSource(),
                    $error->getPositions()
                );
            }
        }

        return $parsed;
    }

    /**
     * @param  array<string, \GraphQL\Language\AST\DocumentNode>  $parsed
     */
    public static function validateDocuments(array $parsed): void
    {
        foreach ($parsed as $path => $documentNode) {
            foreach ($documentNode->definitions as $definition) {
                if ($definition instanceof OperationDefinitionNode && null === $definition->name) {
                    throw new Error("Found unnamed operation definition in {$path}.", $definition);
                }
            }
        }
    }

    protected function schema(): Schema
    {
        $schemaString = \Safe\file_get_contents(
            $this->endpointConfig->schemaPath()
        );

        return BuildSchema::build($schemaString);
    }

    /**
     * @return array<string, \GraphQL\Language\AST\DocumentNode>
     */
    protected function parsedDocuments(): array
    {
        $documents = $this->endpointConfig
            ->finder()
            ->documents();

        // Ignore the schema itself, it never contains operation definitions
        unset($documents[$this->endpointConfig->schemaPath()]);

        $parsed = static::parseDocuments($documents);

        static::validateDocuments($parsed);

        return $parsed;
    }
}
