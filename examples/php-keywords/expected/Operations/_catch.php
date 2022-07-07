<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\_catch\catchResult>
 */
class _catch extends \Spawnia\Sailor\Operation
{
    public static function execute(): _catch\catchResult
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
        return /* @lang GraphQL */ 'query catch {
          __typename
          print {
            __typename
            for
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../sailor.php';
    }
}