<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\Operations\MyBenSampoEnumQuery\MyBenSampoEnumQueryResult>
 */
class MyBenSampoEnumQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(?\Spawnia\Sailor\CustomTypes\Types\BenSampoEnum $value = null): MyBenSampoEnumQuery\MyBenSampoEnumQueryResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyBenSampoEnumQuery($value: BenSampoEnum) {
          __typename
          withBenSampoEnum(value: $value)
        }';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }
}
