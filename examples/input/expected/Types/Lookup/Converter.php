<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Types\Lookup;

class Converter extends \Spawnia\Sailor\Convert\OneOfInputConverter
{
    protected function variants(): array
    {
        return [
            'id' => ById::class,
            'ids' => ByIds::class,
        ];
    }

    public static function endpoint(): string
    {
        return 'input';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
