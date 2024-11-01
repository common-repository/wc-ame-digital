<?php
/**
 * Checkout Order Received Mobile
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
    <div class="wad-qrcode-mobile-container">
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
		<button class="wad-btn-primary"><?php _e( 'PAGAR', 'wc-ame-digital' ); ?></button>
		<div class="wad-qrcode-description">
			<p><?php _e( 'Clique no botão acima para abrir o aplicativo <br> AME no celular e efetuar o pagamento.', 'wc-ame-digital' ); ?></p>
		</div>
    </div>
    <form id="receipt_form">
        <input type="hidden" id="admin-ajax" value="<?php echo admin_url( 'admin-ajax.php' ); ?>">
        <input type="hidden" name="order_id" id="order_id" value="<?php echo esc_attr( $order_id ); ?>" />
		<input type="hidden" name="wad_deeplink" id="wad_deeplink" value="<?php echo esc_attr( $deep_url ); ?>" />
        <?php echo wp_nonce_field( 'order-received'.$order_id, 'wad_thankyou_mobile_nonce', true, false ); ?>
	</form>
</div>
