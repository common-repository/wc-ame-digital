<?php
/**
 * Logs.
 *
 * @package PQAD
 */

declare(strict_types=1);

namespace PQAD\Entities;

use WC_Logger;

/**
 * Logs class.
 */
class Pqad_Logs 
{

    public static function send_order( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-send-order-', "{$title} : ".print_r( $var, true ) );
	}

	public static function order_callback( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-order-callback-', "{$title} : ".print_r( $var, true ) );
	}

	public static function order_callback_error( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-order-callback-error-', "{$title} : ".print_r( $var, true ) );
	}

	public static function order_response( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-order-response-', "{$title} : ".print_r( $var, true ) );
	}

	public static function token_callback_error( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-token-error-', "{$title} : ".print_r( $var, true ) );
	}

	public static function capture_order( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-capture-order-', "{$title} : ".print_r( $var, true ) );
	}

	public static function capture_order_error( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-capture-order-error-', "{$title} : ".print_r( $var, true ) );
	}

	public static function refund_order( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-refund-order-', "{$title} : ".print_r( $var, true ) );
	}

	public static function refund_order_error( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-refund-order-error-', "{$title} : ".print_r( $var, true ) );
	}

	public static function cancel_order( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-cancel-order-', "{$title} : ".print_r( $var, true ) );
	}

	public static function cancel_order_error( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-cancel-order-error-', "{$title} : ".print_r( $var, true ) );
	}

	public static function delete_order( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-delete-order-', "{$title} : ".print_r( $var, true ) );
	}

	public static function delete_order_error( $title, $var ) {
		$log = new WC_Logger();
		$log->add('amedigital-delete-order-error-', "{$title} : ".print_r( $var, true ) );
	}
}
