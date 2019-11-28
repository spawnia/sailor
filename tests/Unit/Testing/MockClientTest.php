<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Testing;

use PHPUnit\Framework\MockObject\MockObject;
use Spawnia\Sailor\Response;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Testing\MockClient;

class MockClientTest extends TestCase
{
    public function testCallsMock(): void
    {
        $query = 'foo';
        $variables = new \stdClass();

        $response = new Response();

        /** @var MockObject&callable $responseMock */
        $responseMock = self::createPartialMock(\stdClass::class, ['__invoke']);
        $responseMock->expects(self::once())
            ->method('__invoke')
            ->with($query, $variables)
            ->willReturn($response);

        $mockClient = new MockClient();
        $mockClient->responseMocks [] = $responseMock;

        self::assertSame($response, $mockClient->request($query, $variables));

        $storedRequest = $mockClient->storedRequests[0];

        self::assertSame($query, $storedRequest->query);
        self::assertSame($variables, $storedRequest->variables);
    }
}
