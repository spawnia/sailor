<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypesSrc;

use DateTime;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Type\TypeConfig;
use Spawnia\Sailor\TypeConverter\GeneratesTypeConverter;

class CustomDateTypeConfig implements TypeConfig
{
    use GeneratesTypeConverter;

    public const FORMAT = 'Y-m-d H:i:s';

    private EndpointConfig $endpointConfig;

    private ScalarType $scalarType;

    public function __construct(EndpointConfig $endpointConfig, ScalarType $scalarType)
    {
        $this->endpointConfig = $endpointConfig;
        $this->scalarType = $scalarType;
    }

    public function typeConverter(): string
    {
        return $this->typeConverterClassName($this->scalarType, $this->endpointConfig);
    }

    public function typeReference(): string
    {
        return '\\' . DateTime::class;
    }

    public function generate(): iterable
    {
        yield $this->makeTypeConverter($this->scalarType, $this->endpointConfig);
    }

    protected function decorateTypeConverterClass(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType
    {
        $dateTimeClass = DateTime::class;
        $format = self::FORMAT;

        $fromGraphQL->setReturnType($dateTimeClass);
        $fromGraphQL->setBody(
            <<<PHP
                return \\{$dateTimeClass}::createFromFormat('{$format}', \$value);
                PHP
        );

        $toGraphQL->setBody(
            <<<PHP
                if (! \$value instanceof \\{$dateTimeClass}) {
                    throw new \InvalidArgumentException('Expected instanceof DateTime, got: '.gettype(\$value));
                }

                return \$value->format('{$format}');
                PHP
        );

        return $class;
    }
}
