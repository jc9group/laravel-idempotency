<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency\Implementation;

use Illuminate\Support\Facades\Redis;
use Jc9Group\Idempotency\Exceptions\InconsistentInformationException;
use Jc9Group\Idempotency\Exceptions\RepositoryUnavailableException;
use Jc9Group\Idempotency\IdempotencyKeyDieTimeGetRepository;
use Jc9Group\Idempotency\IdempotencyKeyRepository;

final class RedisIdempotencyKeyRepository implements IdempotencyKeyRepository, IdempotencyKeyDieTimeGetRepository
{
    /**
     * @var \Predis\Client
     */
    private $redisClient;

    private static $prefix = 'idempotency_key';

    public function __construct()
    {
        $this->redisClient = Redis::connection()->client();
    }

    public function set(string $key, \DateTime $dieTime): void
    {
        try {
            $key = $this->getFullKey($key);

            $this->redisClient->set($key, $dieTime->format(\DateTime::ATOM));

            $this->redisClient->expireat($key, $dieTime->getTimestamp());
        } catch (\Throwable $exception) {
            throw new RepositoryUnavailableException($exception->getMessage(), 0, $exception);
        }
    }

    public function isExists(string $key): bool
    {
        try {
            if (
            (bool)$this->redisClient->exists(
                $this->getFullKey($key)
            )
            ) {
                return true;
            }
            return false;
        } catch (\Throwable $exception) {
            throw new RepositoryUnavailableException($exception->getMessage(), 0, $exception);
        }
    }

    public function getDieTime(string $key): ?\DateTime
    {
        try {
            $value = $this->redisClient->get($this->getFullKey($key));
        } catch (\Throwable $exception) {
            throw new RepositoryUnavailableException($exception->getMessage(), 0, $exception);
        }

        if (null !== $value) {
            if (is_bool($dateTime = \DateTime::createFromFormat(\DateTime::ATOM, $value))) {
                throw new InconsistentInformationException(sprintf('Die time not in %s format', \DateTime::ATOM));
            }
            return $dateTime;
        }

        return null;
    }

    private function getFullKey(string $key): string
    {
        return self::$prefix . '_' . $key;
    }
}
