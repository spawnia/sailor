<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\Codegen\DirectoryFinder;
use Spawnia\Sailor\Codegen\Finder;
use Spawnia\Sailor\Error\Error;
use Spawnia\Sailor\Events\ReceiveResponse;
use Spawnia\Sailor\Events\StartRequest;
use Spawnia\Sailor\Type\BooleanTypeConfig;
use Spawnia\Sailor\Type\EnumTypeConfig;
use Spawnia\Sailor\Type\FloatTypeConfig;
use Spawnia\Sailor\Type\IDTypeConfig;
use Spawnia\Sailor\Type\InputObjectTypeConfig;
use Spawnia\Sailor\Type\IntTypeConfig;
use Spawnia\Sailor\Type\ScalarTypeConfig;
use Spawnia\Sailor\Type\StringTypeConfig;
use Spawnia\Sailor\Type\TypeConfig;

abstract class EndpointConfig
{
    /** Instantiate a client that will resolve the GraphQL operations. */
    abstract public function makeClient(): Client;

    /** The namespace the generated classes will be created in. */
    abstract public function namespace(): string;

    /** Path to the directory where the generated classes will be put. */
    abstract public function targetPath(): string;

    /** Where to look for .graphql files containing operations. */
    abstract public function searchPath(): string;

    /** The location of the schema file that describes the endpoint. */
    abstract public function schemaPath(): string;

    /**
     * Will be called with events that happen during the execution lifecycle.
     *
     * @param StartRequest|ReceiveResponse $event
     */
    public function handleEvent(object $event): void {}

    /** Instantiate an Error class from a plain GraphQL error. */
    public function parseError(\stdClass $error): Error
    {
        return Error::fromStdClass($error);
    }

    /** Is it safe to display the errors from the endpoint to clients? */
    public function errorsAreClientSafe(): bool
    {
        return false;
    }

    /**
     * Return a map from type names to a TypeConfig describing how to deal with them.
     *
     * @return array<string, TypeConfig>
     */
    public function configureTypes(Schema $schema): array
    {
        $typeConfigs = [
            Type::INT => new IntTypeConfig(),
            Type::FLOAT => new FloatTypeConfig(),
            Type::STRING => new StringTypeConfig(),
            Type::BOOLEAN => new BooleanTypeConfig(),
            Type::ID => new IDTypeConfig(),
        ];

        foreach ($schema->getTypeMap() as $name => $type) {
            if ($type instanceof EnumType) {
                $typeConfigs[$name] = new EnumTypeConfig($this, $type);
            } elseif ($type instanceof InputObjectType) {
                $typeConfigs[$name] = new InputObjectTypeConfig($this, $schema, $type);
            } elseif ($type instanceof ScalarType) {
                $typeConfigs[$name] ??= new ScalarTypeConfig();
            }
        }

        return $typeConfigs;
    }

    /**
     * Generate additional classes.
     *
     * Will overwrite built-in generated classes if named the same.
     *
     * @return iterable<ClassType>
     */
    public function generateClasses(Schema $schema, DocumentNode $document): iterable
    {
        return [];
    }

    /** Instantiate a class to find GraphQL documents. */
    public function finder(): Finder
    {
        return new DirectoryFinder($this->searchPath());
    }

    public function typesNamespace(): string
    {
        return $this->namespace() . '\\Types';
    }

    public function operationsNamespace(): string
    {
        return $this->namespace() . '\\Operations';
    }

    public function typeConvertersNamespace(): string
    {
        return $this->namespace() . '\\TypeConverters';
    }
}
