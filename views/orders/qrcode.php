<?php
/**
 * QrCode order
 *
 * @package PQAD
 */
?>
<div class="clear"></div>
<div class="order_data_column_container">
	<div class="order_data_column_wide">
		<h3><?php _e( 'Ame Digital', 'wc-ame-digital' ); ?></h3>
		<div id="wad-order-container">
			<p>
				<?php if ( $ame_id ) : ?>
					<strong><?php _e( 'Ame Order ID: ', 'wc-ame-digital' ); ?></strong>
					<span style="color:red;"><?php echo esc_attr( $ame_id ); ?></span><br>
				<?php endif; ?>
				
				<?php if ( $ame_nsu ) : ?>
					<strong><?php _e( 'NSU/Código de Pagamento: ', 'wc-ame-digital' ); ?></strong>
					<span style="color:red;"><?php echo esc_attr( $ame_nsu ); ?></span><br>
				<?php endif; ?>
			</p>
		</div>
	</div>
</div>

