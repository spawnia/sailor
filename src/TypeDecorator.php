<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Nette\PhpGenerator\ClassType;

/**
 * Enhances a generated class type for a type.
 */
interface TypeDecorator
{
    /**
     * Return a new or modified version of the given code representation of a class type.
     */
    public function decorate(ClassType $class): ClassType;
}
