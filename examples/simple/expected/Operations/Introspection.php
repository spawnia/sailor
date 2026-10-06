<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\Introspection\IntrospectionResult>
 */
class Introspection extends \Spawnia\Sailor\Operation
{
    public static function execute(): Introspection\IntrospectionResult
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
        return /* @lang GraphQL */ 'query Introspection {
          __typename
          __schema {
            __typename
            queryType {
              __typename
              name
            }
          }
          __type(name: "Query") {
            __typename
            name
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
