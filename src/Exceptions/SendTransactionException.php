<?php

namespace GloCurrency\UnionBank\Exceptions;

use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Enums\TransactionStateCodeEnum;
use BrokeYourBike\UnionBank\Enums\ErrorCodeEnum;
use BrokeYourBike\UnionBank\Client;

final class SendTransactionException extends \RuntimeException
{
    private TransactionStateCodeEnum $stateCode;
    private string $stateCodeReason;

    public function __construct(TransactionStateCodeEnum $stateCode, string $stateCodeReason, ?\Throwable $previous = null)
    {
        $this->stateCode = $stateCode;
        $this->stateCodeReason = $stateCodeReason;

        parent::__construct($stateCodeReason, 0, $previous);
    }

    public function getStateCode(): TransactionStateCodeEnum
    {
        return $this->stateCode;
    }

    public function getStateCodeReason(): string
    {
        return $this->stateCodeReason;
    }

    public static function stateNotAllowed(Transaction $transaction): self
    {
        $className = $transaction::class;
        $message = "{$className} state_code `{$transaction->state_code->value}` not allowed";
        return new static(TransactionStateCodeEnum::STATE_NOT_ALLOWED, $message);
    }

    public static function apiRequestException(\Throwable $e): self
    {
        $className = Client::class;
        $message = "Exception during {$className} request with message: `{$e->getMessage()}`";
        return new static(TransactionStateCodeEnum::API_REQUEST_EXCEPTION, $message);
    }

    public static function unexpectedErrorCode(string $code): self
    {
        $className = ErrorCodeEnum::class;
        $message = "Unexpected {$className}: `{$code}`";
        return new static(TransactionStateCodeEnum::UNEXPECTED_ERROR_CODE, $message);
    }
}
