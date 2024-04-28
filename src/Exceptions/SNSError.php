<?php

namespace Attestto\SolanaPhpSdk\Exceptions;

use Exception;
use Throwable;


class SNSError extends Exception {
    public $type;
    public $name;

    const SymbolNotFound = "SymbolNotFound";
    const InvalidSubdomain = "InvalidSubdomain";
    const FavouriteDomainNotFound = "FavouriteDomainNotFound";
    const MissingParentOwner = "MissingParentOwner";
    const U32Overflow = "U32Overflow";
    const InvalidBufferLength = "InvalidBufferLength";
    const U64Overflow = "U64Overflow";
    const NoRecordData = "NoRecordData";
    const InvalidRecordData = "InvalidRecordData";
    const UnsupportedRecord = "UnsupportedRecord";
    const InvalidEvmAddress = "InvalidEvmAddress";
    const InvalidInjectiveAddress = "InvalidInjectiveAddress";
    const InvalidARecord = "InvalidARecord";
    const InvalidAAAARecord = "InvalidAAAARecord";
    const InvalidRecordInput = "InvalidRecordInput";
    const InvalidSignature = "InvalidSignature";
    const AccountDoesNotExist = "AccountDoesNotExist";
    const MultipleRegistries = "MultipleRegistries";
    const InvalidReverseTwitter = "InvalidReverseTwitter";
    const NoAccountData = "NoAccountData";
    const InvalidInput = "InvalidInput";
    const InvalidDomain = "InvalidDomain";
    const InvalidCustomBg = "InvalidCustomBackground";
    const UnsupportedSignature = "UnsupportedSignature";
    const RecordDoestNotSupportGuardianSig = "RecordDoestNotSupportGuardianSig";
    const RecordIsNotSigned = "RecordIsNotSigned";
    const UnsupportedSignatureType = "UnsupportedSignatureType";
    const InvalidSolRecordV2 = "InvalidSolRecordV2";
    const MissingVerifier = "MissingVerifier";
    const PythFeedNotFound = "PythFeedNotFound";

    public function __construct($type, $message = null) {
        parent::__construct($message);
        $this->type = $type;
        $this->name = "SNSError";
    }
}
