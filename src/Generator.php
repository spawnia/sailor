<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\WrappingType;
use GraphQL\Type\Schema;
use GraphQL\Utils\TypeInfo;
use GraphQL\Language\Visitor;
use GraphQL\Language\AST\DocumentNode;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Property;

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

    /**
     * @var ClassType
     */
    private $operation;

    /**
     * @var ClassType[]
     */
    private $selectionStack = [];

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
                    // A named operation, e.g. "mutation FooMutation", maps to a class
                    NodeKind::OPERATION_DEFINITION => [
                        'enter' => function (OperationDefinitionNode $operationDefinition) {
                            $operationName = $operationDefinition->name->value;
                            $namespace = new PhpNamespace($this->namespace);
                            $this->operation = new ClassType($operationName, $namespace);
                            $this->operation->setExtends(Operation::class);
                            $this->operation->addConstant('DOCUMENT', $operationDefinition->loc->source->body);

                            $run = new Method('run');
                            $run->setBody('return $this->runInternal(self::DOCUMENT);');
                            $resultName = "{$operationName}Result";
                            $run->setReturnType($resultName);
                            $run->setVisibility(ClassType::VISIBILITY_PUBLIC);

                            $this->operation->addMember($run);

                            $selection = new ClassType($resultName, $namespace);
                            $this->selectionStack []= $selection;
                        },
                        'leave' => function (OperationDefinitionNode $operationDefinition) {
                            echo $this->operation->getNamespace();
                            echo $this->operation->__toString();
                        },
                    ],
                    NodeKind::FIELD => [
                        'enter' => function (FieldNode $field) use ($typeInfo) {
                            $resultingName = $field->alias
                                ? $field->alias->value
                                : $field->name->value;

                            $selection = end($this->selectionStack);
                            $field = new Property($resultingName);

                            $type = $typeInfo->getType();
                            $field->setComment('@var ' . PhpDoc::forType($type));
                            $selection->addMember($field);

                            $wrappedType = $type;
                            if($type instanceof WrappingType) {
                                $wrappedType = $type->getWrappedType(true);
                            }

                            if($wrappedType instanceof ObjectType) {
                                $namespace = new PhpNamespace($selection->getNamespace() . '\\' . ucfirst($resultingName));
                                $selection = new ClassType($wrappedType->name, $namespace);
                                $this->selectionStack []= $selection;
                            }
                        }
                    ]
                ]
            )
        );
    }
}
