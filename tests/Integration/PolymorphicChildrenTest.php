<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren;
use Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\PolymorphicCommonSubChildrenResult;
use Spawnia\Sailor\Tests\TestCase;

class PolymorphicChildrenTest extends TestCase
{
    public function testPolymorphicSubChildren(): void
    {
        $id = '1';
        $name = 'blarg';

        $expected = (object) [
            'nodes' => [
                (object) [
                    'id' => "$id.1",
                    'node' => (object) [
                        'id' => "$id.1.5",
                        '__typename' => 'Task',
                    ],
                    'name' => $name,
                    '__typename' => 'User',
                ],
                (object) [
                    'id' => "$id.2",
                    '__typename' => 'Post',
                ],
                (object) [
                    'id' => "$id.3",
                    'done' => true,
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
                            $id . '.1',
                            PolymorphicCommonSubChildren\Sub\Nodes\Node\Task::make($id . '.1.5'),
                            $name
                        ),
                        PolymorphicCommonSubChildren\Sub\Nodes\Post::make($id . '.2'),
                        PolymorphicCommonSubChildren\Sub\Nodes\Task::make($id . '.3', true),
                    ])
                )
            ));

        $result = PolymorphicCommonSubChildren::execute()->errorFree();
        $polymorphic = $result->data->sub;

        self::assertEquals($expected, $polymorphic->toStdClass());
    }
}
