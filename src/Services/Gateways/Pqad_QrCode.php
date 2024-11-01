<?php
/**
 * Gateway QR Code
 *
 * @package PQAD
 */

declare(strict_types=1);

namespace PQAD\Services\Gateways;

use WC_Payment_Gateway;

use PQAD\Entities\Pqad_Api as Api_Entity;
use PQAD\Entities\Pqad_Settings as Settings_Entity;
use PQAD\Entities\Pqad_Logs as Logs_Entity;

class Pqad_QrCode extends WC_Payment_Gateway
{

    public function __construct() {
        $this->id                 = 'ame-digital';
        $this->icon               = Settings_Entity::plugins_url( '/resources/images/ame-logo.png' );
        $this->has_fields         = true;
        $this->method_title       = __( 'Ame', 'wc-ame-digital' );
        $this->method_description = __( 'Método de Pagamento Ame Digital', 'wc-ame-digital' );
		$this->supports           = [ 'products' ];
		$this->client_id          = Api_Entity::CLIENT_ID;
		$this->access_token       = $this->get_option( 'access_token' );

        $this->init_form_fields();
        $this->init_settings();

        $this->title          = $this->get_option( 'title' );
        $this->description    = $this->get_option( 'description' );
		$this->enabled        = $this->get_option( 'enabled' );
        $this->checkout_title = $this->get_option( 'checkout_title' );
		$this->enabled_logs   = $this->get_option( 'enabled_logs' );
		$this->logs           = 'yes' === $this->get_option( 'enabled_logs' );
		$this->woo_logs       = admin_url( 'page=wc-status&tab=logs' );

        if ( is_admin() ) {
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
        }

        //add_action( 'woocommerce_api_'. Api::CALLBACK_URL, array( $this, 'payment_callback' ) );
	}
   /**
     * Plugin options, we deal with it in Step 3 too
     */
    public function init_form_fields() {

        $this->form_fields = [
            'enabled' => [
                'title'       =>  __( 'Habilitar', 'wc-ame-digital' ),
                'label'       => __( 'Habilitar pagamento', 'wc-ame-digital' ),
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no'
			],
			'access_token' => [
                'title'       => __( 'Access Token', 'wc-ame-digital' ),
                'type'        => 'text',
                'description' => __( 'Entre em contato com a AME para conseguir seu access token.', 'wc-ame-digital' ),
                'desc_tip'    => false,
			],
            'title' => [
                'title'       => __( 'Título', 'wc-ame-digital' ),
                'type'        => 'text',
                'description' => __( 'Altera o título que o usuário visualiza na página de checkout.', 'wc-ame-digital' ),
                'default'     => __( 'Ame Digital', 'wc-ame-digital' ),
                'desc_tip'    => true,
			],
            'description' => [
                'title'       => __( 'Descrição', 'wc-ame-digital' ),
				'type'        => 'textarea',
				'css'         => 'width: 400px;',
                'description' => __( 'Altera a descrição que o usuário visualiza na página de checkout.', 'wc-ame-digital' ),
                'default'     => __( 'Pague com seu celular através do nosso método de pagamento Ame.', 'wc-ame-digital' ),
			],
            'checkout_title' => [
                'title'       => __( 'Título do Pagamento', 'wc-ame-digital' ),
                'type'        => 'text',
                'description' => __( 'Alter o título que o usuário visualiza durante o pagamento do pedido.', 'wc-ame-digital' ),
                'default'     => __( 'Pagar com Ame', 'wc-ame-digital' ),
			],
			'qrcode_expiry' => [
                'title'       => __( 'Validade do QRCode', 'wc-ame-digital' ),
				'type'        => 'text',
                'description' => __( 'Quantidade de dias para expiração do QRCode após impressão. OBS: Esse campo vazio, ficará com 2 dias de expiração.', 'wc-ame-digital' ),
                'default'     => 1,
			],
			'order_status' => [
                'title'       => __( 'Status Pedido', 'wc-ame-digital' ),
				'type'        => 'select',
				'options'     => [
					'pqad_processing' => 'Processando',
					'pqad_completed'  => 'Concluído'
				],
                'description' => __( 'Altera o pedido no woocommerce quando está pago na AME.', 'wc-ame-digital' ),
                'default'     => 'pqad_processing',
			],
            'enabled_logs' => [
                'title'       =>  __( 'Logs', 'wc-ame-digital' ),
                'label'       => __( 'Habilita os logs. Para visualizar: WooCommerce > Status > Logs', 'wc-ame-digital' ),
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no'
			],
		];
	}

    public function payment_fields() {

        if ( $this->description ) {
            echo wpautop( wp_kses_post( $this->description ) );
        }

		printf('<fieldset id="wc-%s-form"
		class="wc-woo-ame-digital wc-payment-form"
		style="background:transparent;">
		<div class="clear"></div></fieldset>', esc_attr( $this->id ) );

    }

    public function get_header()
	{
		return [
            'content-type' => 'application/json',
            'client_id'    => Api_Entity::client_id(),
			'access_token' => Api_Entity::access_token( $this->access_token )
		];
	}

    public function process_payment( $order_id ) {

        $order          = wc_get_order( $order_id );
        $items          = $order->get_items();
        $total_order    = str_replace( '.', '', $order->get_total() );
        $total          = str_pad( $total_order, 4, '0', STR_PAD_LEFT );
		$args           = [];
		$order_url      = Api_Entity::environment_url();
		$shipping       = $this->pqad_get_shipping_address( $order_id );
		$billing        = $this->pqad_get_billing_address( $order_id );
		$shipping_total = $order->get_shipping_total();
		$replace        = str_replace( '.', '', $shipping_total );
		$shipping_value = str_pad( $replace, 4, '0', STR_PAD_LEFT );
		//$cashback_total = Settings_Entity::cashback_amount( $this->cashback_amount, $order->get_total() );

		if ( empty( $shipping ) ) {
			$shipping = $billing;
		}

        foreach ( $items as $item ) {
            $name           = $item->get_name();
            $quantity       = $item->get_quantity();
            $subtotal_order = number_format( (float)$item->get_subtotal(), 2, '', '.' );
            $subtotal       = str_pad( $subtotal_order, 4, '0', STR_PAD_LEFT );

            $args[] = [
				'ean'         => null,
				'sku'         => null,
                'amount'      => (int)$subtotal,
                'quantity'    => $quantity,
                'description' => $name
			];
        }

        $body  = [
            'title'       => 'Order #' . $order_id,
            'description' => $this->title,
            'amount'      => (int)$total,
            'type'        => 'PAYMENT',
            'attributes'  => [
                //'cashbackAmountValue'           => $cashback_total, //cashback %
                'transactionChangedCallbackUrl' => rest_url( Api_Entity::REST_ROUTE.'/callback' ),
                'items'                         => $args,
				'customPayload'                 => [
					'isFrom' => 'WOOCOMMERCE'
				],
				'address' => [
					$billing,
					$shipping
				],
				'paymentOnce'     => TRUE,
				'riskHubProvider' => 'SYNC',
				'origin'          => 'ECOMMERCE',
				'version'         => Settings_Entity::WAD_LAST_VERSION
			],
		];

        $response = wp_remote_post(
			$order_url . '/ordens',
			[
                'headers' => $this->get_header(),
                'body'    => json_encode( $body ),
				'timeout' => 200,
			]
        );

		$response_body = wp_remote_retrieve_body( $response );
		$response      = json_decode( $response_body );
		$cashback_api  = 0;

		if ( $response ) {
			$cashback = isset( $response->attributes->cashbackAmountValue ) ? true : false;
			if ( $cashback ) {
				$cashback_api = $response->attributes->cashbackAmountValue;
			}
		}

        update_post_meta( $order_id, '_wad_api_order_id', $response->id );
		update_post_meta( $order_id, '_wad_api_order_status', 'hold' );
		update_post_meta( $order_id, '_wad_api_order_cashbackamount', $cashback_api );

        if (  $this->logs ) {
            Logs_Entity::send_order( 'AME DIGITAL ORDER ID', $order_id );
			Logs_Entity::send_order( 'AME DIGITAL ORDER', $body );

			Logs_Entity::order_response( 'AME DIGITAL ORDER ID', $order_id );
            Logs_Entity::order_response( 'AME DIGITAL RESPONSE', $response );
        }

		$order->update_meta_data( '_ame_digital_response', $response );

		$order->update_status( 'on-hold', __( 'Ame: Aguardando pagamento.', 'wc-ame-digital' ) );

        wc_reduce_stock_levels( $order_id );

        WC()->cart->empty_cart();

        return [
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order )
		];
	}

	public function pqad_get_shipping_address( $order_id ) {
		$order        = wc_get_order( $order_id );
		$number       = get_post_meta( $order_id, '_shipping_number', true );
		$neighborhood = get_post_meta( $order_id, '_shipping_neighborhood', true );

		$fields = [
			'postalCode'   => $order->get_shipping_postcode(),
			'street'       => $order->get_shipping_address_1(),
			'number'       => $number,
			'complement'   => $order->get_shipping_address_2(),
			'neighborhood' => $neighborhood,
			'city'         => $order->get_shipping_city(),
			'state'        => $order->get_shipping_state(),
			'country'      => $order->get_shipping_country(),
			'amountValue'  => 0,
			'type'         => 'DELIVERY'
		];

		return $fields;
	}

	public function pqad_get_billing_address( $order_id ) {
		$order        = wc_get_order( $order_id );
		$number       = get_post_meta( $order_id, '_billing_number', true );
		$neighborhood = get_post_meta( $order_id, '_billing_neighborhood', true );

		$fields = [
			'postalCode'   => $order->get_billing_postcode(),
			'street'       => $order->get_billing_address_1(),
			'number'       => $number,
			'complement'   => $order->get_billing_address_2(),
			'neighborhood' => $neighborhood,
			'city'         => $order->get_billing_city(),
			'state'        => $order->get_billing_state(),
			'country'      => $order->get_billing_country(),
			'type'         => 'BILLING'
		];

		return $fields;
	}
}
