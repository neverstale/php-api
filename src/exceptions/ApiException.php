<?php

namespace neverstale\api\exceptions;

use Throwable;

/**
 * Neverstale API Exception
 *
 * @author Neverstale
 * @package neverstale/api
 * @since 1.0.0
 * @see http://docs.neverstale.io/api/
 */
class ApiException extends \Exception
{
    protected int $status;
    /**
     * @var array<array<string>>
     */
    protected array $headers;
    /**
     * @param array<array<string>> $headers
     */
    public function __construct(
        int $status,
        string $message,
        ?Throwable $previous = null,
        array $headers = [],
        int $code = 0
    ) {
        $this->status = $status;
        $this->headers = $headers;
        parent::__construct($message, $code, $previous);
    }
    public function getStatus(): int
    {
        return $this->status;
    }
    /**
     * Get the headers.
     *
     * @return array<array<string>>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
