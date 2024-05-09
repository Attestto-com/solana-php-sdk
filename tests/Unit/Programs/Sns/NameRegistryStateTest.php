<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs\SNS;


use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Programs\SNS\State\NameRegistryStateAccount;
use Attestto\SolanaPhpSdk\Programs\SNS\Utils;
use Attestto\SolanaPhpSdk\Programs\SnsProgram;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\Util\Buffer;

class NameRegistryStateTest extends TestCase
{

    private array $items = [
        [
            'domain' => 'bonfida',
            'address' => 'Crf8hzfthWGbGbLTVCiqRqV5MVnbpHB1L9KQMd6gsinb',
        ],
        [
            'domain' => 'bonfida.sol',
            'address' => 'Crf8hzfthWGbGbLTVCiqRqV5MVnbpHB1L9KQMd6gsinb',
        ],
        [
            'domain' => 'dex.bonfida',
            'address' => 'HoFfFXqFHAC8RP3duuQNzag1ieUwJRBv1HtRNiWFq4Qu',
        ],
        [
            'domain' => 'dex.bonfida.sol',
            'address' => 'HoFfFXqFHAC8RP3duuQNzag1ieUwJRBv1HtRNiWFq4Qu',
        ],
    ];

    public function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function test_deserialize(){
        $accountData = 'PVPCSzg2DtOBOiPfst/YIKtYIct5KaONLqqyUug4JZXybLcicCAgnC2mdJSPjzwzDuT5o4Yla9FPN6bgxWdUKwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAY2x1ZTIuc29sAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA==';
        $dataBuffer = Buffer::from(base64_decode($accountData));
       // $accountData = $dataBuffer->slice(96);
        $nameRegistryState = NameRegistryStateAccount::deserialize($dataBuffer->toArray());
        $this->assertEquals('58PwtjSDuFHuUkYjH9BYnnQKHfwo9reZhC2zMJv9JPkx', $nameRegistryState->parentName->toBase58());
        $this->assertEquals('HKKp49qGWXd639QsuH7JiLijfVW5UtCVY4s1n2HANwEA', $nameRegistryState->owner->toBase58());
        $this->assertEquals('11111111111111111111111111111111', $nameRegistryState->class->toBase58());
    }

    #[Test]
    public function test_retrieve(){

        $rpcClient = new SolanaRpcClient('https://api.mainnet-beta.solana.com');
        $connection = new Connection($rpcClient);
        $nameRegistryState = NameRegistryStateAccount::retrieve($connection, 'Crf8hzfthWGbGbLTVCiqRqV5MVnbpHB1L9KQMd6gsinb');
        $this->assertEquals('HKKp49qGWXd639QsuH7JiLijfVW5UtCVY4s1n2HANwEA', $nameRegistryState['registry']->owner->toBase58());
        $this->assertEquals('58PwtjSDuFHuUkYjH9BYnnQKHfwo9reZhC2zMJv9JPkx', $nameRegistryState['registry']->parentName->toBase58());
        $this->assertEquals('11111111111111111111111111111111', $nameRegistryState['registry']->class->toBase58());
    }





}
