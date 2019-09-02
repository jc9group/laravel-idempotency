<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency\Implementation;

use Illuminate\Support\Facades\Redis;
use Jc9Group\Idempotency\Exceptions\IdempotencyKeyDoesNotExistsException;
use Jc9Group\Idempotency\Exceptions\InconsistentInformationException;
use Jc9Group\Idempotency\Exceptions\RepositoryUnavailableException;
use Jc9Group\Idempotency\Execution;
use Jc9Group\Idempotency\ExecutionsRepository;
use Jc9Group\Idempotency\IdempotencyKeyDieTimeGetRepository;

final class RedisExecutionsRepository implements ExecutionsRepository
{
    /**
     * @var \Predis\Client
     */
    private $redisClient;

    private $idempotencyKeyDieTimeGetRepository;

    private static $prefix = 'idempotent_execution';

    public function __construct(IdempotencyKeyDieTimeGetRepository $idempotencyKeyDieTimeGetRepository)
    {
        $this->redisClient                        = Redis::connection();
        $this->idempotencyKeyDieTimeGetRepository = $idempotencyKeyDieTimeGetRepository;
    }

    public function markAsExecuted(string $executableName, string $idempotencyKey, string $executionResult = null): void
    {
        try {
            $dieTime = $this->idempotencyKeyDieTimeGetRepository->getDieTime($idempotencyKey);
        } catch (InconsistentInformationException $exception) {
            throw new IdempotencyKeyDoesNotExistsException(
                sprintf('Inconsistent key data "%s"', $exception->getMessage()), $exception->getCode(), $exception
            );
        }

        if (null !== $dieTime) {
            $key = $this->getFullKey($executableName, $idempotencyKey);

            try {
                $this->redisClient->set($key, $executionResult ?? '');
                $this->redisClient->expireat($key, $dieTime->getTimestamp());
            } catch (\Throwable $exception) {
                throw new RepositoryUnavailableException($exception->getMessage(), 0, $exception);
            }

            return;
        }

        throw new IdempotencyKeyDoesNotExistsException(
            sprintf(
                'Idempotency key %s for executable %s was not found',
                $idempotencyKey,
                $executableName
            )
        );
    }

    public function getExecution(string $executableName, string $idempotencyKey): ?Execution
    {
        try {
            $value = $this->redisClient->get($this->getFullKey($executableName, $idempotencyKey));
        } catch (\Throwable $exception) {
            throw new RepositoryUnavailableException($exception->getMessage(), 0, $exception);
        }

        if (null === $value) {
            return null;
        }

        return new Execution('' !== $value ? $value : null);
    }

    private function getFullKey(string $executableName, string $idempotencyKey): string
    {
        return self::$prefix . '_' . $executableName . '_' . $idempotencyKey;
    }
}
