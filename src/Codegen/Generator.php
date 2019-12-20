<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Error\Error;
use GraphQL\Error\SyntaxError;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\Parser;
use GraphQL\Utils\BuildSchema;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PsrPrinter;
use Spawnia\Sailor\EndpointConfig;

class Generator
{
    /**
     * @var EndpointConfig
     */
    protected $endpointConfig;

    /**
     * @var string
     */
    protected $endpointName;

    public function __construct(EndpointConfig $endpointConfig, string $endpointName)
    {
        $this->endpointConfig = $endpointConfig;
        $this->endpointName = $endpointName;
    }

    public function generate(): void
    {
        $finder = new Finder($this->endpointConfig->searchPath());
        $documents = $finder->documents();

        $parsed = static::parseDocuments($documents);
        static::ensureOperationsAreNamed($parsed);

        $schemaString = \Safe\file_get_contents(
            $this->endpointConfig->schemaPath()
        );
        $schema = BuildSchema::build($schemaString);

        $document = Merger::combine($parsed);
        Validator::validate($schema, $document);

        $classGenerator = new ClassGenerator($schema, $this->endpointConfig, $this->endpointName);
        $operationSets = $classGenerator->generate($document);

        foreach ($operationSets as $operationSet) {
            $this->writeFile($operationSet->operation);
            $this->writeFile($operationSet->result);

            foreach ($operationSet->selectionStorage as $selection) {
                $this->writeFile($selection);
            }
        }
    }

    protected function writeFile(ClassType $classType): void
    {
        $phpNamespace = $classType->getNamespace();
        if ($phpNamespace === null) {
            throw new \Exception('Generated classes must have a namespace.');
        }
        $targetDirectory = $this->targetDirectory(
            $phpNamespace->getName()
        );

        if (! file_exists($targetDirectory)) {
            \Safe\mkdir($targetDirectory, 0777, true);
        }

        \Safe\file_put_contents(
            $targetDirectory.'/'.$classType->getName().'.php',
            self::asPhpFile($classType)
        );
    }

    protected function targetDirectory(string $namespace): string
    {
        $pathInTarget = self::after($namespace, $this->endpointConfig->namespace());
        $pathInTarget = str_replace('\\', '/', $pathInTarget);

        return $this->endpointConfig->targetPath().$pathInTarget;
    }

    public static function after(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        /** @var string[] $parts */
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
     * @param  string[]  $documents
     * @return \GraphQL\Language\AST\DocumentNode[]
     *
     * @throws \GraphQL\Error\SyntaxError
     */
    public static function parseDocuments(array $documents): array
    {
        $parsed = [];
        foreach ($documents as $path => $content) {
            try {
                $parsed[$path] = Parser::parse($content);
            } catch (SyntaxError $error) {
                // Inform the user which file the error occurred in.
                $error->message .= ' in '.$path;
                throw $error;
            }
        }

        return $parsed;
    }

    /**
     * @param  \GraphQL\Language\AST\DocumentNode[]  $parsed
     * @return void
     */
    public static function ensureOperationsAreNamed(array $parsed): void
    {
        foreach ($parsed as $path => $documentNode) {
            foreach ($documentNode->definitions as $definition) {
                if (! $definition instanceof OperationDefinitionNode) {
                    throw new Error('Found unsupported definition in '.$path, $definition);
                }

                if ($definition->name === null) {
                    throw new Error('Found unnamed operation definition in '.$path, $definition);
                }
            }
        }
    }
}
