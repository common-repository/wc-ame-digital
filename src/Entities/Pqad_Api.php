<?php
/**
 * Api.
 *
 * @package PQAD
 */

declare(strict_types=1);

namespace PQAD\Entities;

/**
 * Api class.
 */
class Pqad_Api
{

	const CALLBACK_URL = 'wc-ame-digital/callback';
	const REST_ROUTE   = 'wc-ame-digital/v1';
	const CLIENT_ID    = '55865e7f-f8a1-3605-aaea-dfc9cbfc998f'; //APIKI
	const HOMOLOG_URL  = 'http://api-amedigital.sensedia.com/hml/transacoes/v1';
	const PROD_URL     = 'http://api-amedigital.sensedia.com/transacoes/v1';
	const API_GUIDE    = 'https://portal-amedigital.sensedia.com/api-portal/pt-br/content/api-guide';
	const SUPPORT_URL  = 'https://apiki.com/parceiros/ame/';

	public static function callback_url() {
		return get_site_url() . '/wc-api/wc-ame-digital/callback';
	}

	public static function environment_url() {
		$environment_url = self::PROD_URL;
		$allowed_hosts   = [ 'amedigital.loc', 'amedigital.apikihomolog.com' ];

		if ( in_array( $_SERVER['HTTP_HOST'], $allowed_hosts ) ) {
			$environment_url = self::HOMOLOG_URL;
		}

		return $environment_url;
	}

	public static function client_id() {
		$client_id     = self::CLIENT_ID;
		$allowed_hosts = [ 'amedigital.loc', 'amedigital.apikihomolog.com' ];

		if ( in_array( $_SERVER['HTTP_HOST'], $allowed_hosts ) ) {
			$client_id = 'b5d1603a-5084-3f60-a975-d7783a3de970';
		}

		return $client_id;
	}

	public static function access_token( $access_token ) {
		$allowed_hosts = [ 'amedigital.loc', 'amedigital.apikihomolog.com' ];

		if ( in_array( $_SERVER['HTTP_HOST'], $allowed_hosts ) ) {
			$access_token = '9d1a607b-1233-3a53-ae08-58ffeb0ab7da';
		}

		return $access_token;
	}

	public static function translation_api_order_status( $status ) {
		switch ( $status ) {
			case $status === 'ERROR':
				return 'error';
				break;
			case $status === 'CREATED':
				return 'created';
				break;
			case $status === 'HOLD':
				return 'hold';
				break;
			case $status === 'DENIED':
				return 'denied';
				break;
			case $status === 'AUTHORIZED':
				return 'authorized';
				break;
			case $status === 'CANCELED':
				return 'canceled';
				break;
			case $status === 'REFUNDED':
				return 'refunded';
				break;
			case $status === 'SUCCESS':
				return 'success';
				break;
		}
	}

	public static function translation_api_order_type( $type ) {
		switch ( $type ) {
			case $type === 'AUTHORIZATION':
				return 'authorization';
				break;
			case $type === 'REFUND':
				return 'refund';
				break;
			case $type === 'HOLD':
				return 'hold';
				break;
			case $type === 'DENIED':
				return 'denied';
				break;
			case $type === 'AUTHORIZED':
				return 'authorized';
				break;
			case $type === 'CANCELED':
				return 'canceled';
				break;
			case $type === 'REFUNDED':
				return 'refunded';
				break;
			case $type === 'SUCCESS':
				return 'success';
				break;
		}
	}
}
