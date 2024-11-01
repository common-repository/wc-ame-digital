<?php
/**
 * QrCode
 *
 * @package PQAD
 */

declare(strict_types=1);

namespace PQAD\Repositories;

use PQAD\Entities\Pqad_Api as Api_Entity;
use PQAD\Entities\Pqad_Logs as Logs_Entity;

/**
 * QrCode class.
 */
class Pqad_QrCode
{

	public static function get_options() {
		$options = get_option( 'woocommerce_ame-digital_settings' );
		$logs    = '';

		if ( $options ) {
			$logs = $options['enabled_logs'];
		}

		return $logs;
	}
    /**
     * Register header route
     *
     * @since  1.0.0
     * @access public
     */
    public static function get_header() {
		$options      = get_option( 'woocommerce_ame-digital_settings' );
		$access_token = $options['access_token'];

        if ( empty( $access_token ) ) {
            return Logs_Entity::capture_order_error( 'AME DIGITAL CAPTURE ERROR', 'Access Token vazio.' );
        }

		return [
			'content-type' => 'application/json',
            'client_id'    => Api_Entity::client_id(),
			'access_token' => Api_Entity::access_token( $access_token )
		];
	}

	public static function get_remote_capture( $id ) {
		$environment = Api_Entity::environment_url();
		$url         = $environment . '/pagamentos';

		$body = [
			'idPagamento' => $id
		];

		$args = [
			'headers' => self::get_header(),
			'body'    => wp_json_encode( $body ),
			'timeout' => 200,
		];

		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			Logs_Entity::capture_order_error( 'AME DIGITAL CAPTURE ERROR', $response );
		}

        return $response;
    }

    public static function get_remote_cancel( $id ) {
		$environment = Api_Entity::environment_url();
		$url         = $environment . '/pagamentos/'.$id;

		$args = [
			'headers' => self::get_header(),
			'method'  => 'DELETE',
			'timeout' => 200,
		];

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			Logs_Entity::cancel_order_error( 'AME DIGITAL CANCEL ERROR', $response );
		}

        return $response;
    }

    public static function get_remote_refund( $id, $amount, $refund_id ) {
		$environment = Api_Entity::environment_url();
		$url         = $environment . '/pagamentos/'.$id;
		$logs        = self::get_options();

		$body = [
			'amount'   => (int)$amount,
			'refundId' => (string)$refund_id
		];

		$args = [
			'headers' => self::get_header(),
			'method'  => 'PUT',
			'body'    => wp_json_encode( $body ),
			'timeout' => 200,
		];

		$response = wp_remote_request( $url, $args );

		if ( $logs === 'yes' ) {
			Logs_Entity::refund_order( 'AME DIGITAL REFUND RESPONSE', $response );
		}

		if ( is_wp_error( $response ) ) {
			Logs_Entity::refund_order_error( 'AME DIGITAL REFUND ERROR', $response );
		}

    	return $response;
  	}

	public static function get_remote_delete( $id ) {
		$environment = Api_Entity::environment_url();
		$url         = $environment . '/ordens/'.$id;

		$args = [
			'headers' => self::get_header(),
			'method'  => 'DELETE',
			'timeout' => 200,
		];

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			Logs_Entity::delete_order_error( 'AME DIGITAL DELETE ERROR', $response );
		}

        return $response;
    }

}
