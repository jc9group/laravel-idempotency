<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency\Providers;

use Illuminate\Support\ServiceProvider;
use Jc9Group\Idempotency\ExecutionsRepository;
use Jc9Group\Idempotency\IdempotencyKeyDieTimeGetRepository;
use Jc9Group\Idempotency\IdempotencyKeyRepository;
use Jc9Group\Idempotency\Implementation\RedisExecutionsRepository;
use Jc9Group\Idempotency\Implementation\RedisIdempotencyKeyRepository;

final class LaravelIdempotencyProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            IdempotencyKeyRepository::class,
            RedisIdempotencyKeyRepository::class
        );
        $this->app->bind(
            ExecutionsRepository::class,
            RedisExecutionsRepository::class
        );
        $this->app->bind(
            IdempotencyKeyDieTimeGetRepository::class,
            RedisIdempotencyKeyRepository::class
        );
    }
}
