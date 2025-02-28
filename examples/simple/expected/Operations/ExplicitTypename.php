<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\ExplicitTypename\ExplicitTypenameResult>
 */
class ExplicitTypename extends \Spawnia\Sailor\Operation
{
    public static function execute(): ExplicitTypename\ExplicitTypenameResult
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
        return /* @lang GraphQL */ 'query ExplicitTypename {
          __typename
          singleObject {
            __typename
            ... on SomeObject {
              value
            }
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
