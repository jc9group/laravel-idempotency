# Laravel idempotency repositories

Library for laravel framework to work with requests idempotency.

## Keys

If you want to work with keys - you should use the `IdempotencyKeyRepository`
- `set(string $key, \DateTime $dieTime): void` method to set idempotency key that will be killed at `$dieTime`
- `isExists(string $key): bool` method to check if the key exists

## Executions

If you want to check if some functionality was executed by request with this key you should use `ExecutionsRepository`
- `markAsExecuted(string $executableName, string $idempotencyKey, string $executionResult = null): void` method to mark some functionality as executed and save execution result
- `public function getExecution(string $executableName, string $idempotencyKey): ?Execution` method to check if the functionality has been executed and get execution result

Be careful with executions! Die time of execution mark will be like die time of a key
 