<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use Nette\PhpGenerator\ClassType;

class OperationSet
{
    /** @var ClassType */
    public $operation;

    /** @var ClassType[] */
    public $selectionStack = [];

    /** @var ClassType[] */
    public $selectionStorage = [];

    public function __construct(ClassType $operation)
    {
        $this->operation = $operation;
    }

    public function pushSelection(ClassType $selectionClass): void
    {
        $this->selectionStack []= $selectionClass;
    }

    /**
     * When building the current selection is finished, we move it to storage.
     */
    public function popSelection(): void
    {
        $this->selectionStorage []= array_pop($this->selectionStack);
    }

    public function peekSelection(): ClassType
    {
        return end($this->selectionStack);
    }
}
