<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency;

use Jc9Group\Idempotency\Exceptions\InconsistentInformationException;
use Jc9Group\Idempotency\Exceptions\RepositoryUnavailableException;

interface IdempotencyKeyDieTimeGetRepository
{
    /**
     * @param string $key
     *
     * @return \DateTime|null
     *                       
     * @throws RepositoryUnavailableException
     * @throws InconsistentInformationException
     */
    public function getDieTime(string $key): ?\DateTime;
}
