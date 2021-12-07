<?php declare(strict_types=1);

namespace Spawnia\Sailor\EnumSrc;

use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\Codegen\EnumGenerator;

class CustomEnumGenerator extends EnumGenerator
{
    protected function decorateClass(ClassType $class): ClassType
    {
        $class->addExtend(Enum::class);

        return parent::decorateClass($class);
    }
}
