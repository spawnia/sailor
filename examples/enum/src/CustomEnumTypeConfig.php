<?php declare(strict_types=1);

namespace Spawnia\Sailor\EnumSrc;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Utils\Utils;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Type\TypeConfig;
use Spawnia\Sailor\TypeConverter\GeneratesTypeConverter;

class CustomEnumTypeConfig implements TypeConfig
{
    use GeneratesTypeConverter;

    private EndpointConfig $endpointConfig;

    private Schema $schema;

    private EnumType $type;

    private CustomEnumGenerator $enumGenerator;

    public function __construct(EndpointConfig $endpointConfig, Schema $schema, string $typeName)
    {
        $this->endpointConfig = $endpointConfig;
        $this->schema = $schema;

        $type = $schema->getType($typeName);
        if (! $type instanceof EnumType) {
            $notEnumType = Utils::printSafe($type);
            throw new \InvalidArgumentException("Expected type {$typeName} to be instanceof EnumType, got: {$notEnumType}.");
        }
        $this->type = $type;

        $this->enumGenerator = new CustomEnumGenerator($endpointConfig, $this->type);
    }

    protected function decorateTypeConverter(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType
    {
        $customEnumClass = $this->enumGenerator->className();

        $fromGraphQL->setReturnType($customEnumClass);
        $fromGraphQL->setBody(
            <<<PHP
                return new \\{$customEnumClass}(\$value);
                PHP
        );

        $toGraphQL->setBody(
            <<<PHP
                if (! \$value instanceof \\{$customEnumClass}) {
                    throw new \InvalidArgumentException('Expected instanceof Enum, got: '.gettype(\$value));
                }

                return \$value->value;
                PHP
        );

        return $class;
    }

    public function typeConverter(): string
    {
        return $this->typeConverterClassName($this->type, $this->endpointConfig);
    }

    public function typeReference(): string
    {
        return "\\{$this->enumGenerator->className()}";
    }

    public function generate(): iterable
    {
        foreach ($this->enumGenerator->generate() as $enum) {
            yield $enum;
        }

        yield $this->makeTypeConverter($this->type, $this->endpointConfig);
    }
}
