<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Schema;
use GraphQL\Utils\TypeInfo;
use GraphQL\Language\Visitor;
use Spawnia\Sailor\Operation;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\FieldNode;
use Nette\PhpGenerator\PhpNamespace;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Language\AST\SelectionSetNode;
use GraphQL\Language\AST\OperationDefinitionNode;

class ClassGenerator
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
     * @var OperationClasses[]
     */
    private $operationClassesStorage = [];

    /**
     * @var ClassType[]
     */
    private $selectionStack = [];

    /**
     * @var ClassType[]
     */
    private $selectionClasses = [];

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
                            $this->selectionStack [] = $selection;
                        },
                        'leave' => function (OperationDefinitionNode $operationDefinition) {
                            $operationClasses = new OperationClasses();

                            $operationClasses->operation = $this->operation;
                            $this->operation = null;
                            $operationClasses->selection = $this->selectionClasses;
                            $this->selectionClasses = [];

                            $this->operationClassesStorage [] = $operationClasses;
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
                            $field->setComment('@var '.PhpDoc::forType($type));
                            $selection->addMember($field);

                            $namedType = Type::getNamedType($type);

                            if ($namedType instanceof ObjectType) {
                                $namespace = new PhpNamespace($selection->getNamespace().'\\'.ucfirst($resultingName));
                                $selection = new ClassType($namedType->name, $namespace);
                                $this->selectionStack [] = $selection;
                            }
                        },
                    ],
                    NodeKind::SELECTION_SET => [
                        'leave' => function (SelectionSetNode $selectionSet) use ($typeInfo) {
                            // We are done with building this subtree of the selection set,
                            // so we move the top-most element to the storage
                            $this->selectionClasses [] = array_pop($this->selectionStack);
                        },
                    ],
                ]
            )
        );

        return $this->operationClassesStorage;
    }
}
