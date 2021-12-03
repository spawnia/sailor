<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\EndpointConfig;

abstract class ClassGenerator
{
    protected Schema $schema;

    protected DocumentNode $document;

    protected EndpointConfig $endpointConfig;

    protected string $endpointName;

    public function __construct(Schema $schema, DocumentNode $document, EndpointConfig $endpointConfig, string $endpointName)
    {
        $this->schema = $schema;
        $this->endpointConfig = $endpointConfig;
        $this->endpointName = $endpointName;
        $this->document = $document;
    }

    /**
     * @return iterable<ClassType>
     */
    abstract public function generate(): iterable;
}
