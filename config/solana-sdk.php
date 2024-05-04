<?php

return [

    'TOKEN_PROGRAM_ID' => env('TOKEN_PROGRAM_ID') ?? 'TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA',
    //-------
    'devnet' => [
        'mints' => [
            'usdc' => env('USDC_MINT_DEVNET'),
            'helium' => env('MAINNET_HELIUM_MINT'),
            'solana' => env('MAINNET_SOLANA_MINT'),
        ],
        'network' => [
            'type' => env('NETWORK'),
            'attesto_rpc' => env('ATTESTO_RPC'),
            'fallback_rpc_1' => env('FALLBACK_RPC_1'),
            'fallback_rpc_2' => env('FALLBACK_RPC_2'),
        ],
        'api_keys' => [
            'helius' => env('HELIUS_API_KEY'),
        ],
        'data_source' => env('DATA_SOURCE'),
        'keys' => [
            'dapp_pk' => env('DAPP_PK'), // Signer
            'burn_address' => env('SOLANA_BURN_ADDRESS'), // Not in use
            'treasury_pk' => env('TREASURY_PK'),  // Not in use
            'fees_pk' => env('FEES_PK'), // Fee Taker
        ],
    ],
    'network' => [
        'type' => env('NETWORK'),
        'attesto_rpc' => env('ATTESTO_RPC'),
        'fallback_rpc_1' => env('FALLBACK_RPC_1'),
        'fallback_rpc_2' => env('FALLBACK_RPC_2'),
    ],
    'api_keys' => [
        'helius' => env('HELIUS_API_KEY'),
    ],
    'data_source' => env('DATA_SOURCE'),
    'keys' => [
        'dapp_pk' => env('DAPP_PK'), // Signer
        'burn_address' => env('SOLANA_BURN_ADDRESS'), // Not in use
        'treasury_pk' => env('TREASURY_PK'),  // Not in use
        'fees_pk' => env('FEES_PK'), // Fee Taker
    ],
    'fees' => [
        'key_registration' => env('KEY_REGISTRATION_FEE'),
        'did_registration' => env('DID_REGISTRATION_FEE'),
        'did_update' => env('DID_UPDATE_FEE'),
        'vc_issuing' => env('VC_ISSUING_FEE'),
        'vc_proofing' => env('VC_PROOFING_FEE'),
        'vc_verifying' => env('VC_VERIFYING_FEE'),
        'vc_revoking' => env('VC_REVOKING_FEE'),
    ],
    'subscription_prices' => [
        'monthly' => env('KEY_REGISTRATION_FEE', 0.01),
        'anual' => env('DID_REGISTRATION_FEE', 0.1),
    ],

];
