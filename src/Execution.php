<?php

declare(strict_types=1);

namespace Jc9Group\Idempotency;

class Execution
{
    /**
     * @var string|null
     */
    private $executionResult;

    public function __construct(?string $executionResult)
    {
        $this->executionResult = $executionResult;
    }

    public function getExecutionResult(): ?string
    {
        return $this->executionResult;
    }
}
