<?php declare(strict_types=1);

namespace Spawnia\Sailor\Client;

use Spawnia\Sailor\Response;

class GuzzlePromiseAdapter implements \Spawnia\Sailor\PromiseInterface
{
    private \GuzzleHttp\Promise\PromiseInterface $guzzlePromise;

    public function __construct(\GuzzleHttp\Promise\PromiseInterface $guzzlePromise)
    {
        $this->guzzlePromise = $guzzlePromise;
    }

    public function otherwise(callable $onRejected): \GuzzleHttp\Promise\PromiseInterface
    {
        $this->guzzlePromise->otherwise($onRejected);

        return $this;
    }

    public function getState(): string
    {
        return $this->guzzlePromise->getState();
    }

    public function resolve($value): void
    {
        $this->guzzlePromise->resolve($value);
    }

    public function reject($reason): void
    {
        $this->guzzlePromise->reject($reason);
    }

    public function cancel(): void
    {
        $this->guzzlePromise->cancel();
    }

    public function wait(bool $unwrap = true): Response
    {
        return $this->guzzlePromise->wait();
    }

    public function then(?callable $onFulfilled = null, ?callable $onRejected = null): \GuzzleHttp\Promise\PromiseInterface
    {
        $this->guzzlePromise->then($onFulfilled, $onRejected);

        return $this;
    }
}
