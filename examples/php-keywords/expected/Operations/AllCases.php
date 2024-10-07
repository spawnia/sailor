<?php declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\PhpKeywords\Operations\AllCases\AllCasesResult>
 */
class AllCases extends \Spawnia\Sailor\Operation
{
    public static function execute(): AllCases\AllCasesResult
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
        return /* @lang GraphQL */ 'query AllCases {
          __typename
          cases {
            __typename
            id
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
