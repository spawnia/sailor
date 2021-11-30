<?php

declare(strict_types=1);

namespace Spawnia\Sailor\EnumSrc;

use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\TypeConverter;

class TypeConverterGenerator
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
        foreach ($this->schema->getTypeMap() as $name => $type) {
            if ('CustomEnum' === $name) {
                $class = new ClassType(
                    $type->name,
                    new PhpNamespace(self::typeConvertersNamespace($this->endpointConfig))
                );

                $customEnumClass = CustomEnumGenerator::className($type->name, $this->endpointConfig);

                $class->addImplement(TypeConverter::class);

                $fromGraphQL = $class->addMethod('fromGraphQL');
                $fromGraphQL->addParameter('value');
                $fromGraphQL->setReturnType($customEnumClass);
                $fromGraphQL->setBody(
                    <<<PHP
                        return new \\{$customEnumClass}(\$value);
                        PHP
                );

                $toGraphQL = $class->addMethod('toGraphQL');
                $toGraphQL->addParameter('value');
                $toGraphQL->setBody(
                    <<<PHP
                        if (! \$value instanceof \\{$customEnumClass}) {
                            throw new \InvalidArgumentException('Expected instanceof Enum, got: '.gettype(\$value));
                        }

                        return \$value->value;
                        PHP
                );

                yield $class;
            }
        }
    }

    public static function className(string $typeName, EndpointConfig $endpointConfig): string
    {
        return self::typeConvertersNamespace($endpointConfig) . '\\' . $typeName;
    }

    protected static function typeConvertersNamespace(EndpointConfig $endpointConfig): string
    {
        return $endpointConfig->namespace() . '\\TypeConverters';
    }
}
