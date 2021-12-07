<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Error\Error;
use GraphQL\Error\SyntaxError;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\Parser;
use GraphQL\Type\Schema;
use GraphQL\Utils\BuildSchema;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PsrPrinter;
use Nette\Utils\FileSystem;
use Spawnia\Sailor\EndpointConfig;

class Generator
{
    protected EndpointConfig $endpointConfig;

    protected string $endpointName;

    public function __construct(EndpointConfig $endpointConfig, string $endpointName)
    {
        $this->endpointConfig = $endpointConfig;
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

        $document = Merger::combine($parsedDocuments);
        AddTypename::modify($document);

        $schema = $this->schema();

        Validator::validate($schema, $document);

        foreach ((new OperationGenerator($schema, $document, $this->endpointConfig, $this->endpointName))->generate() as $class) {
            yield $this->makeFile($class);
        }

        foreach ((new TypeConvertersGenerator($schema, $this->endpointConfig, $this->endpointName))->generate() as $class) {
            yield $this->makeFile($class);
        }

        foreach ($this->endpointConfig->configureTypes($schema, $this->endpointName) as $typeConfig) {
            foreach ($typeConfig->generateClasses() as $class) {
                yield $this->makeFile($class);
            }
        }

        foreach ($this->endpointConfig->generateClasses($schema, $document, $this->endpointName) as $class) {
            yield $this->makeFile($class);
        }
    }

    protected function makeFile(ClassType $classType): File
    {
        $file = new File();

        $phpNamespace = $classType->getNamespace();
        if (null === $phpNamespace) {
            throw new \Exception('Generated classes must have a namespace.');
        }
        $file->directory = $this->targetDirectory(
            $phpNamespace->getName()
        );

        $file->name = $classType->getName() . '.php';
        $file->content = self::asPhpFile($classType);

        return $file;
    }

    protected function targetDirectory(string $namespace): string
    {
        $pathInTarget = self::after($namespace, $this->endpointConfig->namespace());
        $pathInTarget = str_replace('\\', '/', $pathInTarget);

        return $this->endpointConfig->targetPath() . $pathInTarget;
    }

    public static function after(string $subject, string $search): string
    {
        if ('' === $search) {
            return $subject;
        }

        /**
         * We validated that $search is not empty, so this can not be false.
         *
         * @var array<int, string> $parts
         */
        $parts = explode($search, $subject, 2);

        return array_reverse($parts)[0];
    }

    protected static function asPhpFile(ClassType $classType): string
    {
        $printer = new PsrPrinter();
        $phpNamespace = $classType->getNamespace();
        $class = $printer->printClass($classType, $phpNamespace);

        return <<<PHP
            <?php

            declare(strict_types=1);

            {$phpNamespace}{$class}
            PHP;
    }

    /**
     * Parse the raw document contents.
     *
     * @param  array<string, string>  $documents
     *
     * @throws \GraphQL\Error\SyntaxError
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
                    $error->getMessage() . ' in ' . $path,
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
    public static function ensureOperationsAreNamed(array $parsed): void
    {
        foreach ($parsed as $path => $documentNode) {
            foreach ($documentNode->definitions as $definition) {
                if (! $definition instanceof OperationDefinitionNode) {
                    throw new Error('Found unsupported definition in ' . $path, $definition);
                }

                if (null === $definition->name) {
                    throw new Error('Found unnamed operation definition in ' . $path, $definition);
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
        $finder = new Finder($this->endpointConfig->searchPath());
        $documents = $finder->documents();

        $parsed = static::parseDocuments($documents);
        static::ensureOperationsAreNamed($parsed);

        return $parsed;
    }

    protected function deleteGeneratedFiles(): void
    {
        FileSystem::delete($this->endpointConfig->targetPath());
    }
}
