<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use Nette\PhpGenerator\ClassType;

class OperationStack
{
    public OperationBuilder $operation;

    public ClassType $result;

    public ClassType $errorFreeResult;

    /** @var array<int, array<string, ObjectLikeBuilder>> */
    public array $selectionStack = [];

    /** @var array<int, ClassType> */
    public array $selectionStorage = [];

    public function __construct(OperationBuilder $operation)
    {
        $this->operation = $operation;
    }

    /**
     * @param  array<string, ObjectLikeBuilder>  $selection
     */
    public function pushSelection(array $selection): void
    {
        $this->selectionStack[] = $selection;
    }

    /**
     * When building the current selection is finished, we move it to storage.
     */
    public function popSelection(): void
    {
        $selection = array_pop($this->selectionStack);
        if (null === $selection) {
            throw new \Exception('Emptied out the selection stack too quickly.');
        }

        foreach ($selection as $builder) {
            $this->selectionStorage[] = $builder->build();
        }
    }

    /**
     * @return array<string, ObjectLikeBuilder>
     */
    public function peekSelection(): array
    {
        $selection = end($this->selectionStack);
        if (false === $selection) {
            throw new \Exception('The selection stack was unexpectedly empty.');
        }

        return $selection;
    }
}
