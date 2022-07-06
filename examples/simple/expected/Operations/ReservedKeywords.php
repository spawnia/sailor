<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\ReservedKeywords\ReservedKeywordsResult>
 */
class ReservedKeywords extends \Spawnia\Sailor\Operation
{
    public static function execute(): ReservedKeywords\ReservedKeywordsResult
    {
        return self::executeOperation(
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query ReservedKeywords {
          __typename
          print: reservedKeywords {
            __typename
            a
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../sailor.php';
    }
}
