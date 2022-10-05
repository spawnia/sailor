<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren;
use Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\PolymorphicCommonSubChildrenResult;
use Spawnia\Sailor\Tests\TestCase;

final class PolymorphicChildrenTest extends TestCase
{
    public function testPolymorphicSubChildren(): void
    {
        $id = '1';
        $title = 'blarg';

        $expected = (object) [
            'nodes' => [
                (object) [
                    'id' => "{$id}.1",
                    'node' => (object) [
                        'id' => "{$id}.1.5",
                        '__typename' => 'Task',
                    ],
                    'name' => null,
                    '__typename' => 'User',
                ],
                (object) [
                    'id' => "{$id}.2",
                    'node' => null,
                    'title' => $title,
                    '__typename' => 'Post',
                ],
                (object) [
                    'id' => "{$id}.3",
                    'done' => true,
                    'node' => (object) [
                        'id' => "{$id}.3.5",
                        '__typename' => 'User',
                    ],
                    '__typename' => 'Task',
                ],
            ],
            '__typename' => 'Sub',
        ];

        PolymorphicCommonSubChildren::mock()
            ->expects('execute')
            ->once()
            ->andReturn(PolymorphicCommonSubChildrenResult::fromData(
                PolymorphicCommonSubChildren\PolymorphicCommonSubChildren::make(
                    /* sub: */
                    PolymorphicCommonSubChildren\Sub\Sub::make([
                        PolymorphicCommonSubChildren\Sub\Nodes\User::make(
                            /* id: */
                            "{$id}.1",
                            /* node: */
                            PolymorphicCommonSubChildren\Sub\Nodes\Node\Task::make("{$id}.1.5"),
                            /* name: */
                            null
                        ),
                        PolymorphicCommonSubChildren\Sub\Nodes\Post::make(
                            /* id: */
                            "{$id}.2",
                            /* node: */
                            null,
                            /* title: */
                            $title
                        ),
                        PolymorphicCommonSubChildren\Sub\Nodes\Task::make(
                            /* id: */
                            "{$id}.3",
                            /* done: */
                            true,
                            /* node: */
                            PolymorphicCommonSubChildren\Sub\Nodes\Node\User::make("{$id}.3.5"),
                        ),
                    ])
                )
            ));

        $result = PolymorphicCommonSubChildren::execute()->errorFree();
        $polymorphic = $result->data->sub;
        $stdClass = $polymorphic->toStdClass();

        self::assertEquals($expected, $stdClass);
        self::assertEquals($polymorphic, PolymorphicCommonSubChildren\Sub\Sub::fromStdClass($stdClass));
    }
}
