<?php

declare(strict_types=1);

namespace Spawnia\Sailor\EnumSrc;

use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Spawnia\Sailor\TypeConverter\TypeConverterGenerator;

class CustomTypeConverterGenerator extends TypeConverterGenerator
{
    /**
     * @return iterable<ClassType>
     */
    public function generate(): iterable
    {
        yield $this->forType($this->schema->getType('CustomEnum'));
    }

    protected function decorate(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType
    {
        $customEnumClass = CustomEnumGenerator::className($type->name, $this->endpointConfig);

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
}
