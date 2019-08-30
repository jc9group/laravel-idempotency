<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency\Implementation;

use Illuminate\Support\Facades\Redis;
use Jc9Group\Idempotency\Exceptions\IdempotencyKeyDoesNotExistsException;
use Jc9Group\Idempotency\Exceptions\InconsistentInformationException;
use Jc9Group\Idempotency\Exceptions\RepositoryUnavailableException;
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
        $this->redisClient                        = Redis::connection()->client();
        $this->idempotencyKeyDieTimeGetRepository = $idempotencyKeyDieTimeGetRepository;
    }

    public function markAsExecuted(string $executableName, string $idempotencyKey): void
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
            $this->redisClient->set($key, $dieTime->format(\DateTime::ATOM));
            $this->redisClient->expireat($key, $dieTime->getTimestamp());
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

    public function hasBeenExecuted(string $executableName, string $idempotencyKey): bool
    {
        try {
            if (
            (bool)$this->redisClient->exists(
                $this->getFullKey($executableName, $idempotencyKey)
            )
            ) {
                return true;
            }
            return false;
        } catch (\Throwable $exception) {
            throw new RepositoryUnavailableException($exception->getMessage(), 0, $exception);
        }
    }

    private function getFullKey(string $executableName, string $idempotencyKey): string
    {
        return self::$prefix . '_' . $executableName . '_' . $idempotencyKey;
    }
}
