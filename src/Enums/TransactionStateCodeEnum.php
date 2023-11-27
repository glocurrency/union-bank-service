<?php

namespace GloCurrency\UnionBank\Enums;

use GloCurrency\MiddlewareBlocks\Enums\ProcessingItemStateCodeEnum as MProcessingItemStateCodeEnum;
use BrokeYourBike\UnionBank\Enums\PaymentStatusEnum;
use BrokeYourBike\UnionBank\Enums\ErrorCodeEnum;

enum TransactionStateCodeEnum: string
{
    case LOCAL_UNPROCESSED = 'local_unprocessed';
    case LOCAL_EXCEPTION = 'local_exception';
    case STATE_NOT_ALLOWED = 'state_not_allowed';
    case API_REQUEST_EXCEPTION = 'api_request_exception';
    case NO_ERROR_CODE_PROPERTY = 'no_error_code_property';
    case UNEXPECTED_ERROR_CODE = 'unexpected_error_code';
    case NO_STATUS_CODE_PROPERTY = 'no_transaction_status_property';
    case UNEXPECTED_STATUS_CODE = 'unexpected_transaction_status';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case CANCELED = 'canceled';
    case API_ERROR = 'api_error';
    case API_TIMEOUT = 'api_timeout';
    case RECIPIENT_ACCOUNT_VALIDATION_FAILED = 'recipient_account_validation_failed';

    public static function makeFromErrorCode(ErrorCodeEnum $errorCode): self
    {
        return match ($errorCode) {
            ErrorCodeEnum::SUCCESS => TransactionStateCodeEnum::PROCESSING,
            ErrorCodeEnum::PROCESSED => TransactionStateCodeEnum::PROCESSING,
            ErrorCodeEnum::RECIPIENT_ACCOUNT_INVALID => TransactionStateCodeEnum::RECIPIENT_ACCOUNT_VALIDATION_FAILED,
            ErrorCodeEnum::FORMAT_ERROR => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::OTHERS_TYPES_OF_ERRORS => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::BENEFICIARY_AND_ORIGINAL_AMOUNT_MUST_BE_SAME => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::UNPROCESSIBLE_REQUEST => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::MISMATCHED_OR_NOT_TRANSFERABLE_CURRENCIES => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::CANNOT_TRANSFER_NGN_TO_A_NON_NGN_ACCOUNT => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::HASH_VALUE_INVALID => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::DUPLICATE_TRANSACTION_PIN => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::MERCHANT_CODE_INVALID => TransactionStateCodeEnum::API_ERROR,
        };
    }

    public static function makeFromPaymentStatus(PaymentStatusEnum $statusCode): self
    {
        return match ($statusCode) {
            PaymentStatusEnum::REVERSAL_RETRY => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::ACCOUNT_VERIFICATION_RETRY => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::POSTING_RETRY => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::NAME_ENQUIRY_RETRY => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::INTERBANK_TRANSFER_RETRY => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::TRANSACTION_REVERSAL_FAILED => TransactionStateCodeEnum::API_ERROR,
            PaymentStatusEnum::DOWNLOAD_ACKNOWLEGEMENT_COMPLETED_AND_AWAITING_PROCESSING => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::PENDING_ACCOUNT_VERIFICATION => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::PENDING_NAME_ENQUIRY => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::PENDING_POSTING => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::PENDING_INTERBANK_TRANSFER => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::PENDING_NOTIFICATION_BACK_TO_SMALLWORLD => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::SUCCESSFUL => TransactionStateCodeEnum::PAID,
            PaymentStatusEnum::FAILED => TransactionStateCodeEnum::FAILED,
            PaymentStatusEnum::AWAITING_MANUAL_POSTING => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::AWAITING_MANUAL_INTERBANK_NAME_ENQUIRY => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::AWAITING_MANUAL_INTERBANK_FUND_TRANSFER => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::AWAITING_POSTING_REVERSAL => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::FUND_TRANSFER_TIMEOUT => TransactionStateCodeEnum::API_TIMEOUT,
            PaymentStatusEnum::AWAITING_DOWNLOAD_STATUSFEEDBACK => TransactionStateCodeEnum::PROCESSING,
            PaymentStatusEnum::ORDER_CANCELLED => TransactionStateCodeEnum::CANCELED,
            PaymentStatusEnum::ACCOUNT_VALIDATION_FAILED => TransactionStateCodeEnum::RECIPIENT_ACCOUNT_VALIDATION_FAILED,
            PaymentStatusEnum::PAYMENT_SUCCESSFUL_BUT_SMALLWORLD_REJECTED_FEEDBACK => TransactionStateCodeEnum::API_ERROR,
        };
    }

    /**
     * Get the ProcessingItem state based on Transaction state.
     */
    public function getProcessingItemStateCode(): MProcessingItemStateCodeEnum
    {
        return match ($this) {
            self::LOCAL_UNPROCESSED => MProcessingItemStateCodeEnum::PENDING,
            self::LOCAL_EXCEPTION => MProcessingItemStateCodeEnum::MANUAL_RECONCILIATION_REQUIRED,
            self::STATE_NOT_ALLOWED => MProcessingItemStateCodeEnum::EXCEPTION,
            self::API_REQUEST_EXCEPTION => MProcessingItemStateCodeEnum::EXCEPTION,
            self::NO_ERROR_CODE_PROPERTY => MProcessingItemStateCodeEnum::EXCEPTION,
            self::UNEXPECTED_ERROR_CODE => MProcessingItemStateCodeEnum::EXCEPTION,
            self::NO_STATUS_CODE_PROPERTY => MProcessingItemStateCodeEnum::EXCEPTION,
            self::UNEXPECTED_STATUS_CODE => MProcessingItemStateCodeEnum::EXCEPTION,
            self::PROCESSING => MProcessingItemStateCodeEnum::PROVIDER_PENDING,
            self::PAID => MProcessingItemStateCodeEnum::PROCESSED,
            self::CANCELED => MProcessingItemStateCodeEnum::TRANSACTION_CANCELED_BY_PROVIDER,
            self::FAILED => MProcessingItemStateCodeEnum::MANUAL_RECONCILIATION_REQUIRED,
            self::API_ERROR => MProcessingItemStateCodeEnum::PROVIDER_NOT_ACCEPTING_TRANSACTIONS,
            self::API_TIMEOUT => MProcessingItemStateCodeEnum::PROVIDER_TIMEOUT,
            self::RECIPIENT_ACCOUNT_VALIDATION_FAILED => MProcessingItemStateCodeEnum::RECIPIENT_BANK_ACCOUNT_INVALID,
        };
    }
}
