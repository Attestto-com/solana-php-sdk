# Solana PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/attestto/solana-php-sdk.svg?style=flat-square)](https://packagist.org/packages/attestto/solana-php-sdk)
[![GitHub Tests Action Status](https://github.com/Attestto-com/solana-php-sdk/actions/workflows/run-tests.yml/badge.svg?branch=master)](https://github.com/Attestto-com/solana-php-sdk/actions/workflows/run-tests.yml)
[![Coverage (CodeCov)](https://codecov.io/github/Attestto-com/solana-php-sdk/graph/badge.svg?token=M12LECZ9QE)](https://codecov.io/github/Attestto-com/solana-php-sdk)
---

:notice: Forked from:  [verze-app/solana-php-sdk](https://github.com/verze-app/solana-php-sdk)

---

Simple PHP SDK for Solana.

## Installation

You can install the package via composer:

```bash
composer require attestto/solana-php-sdk
```

## Usage

### Using the Solana simple client

You can use the `Connection` class for convenient access to API methods. Some are defined in the code:

```php
use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\SolanaRpcClient;

// Using a defined method
$sdk = new Connection(new SolanaRpcClient(SolanaRpcClient::MAINNET_ENDPOINT));
$accountInfo = $sdk->getAccountInfo('4fYNw3dojWmQ4dXtSGE9epjRGy9pFSx62YypT7avPYvA');
var_dump($accountInfo);
```

For all the possible methods, see the [API documentation](https://docs.solana.com/developing/clients/jsonrpc-api).

### Directly using the RPC client

The `Connection` class is just a light convenience layer on top of the RPC client. You can, if you want, use the client directly, which allows you to work with the full `Response` object:

```php
use Attestto\SolanaPhpSdk\SolanaRpcClient;

$client = new SolanaRpcClient(SolanaRpcClient::MAINNET_ENDPOINT);
$accountInfoResponse = $client->call('getAccountInfo', ['4fYNw3dojWmQ4dXtSGE9epjRGy9pFSx62YypT7avPYvA']);
$accountInfoBody = $accountInfoResponse->json();
$accountInfoStatusCode = $accountInfoResponse->getStatusCode();
``````

### Transactions

Here is working example of sending a transfer instruction to the Solana blockchain, you may overrride the Endpoint with a custom RPC endpoint.:

```php
$client = new SolanaRpcClient(SolanaRpcClient::DEVNET_ENDPOINT);
$connection = new Connection($client);
$fromPublicKey = KeyPair::fromSecretKey([...]);
$toPublicKey = new PublicKey('J3dxNj7nDRRqRRXuEMynDG57DkZK4jYRuv3Garmb1i99');
$instruction = SystemProgram::transfer(
    $fromPublicKey->getPublicKey(),
    $toPublicKey,
    6
);

$transaction = new Transaction(null, null, $fromPublicKey->getPublicKey());
$transaction->add($instruction);

$txHash = $connection->sendTransaction($transaction, $fromPublicKey);
```

### Borsh Deserialize & Deserialize

For Borsh serialization/deseralization to work, a class::SCHEMA object reflecting the Program Structs, based on the program IDL must be passed or defined. e.g.

```php
class DidData
{

    use BorshDeserializable;


    public const SCHEMA = [
        VerificationMethodStruct::class => VerificationMethodStruct::SCHEMA[VerificationMethodStruct::class],
        ServiceStruct::class => ServiceStruct::SCHEMA[ServiceStruct::class],
        self::class => [
            'kind' => 'struct',
            'fields' => [
                ['offset', 'u64'],
                ['version', 'u8'],
                ['bump', 'u8'],
                ['nonce', 'u64'],
                ['initialVerificationMethod', 'string'],
                ['flags', 'u16'],
                ['methodType', 'u8'],
                ['keyData', ['u8']],
                ['verificationMethods', [VerificationMethodStruct::class]],
                ['services', [ServiceStruct::class]],
                ['nativeControllers', ['pubKey']],
                ['otherControllers', ['string']],
            ],
        ],
    ];

    public static function fromBuffer(array $buffer): self
    {
        return Borsh::deserialize(self::SCHEMA, self::class, $buffer);
    }
}
```

Note: This project is in alpha, the code to generate instructions is still being worked on `$instruction = SystemProgram::abc()`

## Roadmap (WIP)

1. Borsh serialize and deserialize.
2. Improved documentation.
3. Build out more of the Connection, SystemProgram, TokenProgram, MetaplexProgram classes.
4. Improve abstractions around working with binary data.
5. Optimizations:
   1. Leverage PHP more.
   2. Better cache `$recentBlockhash` when sending transactions.
6. Suggestions? Open an issue or PR :D

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email the maintainers (see composer.json) instead of using the issue tracker.

## Credits

- [Matt Stauffer](https://github.com/mattstauffer) (Original creator)
- [Zach Vander Velden](https://github.com/exzachlyvv) (Metadata wizard)
- [Neverything](https://github.com/verze-app/solana-php-sdk/graphs/contributors) (Previous Maintainer)
- [All Contributors](../../contributors)
  
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
