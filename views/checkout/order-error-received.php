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
		<div id="wad-qrcode-footer">
			<div class="wad-qrcode-description">
				<p><?php echo esc_attr( $error_message ); ?></p>
				<p><?php _e( 'Entre em contato com a AME!', 'wc-ame-digital' ); ?></p>
				<p><strong><?php _e( 'O pedido foi cancelado!', 'wc-ame-digital' ); ?></strong></p>
			</div>
		</div>
    </div>
    <form id="receipt_form">
        <input type="hidden" id="admin-ajax" value="<?php echo admin_url( 'admin-ajax.php' ); ?>">
        <input type="hidden" name="order_id" id="order_id" value="<?php echo esc_attr( $order_id ); ?>" />
        <?php echo wp_nonce_field( 'order-pay'.$order_id, 'wad_thankyou_nonce', true, false ); ?>
	</form>
</div>
