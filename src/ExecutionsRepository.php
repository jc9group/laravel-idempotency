<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency;

use Jc9Group\Idempotency\Exceptions\RepositoryUnavailableException;

interface ExecutionsRepository
{
    /**
     * @param string $executableName
     * @param string $idempotencyKey
     *
     * @throws RepositoryUnavailableException
     */
    public function markAsExecuted(string $executableName, string $idempotencyKey): void;

    /**
     * @param string $executableName
     * @param string $idempotencyKey
     *
     * @return bool
     *
     * @throws RepositoryUnavailableException
     */
    public function hasBeenExecuted(string $executableName, string $idempotencyKey): bool;
}
