<?php

namespace Spawnia\Sailor;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\Visitor;
use GraphQL\Type\Schema;
use GraphQL\Utils\TypeInfo;

class Generator
{
    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var string
     */
    protected $namespace;

    public function __construct(Schema $schema, string $namespace)
    {
        $this->schema = $schema;
        $this->namespace = $namespace;
    }

    /**
     * @param  DocumentNode  $document
     */
    public function generate(DocumentNode $document)
    {
        $typeInfo = new TypeInfo($this->schema);
        Visitor::visit(
            $document,
            Visitor::visitWithTypeInfo(
                $typeInfo,
                [
                    'enter' => function ($node) use ($typeInfo){
                        //
                        $parent = $typeInfo->getParentType();
                        $type = $typeInfo->getType();
                        $argument = $typeInfo->getArgument();
                        echo 0;
                    }
                ]
            )
        );
    }
}
