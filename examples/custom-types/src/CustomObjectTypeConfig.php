<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypesSrc;

use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Spawnia\Sailor\Convert\GeneratesTypeConverter;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Type\InputTypeConfig;
use Spawnia\Sailor\Type\TypeConfig;

final class CustomObjectTypeConfig implements TypeConfig, InputTypeConfig
{
    use GeneratesTypeConverter;

    private EndpointConfig $endpointConfig;

    /**
     * @var Type&NamedType
     */
    private Type $type;

    /**
     * @param Type&NamedType $type
     */
    public function __construct(EndpointConfig $endpointConfig, Type $type)
    {
        $this->endpointConfig = $endpointConfig;
        $this->type = $type;
    }

    public function typeConverter(): string
    {
        return $this->typeConverterClassName($this->type, $this->endpointConfig);
    }

    public function inputTypeReference(): string
    {
        return '\\' . CustomObject::class;
    }

    public function generateClasses(): iterable
    {
        yield $this->makeTypeConverter($this->type, $this->endpointConfig);
    }

    protected function decorateTypeConverterClass(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType
    {
        $customObjectClass = CustomObject::class;

        $fromGraphQL->setReturnType($customObjectClass);
        $fromGraphQL->setBody(
            <<<PHP
                if (! \$value instanceof \\stdClass) {
                    throw new \InvalidArgumentException('Expected stdClass, got: '.gettype(\$value));
                }

                if (! property_exists(\$value, 'foo')) {
                    throw new \InvalidArgumentException('Did not find expected property foo.');
                }

                return new \\{$customObjectClass}(\$value->foo);
                PHP
        );

        $toGraphQL->setBody(
            <<<PHP
                if (! \$value instanceof \\{$customObjectClass}) {
                    throw new \InvalidArgumentException('Expected instanceof {$customObjectClass}, got: '.gettype(\$value));
                }

                return (array) \$value;
                PHP
        );

        return $class;
    }
}
