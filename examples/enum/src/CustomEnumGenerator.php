<?php

declare(strict_types=1);

namespace Spawnia\Sailor\EnumSrc;

use GraphQL\Type\Definition\EnumType;
use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\Codegen\EnumGenerator;

class CustomEnumGenerator extends EnumGenerator
{
    protected function decorateClass(EnumType $type, ClassType $class): ClassType
    {
        if ($type->name === 'CustomEnum') {
            $class->addExtend(Enum::class);
        }

        return parent::decorateClass($type, $class);
    }
}
