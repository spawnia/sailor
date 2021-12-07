<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Spawnia\Sailor\Convert\ListConverter;
use Spawnia\Sailor\Convert\NonNullConverter;
use Spawnia\Sailor\Convert\NullConverter;

class TypeConverterWrapper
{
    /**
     * Wrap the code for an inner converter with converters for its wrapping types.
     */
    public static function wrap(Type $type, string $innerConverter, bool $nullabilityDecided = false): string
    {
        if ($type instanceof NonNull) {
            $nonNullConverterClass = NonNullConverter::class;

            return self::wrap(
                $type->getOfType(),
                /** @lang PHP */
                "new \\{$nonNullConverterClass}({$innerConverter})",
                true
            );
        }

        if ($type instanceof ListOfType) {
            $listConverterClass = ListConverter::class;

            return self::wrap(
                $type->getOfType(),
                /** @lang PHP */
                "new \\{$listConverterClass}({$innerConverter})"
            );
        }

        if (! $nullabilityDecided) {
            $nullConverterClass = NullConverter::class;

            return /** @lang PHP */ "new \\{$nullConverterClass}({$innerConverter})";
        }

        return $innerConverter;
    }
}
