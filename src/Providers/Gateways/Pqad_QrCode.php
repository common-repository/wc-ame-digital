<?php declare(strict_types = 1);

namespace PQAD\Providers\Gateways;

use WPSteak\Providers\AbstractHookProvider;

class Pqad_QrCode extends AbstractHookProvider
{

	public function register_hooks() {
		$this->add_filter( 'woocommerce_payment_gateways', 'add_gateway_qrcode' );
	}

	/**
	 * Create Gateway Option QR Code
	 *
	 * @since 1.0
	 * @param Array $gateways
	 * @return Array
	 */
	public function add_gateway_qrcode( $gateways ) {

        $gateways[] = 'PQAD\Services\Gateways\Pqad_QrCode';

        return $gateways;
	}
}
