<?php

namespace neverstale\api\models;

use neverstale\api\Client;

/**
 * Class TransactionResult
 * @package neverstale\api\models
 *
 */
class TransactionResult extends BaseModel
{
    public string $message;
    public string $status;
    /**
     * @var array<string>|null
     */
    public ?array $errors;
    public mixed $data;

    public function getWasSuccess(): bool
    {
        return $this->status === Client::STATUS_SUCCESS;
    }
    public function getWasError(): bool
    {
        return $this->status === Client::STATUS_ERROR;
    }
}
