<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency\Implementation;

use Illuminate\Support\Facades\Redis;
use Jc9Group\Idempotency\Exceptions\RepositoryUnavailableException;
use Jc9Group\Idempotency\IdempotencyKeyDieTimeGetRepository;
use Jc9Group\Idempotency\IdempotencyKeyRepository;

final class RedisIdempotencyKeyRepository implements IdempotencyKeyRepository, IdempotencyKeyDieTimeGetRepository
{
    /**
     * @var \Predis\Client
     */
    private $redisClient;

    public function __construct()
    {
        $this->redisClient = Redis::connection()->client();
    }

    public function set(string $key, \DateTime $dieTime): void
    {
        try {
            $this->redisClient->set($this->getFullKey($key), $dieTime->format(\DateTime::ATOM));
        } catch (\Throwable $exception) {
            throw new RepositoryUnavailableException($exception->getMessage(), 0, $exception);
        }
    }

    public function isExists(string $key): bool
    {
        if (
        $this->redisClient->exists(
            $this->getFullKey($key)
        )
        ) {
            return true;
        }
        return false;
    }

    public function getDieTime(string $key): ?\DateTime
    {
        // TODO: Implement get() method.
    }

    private function getFullKey(string $key): string
    {
        return '';
    }
}
