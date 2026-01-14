<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use Spawnia\Sailor\Codegen\ObjectLikeBuilder;
use Spawnia\Sailor\Codegen\OperationBuilder;
use Spawnia\Sailor\Codegen\OperationStack;
use Spawnia\Sailor\Tests\TestCase;

/**
 * Test that demonstrates the bug where inline fragments with the same field name
 * but different concrete types only generate the first type class.
 */
final class OperationStackTest extends TestCase
{
    public function testSetSelectionMergesMultipleTypesForSameNamespace(): void
    {
        $operation = $this->createMock(OperationBuilder::class);
        $stack = new OperationStack($operation);

        $namespace = 'App\\Generated\\GetCompanyUpdates\\Changes\\Article';

        // Simulate processing first inline fragment (NotePublishedCompanyUpdateChange)
        $noteBuilder = $this->createMock(ObjectLikeBuilder::class);
        $stack->setSelection($namespace, ['Note' => $noteBuilder]);

        // Simulate processing second inline fragment (ResearchUpdatePublishedCompanyUpdateChange)
        $updateBuilder = $this->createMock(ObjectLikeBuilder::class);
        $stack->setSelection($namespace, ['Update' => $updateBuilder]);

        // Simulate processing third inline fragment (InitiationCoveragePublishedCompanyUpdateChange)
        $initiationCoverageBuilder = $this->createMock(ObjectLikeBuilder::class);
        $stack->setSelection($namespace, ['InitiationCoverage' => $initiationCoverageBuilder]);

        // Get the merged selections
        $selections = $stack->selection($namespace);

        // Assert all three types are present (this fails with the bug)
        self::assertArrayHasKey('Note', $selections, 'Note type should be present');
        self::assertArrayHasKey('Update', $selections, 'Update type should be present');
        self::assertArrayHasKey('InitiationCoverage', $selections, 'InitiationCoverage type should be present');

        // Verify we have exactly 3 types
        self::assertCount(3, $selections, 'Should have all 3 article types');

        // Verify the builders are the correct instances
        self::assertSame($noteBuilder, $selections['Note']);
        self::assertSame($updateBuilder, $selections['Update']);
        self::assertSame($initiationCoverageBuilder, $selections['InitiationCoverage']);
    }

    public function testSetSelectionDoesNotDuplicateTypesWhenCalledMultipleTimes(): void
    {
        $operation = $this->createMock(OperationBuilder::class);
        $stack = new OperationStack($operation);

        $namespace = 'App\\Generated\\GetCompanyUpdates\\Changes\\Article';

        $noteBuilder = $this->createMock(ObjectLikeBuilder::class);

        // Set the same type multiple times
        $stack->setSelection($namespace, ['Note' => $noteBuilder]);
        $stack->setSelection($namespace, ['Note' => $noteBuilder]);

        $selections = $stack->selection($namespace);

        // Should still only have one Note entry
        self::assertCount(1, $selections);
        self::assertArrayHasKey('Note', $selections);
    }

    public function testSetSelectionHandlesDifferentNamespacesSeparately(): void
    {
        $operation = $this->createMock(OperationBuilder::class);
        $stack = new OperationStack($operation);

        $namespace1 = 'App\\Generated\\Query1\\Article';
        $namespace2 = 'App\\Generated\\Query2\\Article';

        $noteBuilder1 = $this->createMock(ObjectLikeBuilder::class);
        $noteBuilder2 = $this->createMock(ObjectLikeBuilder::class);

        $stack->setSelection($namespace1, ['Note' => $noteBuilder1]);
        $stack->setSelection($namespace2, ['Note' => $noteBuilder2]);

        $selections1 = $stack->selection($namespace1);
        $selections2 = $stack->selection($namespace2);

        // Each namespace should have its own selection
        self::assertArrayHasKey('Note', $selections1);
        self::assertArrayHasKey('Note', $selections2);
        self::assertSame($noteBuilder1, $selections1['Note']);
        self::assertSame($noteBuilder2, $selections2['Note']);
    }
}
