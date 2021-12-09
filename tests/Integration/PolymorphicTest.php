<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Polymorphic\Operations\AllMembers;
use Spawnia\Sailor\Polymorphic\Operations\AllMembers\AllMembersResult;
use Spawnia\Sailor\Polymorphic\Operations\NodeMembers;
use Spawnia\Sailor\Polymorphic\Operations\NodeMembers\NodeMembersResult;
use Spawnia\Sailor\Polymorphic\Operations\UserOrPost;
use Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node;
use Spawnia\Sailor\Polymorphic\Operations\UserOrPost\UserOrPostResult;
use Spawnia\Sailor\Tests\TestCase;

class PolymorphicTest extends TestCase
{
    public function testUserOrPost(): void
    {
        $id = '1';
        $name = 'blarg';

        UserOrPost::mock()
            ->expects('execute')
            ->once()
            ->with($id)
            ->andReturn(UserOrPostResult::fromStdClass((object) [
                'data' => (object) [
                    'node' => (object) [
                        '__typename' => 'User',
                        'id' => $id,
                        'name' => $name,
                    ],
                ],
            ]));

        $result = UserOrPost::execute($id)->errorFree();
        $user = $result->data->node;

        self::assertInstanceOf(Node\User::class, $user);
        self::assertSame($id, $user->id);
        self::assertSame($name, $user->name);
    }

    public function testAllMembers(): void
    {
        $name = 'blarg';
        $code = 'XYZ';

        AllMembers::mock()
            ->expects('execute')
            ->once()
            ->with()
            ->andReturn(AllMembersResult::fromStdClass((object) [
                'data' => (object) [
                    'members' => [
                        (object) [
                            '__typename' => 'User',
                            'name' => $name,
                        ],
                        (object) [
                            '__typename' => 'Organization',
                            'code' => $code,
                        ],
                    ],
                ],
            ]));

        $result = AllMembers::execute()->errorFree();
        $members = $result->data->members;

        self::assertCount(2, $members);
        [$user, $organization] = $members;

        self::assertInstanceOf(AllMembers\Members\User::class, $user);
        self::assertSame($name, $user->name);

        self::assertInstanceOf(AllMembers\Members\Organization::class, $organization);
        self::assertSame($code, $organization->code);
    }

    public function testNodeMembers(): void
    {
        $id = 'foo';

        NodeMembers::mock()
            ->expects('execute')
            ->once()
            ->with()
            ->andReturn(NodeMembersResult::fromStdClass((object) [
                'data' => (object) [
                    'members' => [
                        (object) [
                            '__typename' => 'User',
                            'id' => $id,
                        ],
                        (object) [
                            '__typename' => 'Organization',
                        ],
                    ],
                ],
            ]));

        $result = NodeMembers::execute()->errorFree();
        $members = $result->data->members;

        self::assertCount(2, $members);
        [$user, $organization] = $members;

        self::assertInstanceOf(NodeMembers\Members\User::class, $user);
        self::assertSame('User', $user->__typename);
        self::assertSame($id, $user->id);

        self::assertInstanceOf(NodeMembers\Members\Organization::class, $organization);
        self::assertSame('Organization', $organization->__typename);
    }
}
