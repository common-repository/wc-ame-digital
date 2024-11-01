<?php declare(strict_types = 1);

namespace PQAD\Providers\Orders;

use PQAD\Entities\Pqad_Settings as Settings_Entity;
use PQAD\Entities\Pqad_Api as Api_Entity;
use PQAD\Entities\Pqad_Logs as Log_Entity;
use PQAD\Repositories\Pqad_Orders as Orders_Repo;
use PQAD\Repositories\Pqad_QrCode as QrCode_Repo;
use WPSteak\Providers\AbstractHookProvider;

class Pqad_QrCode extends AbstractHookProvider
{

	public function __construct() {
		$this->options = get_option( 'woocommerce_ame-digital_settings' );
	}

    public function register_hooks() {
		$this->add_action( 'rest_api_init', 'register_callback_route' );
		$this->add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_order_fields' );
		$this->add_action( 'woocommerce_order_refunded', 'pqad_woocommerce_order_refunded', 10, 2 );
		//$this->add_action( 'woocommerce_order_status_cancelled', 'pqad_woocommerce_cancelled_order', 10 );
		$this->add_action( 'woocommerce_order_status_cancelled', 'pqad_woocommerce_delete_order', 10 );
		$this->add_filter( 'cron_schedules', 'pqad_cron_schedules' );
		$this->add_action( 'admin_init', 'pqad_register_schedules' );
		$this->add_action( 'pqad_ame_delete_qrcode', 'pqad_cron_delete_qrcode' );
	}

    /**
     * Register callback route endpoint
     *
     * @since  1.0.0
     * @access public
     */
    public function register_callback_route() {
		register_rest_route(
			Api_Entity::REST_ROUTE,
			'/callback',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'get_transaction_data' ],
				'permission_callback' => '__return_true'
			]
		);
	}

	/**
	 * Rest response.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_transaction_data( $request ) {
		$data = json_decode( $request->get_body() );

		if ( $this->options ) {
			$logs = $this->options['enabled_logs'];
		}

        if ( empty( $data ) ) {
			Log_Entity::order_callback_error( 'AME DIGITAL CALLBACK DATA', $data );
            return;
        }

        $orders       = Orders_Repo::get_all_orders_by_id( $data->attributes->orderId );
        $order_status = Api_Entity::translation_api_order_status( $data->status );

        foreach ( $orders as $order ) {
            $order_id = $order;
		}

		if ( $logs === 'yes' ) {
			Log_Entity::order_callback( 'AME DIGITAL ORDER ID', $order_id );
			Log_Entity::order_callback( 'AME DIGITAL CALLBACK RESPONSE', $data );
		}

        if ( $order_status === 'authorized' ) {
			$this->capture_order_api( $data->id, $order_id );
			update_post_meta( $order_id, '_wad_api_order_status', $order_status );
			update_post_meta( $order_id, '_wad_captured_order_uuid', $data->id );
			update_post_meta( $order_id, '_wad_captured_order_nsu', $data->nsu );
        }
	}

	 /**
     * Register Capture Order route
     *
     * @since  1.0.0
     * @access public
     */
    public function capture_order_api( $id, $order_id ) {

		$response   = QrCode_Repo::get_remote_capture( $id );
        $code       = wp_remote_retrieve_response_code( $response );
		$body       = wp_remote_retrieve_body( $response );
		$response   = json_decode( $body );
		$get_status = $this->options['order_status'];
		$woo_status = 'completed';

		if ( $get_status === 'pqad_processing' ) {
			$woo_status = 'processing';
		}

		if ( $this->options ) {
			$logs = $this->options['enabled_logs'];
		}

        if ( in_array( $code, [200, 201] ) ) {
			$order_status = Api_Entity::translation_api_order_status( $response->status );

			if ( $order_status === 'success' ) {
				update_post_meta( $order_id, '_wad_api_order_status', $order_status );
				update_post_meta( $order_id, '_wad_captured_order_uuid', $id );

				$wc_order = wc_get_order( $order_id );

				$wc_order->update_status( $woo_status );
				$wc_order->add_order_note(  __( 'Ame: Pagamento autorizado.', 'wc-ame-digital' ) );
			}

			if ( $response->status === 'canceled' ) {
				update_post_meta( $order_id, '_wad_api_order_status', $order_status );

				$wc_order = wc_get_order( $order_id );

				$wc_order->update_status( 'cancelled' );
				$wc_order->add_order_note(  __( 'Ame: Pagamento cancelado.', 'wc-ame-digital' ) );
			}

			if ( $logs === 'yes' ) {
				Log_Entity::capture_order( 'AME CAPTURED ORDER ID', $order_id );
				Log_Entity::capture_order( 'AME CAPTURED CALLBACK ORDER', $response );
			}
		}
	}

	/**
     * WooCommerce Cancel Order Manual
     *
     * @since  1.0.0
     * @access public
     */
	public function pqad_woocommerce_cancelled_order( $order_id ) {
		$order_status = get_post_meta( $order_id, '_wad_api_order_status', true );
		$uuid         = get_post_meta( $order_id, '_wad_captured_order_uuid', true );

		if ( $order_status === 'success' ) {
			return;
		}

		$this->cancel_order_api( $uuid, $order_id );
	}

    /**
     * Register Cancel Order route
     *
     * @since  1.0.0
     * @access public
     */
    public function cancel_order_api( $id, $order_id ) {

        $response = QrCode_Repo::get_remote_cancel( $id );
        $code     = wp_remote_retrieve_response_code( $response );
		$body     = wp_remote_retrieve_body( $response );
		$response = json_decode( $body );

		if ( $this->options ) {
			$logs = $this->options['enabled_logs'];
		}

        if ( in_array( $code, [200, 201] ) ) {
            update_post_meta( $order_id, '_wad_api_order_status', 'canceled' );

            $order = wc_get_order( $order_id );

            $order->update_status( 'cancelled' );
			$order->add_order_note(  __( 'Ame: Pagamento cancelado.', 'wc-ame-digital' ) );

			if ( $logs === 'yes' ) {
				Log_Entity::cancel_order( 'AME DIGITAL CANCELED ORDER ID', $order_id );
				Log_Entity::cancel_order( 'AME DIGITAL CANCELED RESPONSE', $response );
			}
		}

		if ( in_array( !$code, [200, 201] ) ) {
			$order->add_order_note(  __( 'Ame: Pagamento não cancelado.', 'wc-ame-digital' ) );
		}
	}

	/**
     * WooCommerce Refund Order Manual
     *
     * @since  1.0.0
     * @access public
     */
	public function pqad_woocommerce_order_refunded( $order_id, $refund_id ) {
		$order         = wc_get_order( $order_id );
		$uuid          = get_post_meta( $order_id, '_wad_captured_order_uuid', true );
		$get_refund    = Settings_Entity::post( 'refund_amount', FILTER_SANITIZE_STRING );
		$refund_format = wc_format_decimal( $get_refund, 2 );
		$refund_remain = $order->get_remaining_refund_amount();
		$replace       = str_replace( '.', '', $refund_format );
		$refund_value  = str_pad( $replace, 4, '0', STR_PAD_LEFT );

		if ( $refund_format === '0.00' ) {
			return $order->add_order_note(  __( 'Ame: Pagamento não reembolsado.', 'wc-ame-digital' ) );
		}

		if ( $refund_format === '0.00' && $refund_remain === '0.00' ) {
			return $order->add_order_note(  __( 'Ame: Pagamento totalmente reembolsado.', 'wc-ame-digital' ) );
		}

		$this->refund_order_api( $uuid, $order_id, $refund_value, $refund_id, $refund_format );
	}

	/**
	 * Register Refund Order route
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function refund_order_api( $id, $order_id, $refund_value, $refund_id, $price ) {

		$response = QrCode_Repo::get_remote_refund( $id, $refund_value, $refund_id );
		$code     = wp_remote_retrieve_response_code( $response );
		$body     = wp_remote_retrieve_body( $response );
		$response = json_decode( $body );

		if ( $this->options ) {
			$logs = $this->options['enabled_logs'];
		}

		if ( in_array( $code, [200, 201] ) ) {
			update_post_meta( $order_id, '_wad_api_order_status', 'refunded' );

			$order = wc_get_order( $order_id );

			$order->add_order_note(  __( 'Ame: Pagamento reembolsado. R$'.$price, 'wc-ame-digital' ) );

			if ( $logs === 'yes' ) {
				Log_Entity::refund_order( 'AME DIGITAL REFUNDED ORDER ID', $order_id );
				Log_Entity::refund_order( 'AME DIGITAL REFUNDED RESPONSE', $response );
			}
		}

		if ( in_array( !$code, [200, 201] ) ) {
			$order->add_order_note(  __( 'Ame: Pagamento não reembolsado.', 'wc-ame-digital' ) );
		}
	}

	/**
     * WooCommerce Delete Order Manual
     *
     * @since  1.2.1
     * @access public
     */
	public function pqad_woocommerce_delete_order( $order_id ) {
		$order_status = get_post_meta( $order_id, '_wad_api_order_status', true );
		$uuid         = get_post_meta( $order_id, '_wad_captured_order_uuid', true );

		if ( $order_status === 'success' ) {
			return;
		}

		$this->delete_order_api( $uuid, $order_id );
	}

	/**
	 * Register Delete Order route
	 *
	 * @since  1.2.1
	 * @access public
	 */
    public function delete_order_api( $id, $order_id ) {

        $response = QrCode_Repo::get_remote_delete( $id );
        $code     = wp_remote_retrieve_response_code( $response );
		$body     = wp_remote_retrieve_body( $response );
		$response = json_decode( $body );

		if ( $this->options ) {
			$logs = $this->options['enabled_logs'];
		}

        if ( in_array( $code, [200, 201] ) ) {
            update_post_meta( $order_id, '_wad_api_order_status', 'canceled' );

            $order = wc_get_order( $order_id );

            $order->update_status( 'cancelled' );
			$order->add_order_note(  __( 'Ame: QRCode foi excluído.', 'wc-ame-digital' ) );

			if ( $logs === 'yes' ) {
				Log_Entity::delete_order( 'AME DIGITAL DELETE ORDER ID', $order_id );
				Log_Entity::delete_order( 'AME DIGITAL DELETE RESPONSE', $response );
			}
		}

		if ( in_array( !$code, [200, 201] ) ) {
			$order->add_order_note(  __( 'Ame: QRCode não foi excluído.', 'wc-ame-digital' ) );
		}
	}

	/**
     * Add Cron Schedules
     *
     * @since  1.2.1
     * @access public
     */
    public function pqad_cron_schedules() {
		$interval = isset( $this->options['qrcode_expiry'] ) ? $this->options['qrcode_expiry'] : 1;
		$days     = 'dias';

		if ( $interval === 1 ) {
			$days = 'dia';
		}

		$display = sprintf( '%s %s %s',
			esc_html__( 'Exclusão a cada ', 'wc-ame-digital' ),
			$interval,
			$days
		);

		return [
			'pqad_delete_qrcode' => [
				'interval' => $interval * 86400, //Intervalo em dias
				'display'  => $display
			]
		];
	}

	/**
     * Register Cron Schedules
     *
     * @since  1.2.1
     * @access public
     */
	public function pqad_register_schedules() {
		if ( ! wp_next_scheduled( 'pqad_ame_delete_qrcode' ) ) {
			wp_schedule_event( time(), 'pqad_delete_qrcode', 'pqad_ame_delete_qrcode' );
		}
	}

	/**
     * Cron Delete QRCode
     *
     * @since  1.2.1
     * @access public
     */
	public function pqad_cron_delete_qrcode() {
		$orders_ids = Orders_Repo::get_all_id_orders();

		foreach ( $orders_ids as $order_id ) {
			$order_status = get_post_meta( $order_id, '_wad_api_order_status', true );
			$uuid         = get_post_meta( $order_id, '_wad_captured_order_uuid', true );

			if ( $order_status === 'success' ) {
				return;
			}

			$this->delete_order_api( $uuid, $order_id );
		}
	}

	public function display_order_fields() {

		$order_id = get_the_id();
		$ame_id   = get_post_meta( $order_id, '_wad_api_order_id', true );
		$ame_nsu  = get_post_meta( $order_id, '_wad_captured_order_nsu', true );

		include $this->plugin->get_path( 'views/orders/qrcode.php' );
	}
}
