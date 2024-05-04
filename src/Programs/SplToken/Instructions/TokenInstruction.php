<?php

namespace Attestto\SolanaPhpSdk\Programs\SplToken\Instructions;
class TokenInstruction
{
    const InitializeMint = 0;
    const InitializeAccount = 1;
    const InitializeMultisig = 2;
    const Transfer = 3;
    const Approve = 4;
    const Revoke = 5;
    const SetAuthority = 6;
    const MintTo = 7;
    const Burn = 8;
    const CloseAccount = 9;
    const FreezeAccount = 10;
    const ThawAccount = 11;
    const TransferChecked = 12;
    const ApproveChecked = 13;
    const MintToChecked = 14;
    const BurnChecked = 15;
    const InitializeAccount2 = 16;
    const SyncNative = 17;
    const InitializeAccount3 = 18;
    const InitializeMultisig2 = 19;
    const InitializeMint2 = 20;
    const GetAccountDataSize = 21;
    const InitializeImmutableOwner = 22;
    const AmountToUiAmount = 23;
    const UiAmountToAmount = 24;
    const InitializeMintCloseAuthority = 25;
    const TransferFeeExtension = 26;
    const ConfidentialTransferExtension = 27;
    const DefaultAccountStateExtension = 28;
    const Reallocate = 29;
    const MemoTransferExtension = 30;
    const CreateNativeMint = 31;
    const InitializeNonTransferableMint = 32;
    const InterestBearingMintExtension = 33;
    const CpiGuardExtension = 34;
    const InitializePermanentDelegate = 35;
    const TransferHookExtension = 36;
    const MetadataPointerExtension = 39;
    const GroupPointerExtension = 40;
    const GroupMemberPointerExtension = 41;
}
