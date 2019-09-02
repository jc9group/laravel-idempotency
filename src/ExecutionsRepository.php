<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency;

use Jc9Group\Idempotency\Exceptions\IdempotencyKeyDoesNotExistsException;
use Jc9Group\Idempotency\Exceptions\RepositoryUnavailableException;

interface ExecutionsRepository
{
    /**
     * @param string      $executableName
     * @param string      $idempotencyKey
     * @param string|null $executionResult
     *
     * @throws RepositoryUnavailableException
     * @throws IdempotencyKeyDoesNotExistsException
     */
    public function markAsExecuted(
        string $executableName,
        string $idempotencyKey,
        string $executionResult = null
    ): void;

    /**
     * @param string $executableName
     * @param string $idempotencyKey
     *
     * @return Execution|null
     *
     * @throws RepositoryUnavailableException
     */
    public function getExecution(string $executableName, string $idempotencyKey): ?Execution;
}
