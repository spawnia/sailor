<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use Nette\PhpGenerator\ClassType;

class OperationStack
{
    public OperationBuilder $operation;

    public ClassType $result;

    public ClassType $errorFreeResult;

    /** @var array<string, array<string, ObjectLikeBuilder>> */
    public array $selections = [];

    public function __construct(OperationBuilder $operation)
    {
        $this->operation = $operation;
    }

    /**
     * @param  array<string, ObjectLikeBuilder>  $selection
     */
    public function setSelection(string $namespace, array $selection): void
    {
        // Ignore if already set, we already were in that subtree
        $this->selections[$namespace] ??= $selection;
    }

    /**
     * @return iterable<ClassType>
     */
    public function buildSelections(): iterable
    {
        foreach ($this->selections as $selection) {
            foreach ($selection as $builder) {
                yield $builder->build();
            }
        }
    }

    /**
     * @return array<string, ObjectLikeBuilder>
     */
    public function selection(string $namespace): array
    {
        return $this->selections[$namespace];
    }
}
