<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use Nette\PhpGenerator\ClassType;

interface ClassGenerator
{
    /** @return iterable<ClassType> */
    public function generate(): iterable;
}
