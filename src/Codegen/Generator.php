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
        $classes = $classGenerator->generate($document);

        foreach ($classes as $operationClasses) {
            if (! file_exists($this->options->targetPath)) {
                \Safe\mkdir($this->options->targetPath, 0777, true);
            }

            $operation = $operationClasses->operation;
            \Safe\file_put_contents(
                $this->options->targetPath.'/'.$operation->getName().'.php',
                self::asPhpFile($operation)
            );
        }
    }

    protected static function asPhpFile(ClassType $classType): string
    {
        return <<<PHP
            <?php
            
            declare(strict_types=1);
            
            {$classType->getNamespace()}
            
            {$classType->__toString()}
            PHP;
    }
}
