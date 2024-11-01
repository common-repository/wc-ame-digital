<?php
/**
 * Checkout Order Received
 *
 * @package PQAD
 */
?>
<div class="woocommerce">
<div id="ame-digital-qrcode-thankyou">
	<div id="ame-digital-top">
		<img src="<?php echo esc_url( $logo ); ?>" width="160" height="160" />
		<h2><?php echo esc_attr( $title ); ?></h2>
	</div>
    <div class="wad-qrcode-container">
		<div class="wad-qrcode-price-container">
			<p><strong><?php _e( 'Valor da compra:', 'wc-ame-digital' ); ?></strong></p>
			<div id="wad-qrcode-price">
				<p><?php echo wc_price( esc_attr( $amount ) ); ?></p>
			</div>
			<?php if ( $is_cashback ) : ?>
				<div id="wad-qrcode-cashback">
					<?php printf( '<p>%s <strong>R$%s</strong> %s</p>',
					__( 'Receba', 'wc-ame-digital' ),
					$cashback,
					__( 'em até 30 dias.', 'wc-ame-digital' ) ); ?>
				</div>
			<?php endif; ?>
		</div>
		<div id="wad-qrcode-footer">
			<div class="wad-qrcode-footer-image">
				<img src="<?php echo esc_url( $qrcode_url ); ?>" width="250" height="250" />
			</div>

			<div class="wad-qrcode-description">
				<p><?php _e( 'Abra o aplicativo Ame em seu celular <br> e escaneie o código <strong>acima</strong>.', 'wc-ame-digital' ); ?></p>
			</div>
		</div>
    </div>
    <form id="receipt_form">
        <input type="hidden" id="admin-ajax" value="<?php echo admin_url( 'admin-ajax.php' ); ?>">
        <input type="hidden" name="order_id" id="order_id" value="<?php echo esc_attr( $order_id ); ?>" />
        <?php echo wp_nonce_field( 'order-pay'.$order_id, 'wad_thankyou_nonce', true, false ); ?>
	</form>
</div>
