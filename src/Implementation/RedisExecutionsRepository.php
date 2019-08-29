<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency\Implementation;

use Jc9Group\Idempotency\ExecutionsRepository;

final class RedisExecutionsRepository implements ExecutionsRepository
{
    public function markAsExecuted(string $executableName, string $idempotencyKey): void
    {
        // TODO: Implement markAsExecuted() method.
    }
    
    public function hasBeenExecuted(string $executableName, string $idempotencyKey): bool
    {
        // TODO: Implement hasBeenExecuted() method.
    }
}
