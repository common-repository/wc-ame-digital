<?php
/**
 * Orders
 *
 * @package PQAD
 */

declare(strict_types=1);

namespace PQAD\Repositories;

/**
 * Orders class.
 */
class Pqad_Orders
{
    public static function get_all_orders_by_id( $id ) {
        $wc_order = wc_get_orders(
            [
                'meta_key'         => '_wad_api_order_id',
                'meta_value'       => $id,
                'limit'            => -1,
                'return'           => 'ids',
                'suppress_filters' => false
			]
        );

        return $wc_order;
	}

	public static function get_all_id_orders() {
		$orders = wc_get_orders(
			[
				'limit'  => -1,
				'status' => [ 'wc-pending' ],
				'return' => 'ids'
			]
		);

		return $orders;

	}
}
