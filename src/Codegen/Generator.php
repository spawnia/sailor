<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\Parser;
use GraphQL\Utils\BuildSchema;
use Nette\PhpGenerator\ClassType;

class Generator
{
    /**
     * @var GeneratorOptions
     */
    protected $options;

    public function __construct(GeneratorOptions $options)
    {
        $this->options = $options;
    }

    public function run()
    {
        $finder = new Finder($this->options->searchPath);
        $documents = $finder->documents();
        $documents = array_map(
            [Parser::class, 'parse'],
            $documents
        );

        $schemaString = \Safe\file_get_contents($this->options->schemaPath);
        $schema = BuildSchema::build($schemaString);

        // TODO call validator
        $document = Merger::combine($documents);

        $classGenerator = new ClassGenerator($schema, $this->options->namespace);
        $operationSets = $classGenerator->generate($document);

        foreach ($operationSets as $operationSet) {
            $this->writeFile($operationSet->operation);
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
        $pathInTarget = self::after($namespace, $this->options->namespace);
        $pathInTarget = str_replace('\\', '/', $pathInTarget);

        return $this->options->targetPath.$pathInTarget;
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
        return <<<PHP
            <?php
            
            declare(strict_types=1);
            
            {$classType->getNamespace()}{$classType->__toString()}
            PHP;
    }
}
