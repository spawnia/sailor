<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;

class OperationStack
{
    public ClassType $operation;

    public ClassType $result;

    public ClassType $errorFreeResult;

    /** @var array<int, array<string, ClassType>> */
    public array $selectionStack = [];

    /** @var array<int, ClassType> */
    public array $selectionStorage = [];

    public function __construct(ClassType $operation)
    {
        $this->operation = $operation;
    }

    /**
     * @param  array<string, ClassType>  $selection
     */
    public function pushSelection(array $selection): void
    {
        $this->selectionStack [] = $selection;
    }

    /**
     * When building the current selection is finished, we move it to storage.
     */
    public function popSelection(): void
    {
        $selection = array_pop($this->selectionStack);
        if ($selection === null) {
            throw new \Exception('Emptied out the selection stack too quickly.');
        }

        foreach ($selection as $class) {
            $this->selectionStorage [] = $class;
        }
    }

    /**
     * @return array<string, ClassType>
     */
    public function peekSelection(): array
    {
        $selection = end($this->selectionStack);
        if ($selection === false) {
            throw new \Exception('The selection stack was unexpectedly empty.');
        }

        return $selection;
    }

    public function addParameterToOperation(Parameter $parameter): void
    {
        $execute = $this->operation->getMethod('execute');

        $parameters = $execute->getParameters();
        $parameters[] = $parameter;

        $execute->setParameters($parameters);
    }
}
