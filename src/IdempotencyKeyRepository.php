<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency;

use Jc9Group\Idempotency\Exceptions\RepositoryUnavailableException;

interface IdempotencyKeyRepository
{
    /**
     * @param string    $key
     * @param \DateTime $dieTime
     *
     * @throws RepositoryUnavailableException
     */
    public function set(string $key, \DateTime $dieTime): void;

    /**
     * @param string $key
     *
     * @return bool
     *
     * @throws RepositoryUnavailableException
     */
    public function isExists(string $key): bool;
}
