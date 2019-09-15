<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

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

    public function run()
    {
        $finder = new Finder($this->endpointConfig->searchPath());
        $documents = $finder->documents();
        $documents = array_map(
            [Parser::class, 'parse'],
            $documents
        );

        $schemaString = \Safe\file_get_contents(
            $this->endpointConfig->schemaPath()
        );
        $schema = BuildSchema::build($schemaString);

        // TODO call validator
        $document = Merger::combine($documents);

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
        $targetDirectory = $this->targetDirectory(
            $classType->getNamespace()->getName()
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
        return $search === ''
            ? $subject
            : array_reverse(
                explode($search, $subject, 2)
            )[0];
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
}
