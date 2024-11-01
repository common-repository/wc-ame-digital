<?php
/**
 * Security
 *
 * @package PQAD
 */

declare(strict_types=1);

namespace PQAD\Entities;

class Pqad_Security
{

    /* =================================================
    * ENCRYPTION-DECRYPTION
    * =================================================
    * ENCRYPTION: encrypt($data);
    * DECRYPTION: decrypt($data);
    */
    private static $encrypt_method = 'AES-256-CBC';
    private static $secret_iv      = 'mSIAjNYK!a9q$gWx';
    private static $secret_key     = 'RGEwUHBmaCtLa282aFhZdHZRdEFBZz09';

    public static function encrypt( $data, $is_json = true ) {
        if ( $is_json ) {
            $data = wp_json_encode( $data );
        }

        return openssl_encrypt( $data, self::$encrypt_method, self::$secret_key, 0, self::$secret_iv );
    }

    public static function decrypt( $data ) {
		return openssl_decrypt( $data, self::$encrypt_method, self::$secret_key, 0, self::$secret_iv );
	}

}
