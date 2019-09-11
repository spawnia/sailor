<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Schema;
use GraphQL\Utils\TypeInfo;
use GraphQL\Language\Visitor;
use Spawnia\Sailor\Operation;
use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\FieldNode;
use Nette\PhpGenerator\PhpNamespace;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Language\AST\SelectionSetNode;
use GraphQL\Language\AST\OperationDefinitionNode;

class ClassGenerator
{
    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var OperationSet
     */
    private $operationSet;

    /**
     * @var OperationSet[]
     */
    private $operationStorage = [];

    /**
     * @var string[]
     */
    private $namespaceStack = [];

    public function __construct(Schema $schema, string $namespace)
    {
        $this->schema = $schema;
        $this->namespaceStack [] = $namespace;
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

                            // Generate a class to represent the query/mutation itself
                            $operation = new ClassType($operationName, $this->makeNamespace());

                            // The base class contains most of the logic
                            $operation->setExtends(Operation::class);

                            // Store the actual query string in the operation
                            // TODO minify the query string
                            $operation->addConstant('DOCUMENT', $operationDefinition->loc->source->body);

                            // The run method is the public API of the operation
                            $run = $operation->addMethod('run');
                            $run->setStatic();

                            // It returns a typed result which is a new selection set class
                            $run->setBody(<<<'PHP'
                            $instance = new self;
                            
                            return $instance->runInternal(self::DOCUMENT);
                            PHP
                            );
                            $resultName = "{$operationName}Result";

                            // Related classes are put into a nested namespace
                            $this->namespaceStack [] = $operationName;
                            $run->setReturnType($this->currentNamespace().'\\'.$resultName);

                            $operationResult = new ClassType($resultName, $this->makeNamespace());

                            $this->operationSet = new OperationSet($operation);
                            $this->operationSet->pushSelection($operationResult);
                        },
                        'leave' => function (OperationDefinitionNode $operationDefinition) {
                            // Store the current operation as we continue with the next one
                            $this->operationStorage [] = $this->operationSet;
                        },
                    ],
                    NodeKind::FIELD => [
                        'enter' => function (FieldNode $field) use ($typeInfo) {
                            // The key that the
                            $resultKey = $field->alias
                                ? $field->alias->value
                                : $field->name->value;

                            $selection = $this->operationSet->peekSelection();
                            $field = $selection->addProperty($resultKey);
                            $type = $typeInfo->getType();

                            $namedType = Type::getNamedType($type);

                            if ($namedType instanceof ObjectType) {
                                $className = ucfirst($resultKey);
                                $typeReference = $this->currentNamespace().'\\'.$className;
                                $this->operationSet->pushSelection(
                                    new ClassType(
                                        $className,
                                        $this->makeNamespace()
                                    )
                                );
                                // We go one level deeper into the selection set
                                // To avoid naming conflicts, we add on another namespace
                                $this->namespaceStack [] = $typeReference;
                            } elseif ($namedType instanceof ScalarType) {
                                $typeReference = PhpDoc::forScalar($namedType);
                            }

                            $field->setComment('@var '.PhpDoc::forType($type, $typeReference));
                        },
                    ],
                    NodeKind::SELECTION_SET => [
                        'leave' => function (SelectionSetNode $selectionSet) use ($typeInfo) {
                            // We are done with building this subtree of the selection set,
                            // so we move the top-most element to the storage
                            $this->operationSet->popSelection();

                            // The namespace moves up a level
                            array_pop($this->namespaceStack);
                        },
                    ],
                ]
            )
        );

        return $this->operationStorage;
    }

    protected function makeNamespace(): PhpNamespace
    {
        return new PhpNamespace(
            $this->currentNamespace()
        );
    }

    protected function currentNamespace(): string
    {
        return implode('\\', $this->namespaceStack);
    }
}
