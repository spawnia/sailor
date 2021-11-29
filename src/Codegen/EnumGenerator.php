<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\EndpointConfig;

class EnumGenerator
{
    protected Schema $schema;

    protected EndpointConfig $endpointConfig;

    public function __construct(Schema $schema, EndpointConfig $endpointConfig)
    {
        $this->schema = $schema;
        $this->endpointConfig = $endpointConfig;
    }

    /**
     * @return iterable<ClassType>
     */
    public function generate(): iterable
    {
        foreach ($this->schema->getTypeMap() as $type) {
            if (! $type instanceof EnumType) {
                continue;
            }

            $class = $this->makeClass($type);

            yield $this->decorateClass($type, $class);
        }
    }

    public static function className(EnumType $type, EndpointConfig $endpointConfig): string
    {
        return self::enumsNamespace($endpointConfig).'\\'.$type->name;
    }

    protected static function enumsNamespace(EndpointConfig $endpointConfig): string
    {
        return $endpointConfig->namespace().'\\Enums';
    }

    protected function makeClass(EnumType $type): ClassType
    {
        return new ClassType(
            $type->name,
            new PhpNamespace(static::enumsNamespace($this->endpointConfig))
        );
    }

    protected function decorateClass(EnumType $type, ClassType $class): ClassType
    {
        foreach ($type->getValues() as $value) {
            $name = $value->name;
            $class->addConstant($name, $name);
        }

        return $class;
    }
}
