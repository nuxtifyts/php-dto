<?php

namespace Nuxtifyts\PhpDto\DataCiphers;

use Nuxtifyts\PhpDto\Exceptions\DataCipherException;

class DefaultDataCipher implements DataCipher
{
    protected const string CIPHER_ALGO = 'aes-256-ctr';

    /**
     * @throws DataCipherException
     */
    public static function cipher(
        mixed $data,
        string $secret,
        bool $encode = false
    ): string {
        $data = self::stringify($data);
        $nonceSize = openssl_cipher_iv_length(self::CIPHER_ALGO);

        if ($nonceSize === false) {
            throw DataCipherException::failedToGetNonceSize();
        }

        $nonce = openssl_random_pseudo_bytes($nonceSize);

        $cipherText = openssl_encrypt(
            data: $data,
            cipher_algo: self::CIPHER_ALGO,
            passphrase: $secret,
            options: OPENSSL_RAW_DATA,
            iv: $nonce,
        );

        if ($cipherText === false) {
            throw DataCipherException::failedToCipherData();
        }

        return $encode
            ? base64_encode($nonce.$cipherText)
            : $nonce.$cipherText;
    }

    /**
     * @throws DataCipherException
     */
    public static function decipher(
        string $data,
        string $secret,
        bool $decode = false
    ): mixed {
        $data = $decode ? base64_decode($data) : $data;

        $nonceSize = openssl_cipher_iv_length(self::CIPHER_ALGO);

        if ($nonceSize === false) {
            throw DataCipherException::failedToGetNonceSize();
        }

        $nonce = substr($data, 0, $nonceSize);
        $cipheredData = substr($data, $nonceSize);

        if (strlen($nonce) < $nonceSize) {
            throw DataCipherException::invalidNonceSize();
        }

        $decipheredData = openssl_decrypt(
            data: $cipheredData,
            cipher_algo: self::CIPHER_ALGO,
            passphrase: $secret,
            options: OPENSSL_RAW_DATA,
            iv: $nonce,
        );

        if (!$decipheredData) {
            throw DataCipherException::failedToDecipherData();
        }

        return json_validate($decipheredData)
            ? json_decode($decipheredData, true)
            : $decipheredData;
    }

    /**
     * @throws DataCipherException
     */
    private static function stringify(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        $jsonEncodedValue = json_encode($value);

        if ($jsonEncodedValue === false) {
            throw DataCipherException::failedToStringifyValue();
        }

        return $jsonEncodedValue;
    }
}
