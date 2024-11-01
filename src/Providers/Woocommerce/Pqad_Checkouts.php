<?php declare(strict_types = 1);

namespace PQAD\Providers\Woocommerce;

use PQAD\Entities\Pqad_Settings as Settings_Entity;
use WPSteak\Providers\AbstractHookProvider;
use stdClass;

class Pqad_Checkouts extends AbstractHookProvider
{

    public function __construct() {
        $this->welcome_learn_more = get_site_option( 'welcome_learn_more' );
		$this->options            = get_option( 'woocommerce_ame-digital_settings' );
	}

	public function register_hooks() {
		$this->add_action( 'woocommerce_thankyou_ame-digital', 'received_page', 10  );
        $this->add_action( 'wp_ajax_order_status', 'get_order_status' );
		$this->add_action( 'wp_ajax_nopriv_order_status', 'get_order_status' );

		if ( ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
			$this->add_filter( 'woocommerce_billing_fields', 'pqad_billing_checkout_field' );
			$this->add_filter( 'woocommerce_shipping_fields', 'pqad_shipping_checkout_field' );
			$this->add_filter( 'woocommerce_localisation_address_formats', 'pqad_address_formats' );
			$this->add_filter( 'woocommerce_formatted_address_replacements', 'pqad_address_replacements', 10, 2 );
			$this->add_filter( 'woocommerce_order_formatted_billing_address', 'pqad_order_billing_address', 10, 2 );
			$this->add_filter( 'woocommerce_order_formatted_shipping_address', 'pqad_order_shipping_address', 10, 2 );
			$this->add_filter( 'woocommerce_ajax_get_customer_details', 'pqad_ajax_get_customer_details', 10, 2 );
		}
    }

    public function received_page( $order_id ) {
		$ame_reponse = get_post_meta( $order_id, '_ame_digital_response', true );
		$logo        = $this->plugin->get_url( 'resources/images/ame-pay.png' );
		$title       = $this->options['checkout_title'];
		$order       = wc_get_order( $order_id );
		$error       = isset( $ame_reponse->error ) ? true : false;

		if ( $error ) {
			$error_message = isset( $ame_reponse->error_description ) ? $ame_reponse->error_description : '';

			include $this->plugin->get_path( 'views/checkout/order-error-received.php' );

			$order->update_status( 'cancelled', __( 'Ame: Pagamento cancelado.', 'wc-ame-digital' ) );

			return;
		}

        $qrcode_url   = isset( $ame_reponse->qrCodeLink ) ? $ame_reponse->qrCodeLink : '';
		$deep_url     = isset( $ame_reponse->deepLink ) ? $ame_reponse->deepLink : '';
        $api_status   = get_post_meta( $order_id, '_wad_api_order_status', true );
        $order_status = 'hold';
        $amount       = $order->get_total();
        $is_cashback  = '';
        $get_cashback = get_post_meta( $order_id, '_wad_api_order_cashbackamount', true );

		if ( $get_cashback > 0 ) {
			$is_cashback  = 'yes';
			$cashback     = number_format( ( $get_cashback/100 ), 2, ",","." );
		}

		if ( wp_is_mobile() ) {
			include $this->plugin->get_path( 'views/checkout/order-mobile-received.php' );
		} else {
			include $this->plugin->get_path( 'views/checkout/order-received.php' );
		}
    }

    public function get_order_status() {
        $callback         = new stdClass();
		$order_id         = Settings_Entity::get( 'order-id', FILTER_SANITIZE_NUMBER_INT );
		$order            = wc_get_order( $order_id );
		$order_status     = get_post_meta( $order_id, '_wad_api_order_status', true );
		$callback->status = false;

        if ( $order_status !== 'hold' ) {
			$callback->status = true;
        }

        echo json_encode( $callback );
        exit;
	}

	/**
	 * Checkout billing fields.
	 *
	 * @param  array $fields Default fields.
	 *
	 * @return array
	 */
	public function pqad_billing_checkout_field( $fields ) {
		$custom_fields = [];

		$custom_fields['billing_number'] = [
			'label'       => __( 'Número', 'wc-ame-digital' ),
			'placeholder' => __( 'Número', 'wc-ame-digital' ),
			'class'       => [ 'form-row-first', 'address-field' ],
			'clear'       => true,
			'required'    => true,
			'priority'    => 55,
		];

		$custom_fields['billing_neighborhood'] = [
			'label'       => __( 'Bairro', 'wc-ame-digital' ),
			'placeholder' => __( 'Bairro', 'wc-ame-digital' ),
			'class'       => [ 'form-row-last', 'address-field' ],
			'clear'       => true,
			'required'    => true,
			'priority'    => 56,
		];

		$fields = wp_parse_args( $custom_fields, $fields );

		return apply_filters( 'wc_ame_digital_billing_checkout_fields', $fields );
	}

	/**
	 * Checkout shipping fields.
	 *
	 * @param  array $fields Default fields.
	 *
	 * @return array
	 */
	public function pqad_shipping_checkout_field( $fields ) {
		$custom_fields = [];

		$custom_fields['shipping_number'] = [
			'label'       => __( 'Número', 'wc-ame-digital' ),
			'placeholder' => __( 'Número', 'wc-ame-digital' ),
			'class'       => [ 'form-row-first', 'address-field' ],
			'clear'       => true,
			'required'    => true,
			'priority'    => 55,
		];

		$custom_fields['shipping_neighborhood'] = [
			'label'       => __( 'Bairro', 'wc-ame-digital' ),
			'placeholder' => __( 'Bairro', 'wc-ame-digital' ),
			'class'       => [ 'form-row-last', 'address-field' ],
			'clear'       => true,
			'required'    => true,
			'priority'    => 56,
		];

		$fields = wp_parse_args( $custom_fields, $fields );

		return apply_filters( 'wc_ame_digital_shipping_checkout_fields', $fields );
	}

	/**
	 * Custom address formats.
	 *
	 * @param  array $formats Defaul formats.
	 *
	 * @return array New BR format.
	 */
	public function pqad_address_formats( $formats ) {
		$formats['BR'] = "{name}\n{address_1}, {number}\n{address_2}\n{neighborhood}\n{city}\n{state}\n{postcode}\n{country}";

		return $formats;
	}

	/**
	 * Custom address format.
	 *
	 * @param  array $replacements Default replacements.
	 * @param  array $args         Arguments to replace.
	 *
	 * @return array New replacements.
	 */
	public function pqad_address_replacements( $replacements, $args ) {
		$args = wp_parse_args(
			$args,
			[
				'number'       => '',
				'neighborhood' => '',
			]
		);

		$replacements['{number}']       = $args['number'];
		$replacements['{neighborhood}'] = $args['neighborhood'];

		return $replacements;
	}
	/**
	 * Custom order billing address.
	 *
	 * @param  array  $address Default address.
	 * @param  object $order   Order data.
	 *
	 * @return array New address format.
	 */
	public function pqad_order_billing_address( $address, $order ) {

		// WooCommerce 3.0 or later.
		if ( method_exists( $order, 'get_meta' ) ) {
			$address['number']       = $order->get_meta( '_billing_number' );
			$address['neighborhood'] = $order->get_meta( '_billing_neighborhood' );
		} else {
			$address['number']       = $order->billing_number;
			$address['neighborhood'] = $order->billing_neighborhood;
		}

		return $address;
	}

	/**
	 * Custom order shipping address.
	 *
	 * @param  array  $address Default address.
	 * @param  object $order   Order data.
	 *
	 * @return array New address format.
	 */
	public function pqad_order_shipping_address( $address, $order ) {
		if ( ! is_array( $address ) ) {
			return $address;
		}

		// WooCommerce 3.0 or later.
		if ( method_exists( $order, 'get_meta' ) ) {
			$address['number']       = $order->get_meta( '_shipping_number' );
			$address['neighborhood'] = $order->get_meta( '_shipping_neighborhood' );
		} else {
			$address['number']       = $order->shipping_number;
			$address['neighborhood'] = $order->shipping_neighborhood;
		}

		return $address;
	}

	public function pqad_ajax_get_customer_details( $data, $customer ) {
		$data['billing_number']        = $customer->get_meta( 'billing_number' );
		$data['billing_neighborhood']  = $customer->get_meta( 'billing_neighborhood' );
		$data['shipping_number']       = $customer->get_meta( 'shipping_number' );
		$data['shipping_neighborhood'] = $customer->get_meta( 'shipping_neighborhood' );

		return $data;
	}
}
