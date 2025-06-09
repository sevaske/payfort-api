<?php

namespace Sevaske\PayfortApi\Enums;

enum PayfortStatusEnum: string
{
    // Invalid request
    case InvalidRequest = '00';

    // Order stored
    case OrderStored = '01';

    // Authorization success
    case AuthorizationSuccess = '02';

    // Authorization failed
    case AuthorizationFailed = '03';

    // Capture success
    case CaptureSuccess = '04';

    // Capture failed
    case CaptureFailed = '05';

    // Refund success
    case RefundSuccess = '06';

    // Refund failed
    case RefundFailed = '07';

    // Authorization voided successfully
    case AuthorizationVoidedSuccessfully = '08';

    // Authorization void failed
    case AuthorizationVoidFailed = '09';

    // Incomplete
    case Incomplete = '10';

    // Check status failed
    case CheckStatusFailed = '11';

    // Check status success
    case CheckStatusSuccess = '12';

    // Purchase failure
    case PurchaseFailure = '13';

    // Purchase success
    case PurchaseSuccess = '14';

    // Uncertain transaction
    case UncertainTransaction = '15';

    // Tokenization failed
    case TokenizationFailed = '17';

    // Tokenization success
    case TokenizationSuccess = '18';

    // Transaction pending
    case TransactionPending = '19';

    // On hold
    case OnHold = '20';

    // SDK token creation failure
    case SdkTokenCreationFailure = '21';

    // SDK token creation success
    case SdkTokenCreationSuccess = '22';

    // Failed to process digital wallet service
    case FailedToProcessDigitalWalletService = '23';

    // Digital wallet order processed successfully
    case DigitalWalletOrderProcessedSuccessfully = '24';

    // Check card balance failed
    case CheckCardBalanceFailed = '27';

    // Check card balance success
    case CheckCardBalanceSuccess = '28';

    // Redemption failed
    case RedemptionFailed = '29';

    // Redemption success
    case RedemptionSuccess = '30';

    // Reverse redemption transaction failed
    case ReverseRedemptionTransactionFailed = '31';

    // Reverse redemption transaction success
    case ReverseRedemptionTransactionSuccess = '32';

    // Transaction in review
    case TransactionInReview = '40';

    // Currency conversion success
    case CurrencyConversionSuccess = '42';

    // Currency conversion failed
    case CurrencyConversionFailed = '43';

    // 3DS success
    case ThreeDsSuccess = '44';

    // 3DS failed
    case ThreeDsFailed = '45';

    // Bill creation success
    case BillCreationSuccess = '46';

    // Bill creation failed
    case BillCreationFailed = '47';

    // Generating invoice payment link success
    case GeneratingInvoicePaymentLinkSuccess = '48';

    // Generating invoice payment link failed
    case GeneratingInvoicePaymentLinkFailed = '49';

    // Batch file upload successfully
    case BatchFileUploadSuccessfully = '50';

    // Upload batch file failed
    case UploadBatchFileFailed = '51';

    // Token created successfully
    case TokenCreatedSuccessfully = '52';

    // Token creation failed
    case TokenCreationFailed = '53';

    // Get tokens success
    case GetTokensSuccess = '54';

    // Get tokens failed
    case GetTokensFailed = '55';

    // Reporting request success
    case ReportingRequestSuccess = '56';

    // Reporting request failed
    case ReportingRequestFailed = '57';

    // Token updated successfully
    case TokenUpdatedSuccessfully = '58';

    // Token updated failed
    case TokenUpdatedFailed = '59';

    // Get installment plans successfully
    case GetInstallmentPlansSuccessfully = '62';

    // Get installment plans failed
    case GetInstallmentPlansFailed = '63';

    // Delete token success
    case DeleteTokenSuccess = '66';

    // Get batch results successfully
    case GetBatchResultsSuccessfully = '70';

    // Get batch results failed
    case GetBatchResultsFailed = '71';

    // Batch processing success
    case BatchProcessingSuccess = '72';

    // Batch processing failed
    case BatchProcessingFailed = '73';

    // Bank transfer successfully
    case BankTransferSuccessfully = '74';

    // Bank transfer failed
    case BankTransferFailed = '75';

    // Batch validation successfully
    case BatchValidationSuccessfully = '76';

    // Batch validation failed
    case BatchValidationFailed = '77';

    // Credit card verified successfully
    case CreditCardVerifiedSuccessfully = '80';

    // Failed to verify credit card
    case FailedToVerifyCreditCard = '81';
}
