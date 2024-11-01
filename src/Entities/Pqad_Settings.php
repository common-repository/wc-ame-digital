<?php
/**
 * Settings.
 *
 * @package PQAD
 */

declare(strict_types=1);

namespace PQAD\Entities;

/**
 * Settings class.
 */
class Pqad_Settings
{

    /**
	 * Ame Digital name.
	 */
	const WAD_NAME = 'ame_digital';
	/**
	 * Ame Digital path name.
	 */
	const WAD_PATH_NAME = 'wc-ame-digital';
	/**
	 * Ame Digital Version.
	 */
    const WAD_LAST_VERSION = '1.2.6';

	public static function post( $key, $sanitize ) {
		return filter_input( INPUT_POST, $key, $sanitize );
	}

	public static function get( $key, $sanitize ) {
		return filter_input( INPUT_GET, $key, $sanitize );
	}

	public static function cashback_price( $percentage, $total ) {
		if ( ! $percentage ) {
			return;
		}

		$price = ( $percentage / 100 ) * $total;

		return wc_price( $price );
	}

	public static function cashback_amount( $percentage, $total ) {
		if ( ! $percentage ) {
			return 0;
		}

		$amount      = ( $percentage / 100 ) * $total;
		$order       = number_format( (float)$amount, 2, '', '.' );
        $amount_toal = str_pad( $order, 4, '0', STR_PAD_LEFT );

		return $amount_toal;
	}

	public static function plugins_url( $path ) {
		return esc_url( plugins_url( 'wc-ame-digital' . $path ) );
	}

	public static function render_admin_message() { ?>
		<div class="updated inline woocommerce-message">
			<p><?php echo esc_html( sprintf( __( 'Se você gostou do plugin %s, deixe-nos uma avaliação com %s no WordPress.org. Desde já nosso obrigado!', 'wc-ame-digital' ), __( 'Pagamento QRCode Ame Digital', 'wc-ame-digital' ), '&#9733;&#9733;&#9733;&#9733;&#9733;' ) ); ?></p>
			<p>
				<a href="https://apiki.com/parceiros/ame/" target="_blank" class="button button-primary">
					<?php esc_html_e( 'Suporte Apiki', 'wc-ame-digital' ); ?>
				</a>
				<a href="https://wordpress.org/support/plugin/wc-ame-digital/" target="_blank" class="button button-secondary">
					<?php esc_html_e( 'Deixe-nos uma avaliação', 'wc-ame-digital' ); ?>
				</a>
			</p>
			<p><b><?php _e( 'Versão:', 'wc-ame-digital' ); ?></b> <?php echo esc_attr( self::WAD_LAST_VERSION ); ?></p>
		</div>
<?php }
}
