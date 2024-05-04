# Solana PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/attestto/solana-php-sdk.svg?style=flat-square)](https://packagist.org/packages/attestto/solana-php-sdk)
[![GitHub Tests Action Status](https://github.com/Attestto-com/solana-php-sdk/actions/workflows/run-tests.yml/badge.svg?branch=master)](https://github.com/Attestto-com/solana-php-sdk/actions/workflows/run-tests.yml)
[![Coverage (CodeCov)](https://codecov.io/github/Attestto-com/solana-php-sdk/graph/badge.svg?token=M12LECZ9QE)](https://codecov.io/github/Attestto-com/solana-php-sdk)

```js
    ____  __  ______     _____ ____  __    ___    _   _____       _____ ____  __ __
   / __ \/ / / / __ \   / ___// __ \/ /   /   |  / | / /   |     / ___// __ \/ //_/
  / /_/ / /_/ / /_/ /   \__ \/ / / / /   / /| | /  |/ / /| |     \__ \/ / / / ,<   
 / ____/ __  / ____/   ___/ / /_/ / /___/ ___ |/ /|  / ___ |    ___/ / /_/ / /| |  
/_/   /_/ /_/_/       /____/\____/_____/_/  |_/_/ |_/_/  |_|   /____/_____/_/ |_|
```
Simple PHP SDK for interacting with the Transactions, Signatures, Borsh Serialization/Deserialization and RPCs
---
Forked from the Verze repo:  [verze-app/solana-php-sdk](https://github.com/verze-app/solana-php-sdk/pull/53)

---
#### Motivations

- To protect RPC API Keys acting as a Hosted Proxy. 
- To enable Background Data Processing.
- To enable Descentralized Data & Query Caching. 
- To empower PHP Applications to interact with the Solana Network
- To enable Async Jobs and Queries in the Background. 
- To enable Decetralized Websockets (Push Notifications & Subscriptions)
- To reduce Client-Sice RPC Polling, aiming at decongesting the mainnet and devnets.   


## Installation

You can install the package via composer :

```bash
composer require attestto/solana-php-sdk
```
### From this Repository

```bash
git clone https://github.com/Attestto-com/solana-php-sdk.git

cd solana-php-sdk

composer install 

```
### With Docker

- [DockerFile](https://github.com/Attestto-com/solana-php-sdk/blob/master/Dockerfile) 
- [compose-dev.yaml](https://github.com/Attestto-com/solana-php-sdk/blob/master/compose-dev.yaml)

```bash
docker build -t solana-php-sdk .
```
then 

```bash
docker run -it solana-php-sdk /bin/bash
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

    use BorshObject; //trait
    public $keyData;

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
## BORSH USAGE (PHP implementation)

To get a better understanding on the implementation and usage, please refer to the following references: 

- [PHP Borsh Test](https://github.com/Attestto-com/solana-php-sdk/blob/master/tests/Unit/BorshTest.php)
- [PHP Borsh Class](https://github.com/Attestto-com/solana-php-sdk/blob/master/src/Borsh/Borsh.php)
- [PHP Borsh Trait](https://github.com/Attestto-com/solana-php-sdk/blob/master/src/Borsh/BorshObject.php)

example usage _**(This will be improved, WIP)**_: 
```php
/**
     * deserializeDidData
     *
     * @param string $dataBase64 The base64 encoded data of the DID data account
     * @return DidData The deserialized DID data object
     * @example DidSolProgram::deserializeDidData('TVjvjfsd7fMA/gAAAA...');
     */
    static function deserializeDidData($dataBase64)
    {

        $base64String = base64_decode($dataBase64);
        $uint8Array = array_values(unpack('C*', $base64String));
        $didData = DidData::fromBuffer($uint8Array); // See above code block

        $keyData = $didData->keyData;

        $binaryString = pack('C*', ...$keyData);

        $b58 = new Base58();
        $base58String = $b58->encode($binaryString);
        $didData->keyData = $base58String;
        return $didData;
    }
```

## Notes:

- Most of the Magic is done in the [BorshDesealizable.php](https://github.com/Attestto-com/solana-php-sdk/blob/master/src/Borsh/BorshDeserializable.php) Trait. 
- This project is in alpha, the code to generate instructions is still being worked on `$instruction = SystemProgram::abc()`
- This project is maintained by a single dev, so any feedback, ideas, comments are appreciated. 

## Roadmap (WIP)

1. Borsh serialize and deserialize. [Done](https://github.com/Attestto-com/solana-php-sdk/tree/master/src/Borsh) - [Test(s)](https://github.com/Attestto-com/solana-php-sdk/blob/master/tests/Unit/BorshTest.php) - [Coverage](https://app.codecov.io/github/Attestto-com/solana-php-sdk/tree/master/src%2FBorsh)
2. Improved documentation. [WIP](#) - This document + [Documentation Index](https://github.com/Attestto-com/solana-php-sdk/tree/master/docs) (https://github.com/Attestto-com/solana-php-sdk/tree/master/docs)
3. Build out more of the Connection, Message, SystemProgram, TokenProgram, MetaplexProgram classes. [WIP](https://github.com/Attestto-com/solana-php-sdk/tree/master/src) - [Tests](https://github.com/Attestto-com/solana-php-sdk/tree/master/tests/Unit) - [Coverage](https://app.codecov.io/github/Attestto-com/solana-php-sdk/tree/master/src)
   4. [ ] Connection::class
      5. [x] getLatestBlokchash::class [Source] - [Test] - [Coverage]
      6. [ ] getMinimumBalanceForRentExemption()
      7. [ ] getTokenAccountBalance()
   6. [ ] TransactionMessage::class 
      7. [ ] compileToV0Message()
   8. [ ] VersionedTransaction::class
   9. [ ] SPL-TOKEN Program
      10. [ ] getOrCreateAssociatedTokenAccount()
      11. [ ] getAssociatedTokenAddressSync()
      12. [ ] createAssociatedTokenAccountInstruction()
      11. [ ] createSyncNativeInstruction() - [Test][Coverage]
      
4. Improve abstractions around working with binary data. [Done?](https://github.com/Attestto-com/solana-php-sdk/tree/master/src/Borsh) - [Test(s)](https://github.com/Attestto-com/solana-php-sdk/blob/master/tests/Unit/BorshTest.php) - [Coverage](https://app.codecov.io/github/Attestto-com/solana-php-sdk/tree/master/src%2FBorsh)
5. Optimizations:
   1. Leverage PHP more.
   2. Better cache `$recentBlockhash` when sending transactions. 
6. Suggestions? Open an [Issue](https://github.com/Attestto-com/solana-php-sdk/issues) or [Pull Request](https://github.com/Attestto-com/solana-php-sdk/pulls) :D

## Testing & Code Coverage

WIP -- Working on coverage and deprecations. See [Coverage Report](https://app.codecov.io/github/Attestto-com/solana-php-sdk).

- Configuration [phpunit.xml](https://github.com/Attestto-com/solana-php-sdk/blob/master/phpUnit.xml)
- composer.json
```json
   "scripts": {
        "test": "vendor/bin/phpunit tests --coverage-clover=coverage.xml --coverage-filter src/",
        "format": "vendor/bin/php-cs-fixer fix --allow-risk=yes"
    },
```
[![GitHub Tests Action Status](https://github.com/Attestto-com/solana-php-sdk/actions/workflows/run-tests.yml/badge.svg?branch=master)](https://github.com/Attestto-com/solana-php-sdk/actions/workflows/run-tests.yml)
[![Coverage (CodeCov)](https://codecov.io/github/Attestto-com/solana-php-sdk/graph/badge.svg?token=M12LECZ9QE)](https://codecov.io/github/Attestto-com/solana-php-sdk)


```bash
composer test
```
OR

```bash
/verdor/bin/phpunit tests [options]
```

## Contributing - Yes Please! :-P

- Please see [CONTRIBUTING](CONTRIBUTING.md) for details.
- I will change my profile pic once we get a 2nd mantainer onboard :-)


## Security

If you discover any security related issues, please email the maintainers (see composer.json) instead of using the issue tracker.

## Credits

- [Matt Stauffer](https://github.com/mattstauffer) (Original creator)
- [Zach Vander Velden](https://github.com/exzachlyvv) (Metadata wizard)
- [Neverything](https://github.com/verze-app/solana-php-sdk/graphs/contributors) (Previous Maintainer)
- [All Contributors](../../contributors)
  
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
