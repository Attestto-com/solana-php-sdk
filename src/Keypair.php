<?php

namespace Attestto\SolanaPhpSdk;

use SodiumException;
use Attestto\SolanaPhpSdk\Util\Buffer;
use Attestto\SolanaPhpSdk\Util\HasPublicKey;
use Attestto\SolanaPhpSdk\Util\HasSecretKey;

/**
 * An account keypair used for signing transactions.
 *  @property PublicKey $publicKey The public key for this keypair
 *  @property PublicKey $secretKey The raw secret key for this keypair
 *   
 */
class Keypair implements HasPublicKey, HasSecretKey
{
    public PublicKey $publicKey;
    public Buffer $secretKey;


    public function __construct($publicKey = null, $secretKey = null)
    {
        if ($publicKey == null && $secretKey == null) {
            $keypair = sodium_crypto_sign_keypair();

            $publicKey = sodium_crypto_sign_publickey($keypair);
            $secretKey = sodium_crypto_sign_secretkey($keypair);
        }

        // $this->publicKey = Buffer::from($publicKey);
        // $this->secretKey = Buffer::from($secretKey);
        $this->publicKey = new PublicKey($publicKey);
        $this->secretKey = new Buffer($secretKey);
    }

    /**
     * @return Keypair
     * @throws SodiumException
     */
    public static function generate(): Keypair
    {
        $keypair = sodium_crypto_sign_keypair();

        return static::from($keypair);
    }

    /**
     * @param string $keypair
     * @return Keypair
     * @throws SodiumException
     */
    public static function from(string $keypair): Keypair
    {
        return new static(
            sodium_crypto_sign_publickey($keypair),
            sodium_crypto_sign_secretkey($keypair)
        );
    }

    /**
     * Create a keypair from a raw secret key byte array.
     *
     * This method should only be used to recreate a keypair from a previously
     * generated secret key. Generating keypairs from a random seed should be done
     * with the {@link Keypair.fromSeed} method.
     *
     * @param $secretKey
     * @return Keypair
     */
    static public function fromSecretKey($secretKey, $skipValidation = null): Keypair
    {
        $secretKey = Buffer::from($secretKey)->toString();

        $publicKey = sodium_crypto_sign_publickey_from_secretkey($secretKey);

        return new static(
            $publicKey,
            $secretKey
        );
    }

    /**
     * Generate a keypair from a 32 byte seed.
     *
     * @param string|array $seed
     * @return Keypair
     * @throws SodiumException
     */
    static public function fromSeed($seed): Keypair
    {
        $seed = Buffer::from($seed)->toString();

        $keypair = sodium_crypto_sign_seed_keypair($seed);

        return static::from($keypair);
    }

    /**
     * The public key for this keypair
     *
     * @return PublicKey
     */
    public function getPublicKey(): PublicKey
    {
        return $this->publicKey;
    }

    /**
     * The raw secret key for this keypair
     *
     * @return Buffer
     */
    public function getSecretKey(): Buffer
    {
        return Buffer::from($this->secretKey);
    }
}
