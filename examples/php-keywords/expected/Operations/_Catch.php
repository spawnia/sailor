<?php declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\PhpKeywords\Operations\_Catch\_CatchResult>
 */
class _Catch extends \Spawnia\Sailor\Operation
{
    public static function execute(): _Catch\_CatchResult
    {
        return self::executeOperation(
        );
    }

    protected static function converters(): array
    {
        /** @var array<int, array{string, \Spawnia\Sailor\Convert\TypeConverter}>|null $converters */
        static $converters;

        return $converters ??= [
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query Catch {
          __typename
          print {
            __typename
            int
            ... on Switch {
              for
            }
            ... on Abstract {
              as: int
            }
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
