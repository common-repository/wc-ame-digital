<?php declare(strict_types = 1);

namespace PQAD\Providers\Dashboard;

use WPSteak\Providers\AbstractHookProvider;
use PQAD\Entities\Pqad_Api as Api_Entity;

class Pqad_Options extends AbstractHookProvider
{

	public function register_hooks() {
		$this->add_filter( 'plugin_action_links_wc-ame-digital/wc-ame-digital.php', 'plugin_links' );
		$this->add_filter( 'plugin_row_meta', 'support_links', 10, 2 );
	}
	/**
	 * Add link settings page
	 *
	 * @since 1.0
	 * @param Array $links
	 * @return Array
	 */
	public function plugin_links( $links ) {
		$qrcode_settings = [
			sprintf(
				'<a href="%s">%s</a>',
				'admin.php?page=wc-settings&tab=checkout&section=ame-digital',
				__( 'Configurações', 'wc-ame-digital' )
			)
		];

		return array_merge( $qrcode_settings, $links );
	}
	/**
	 * Add support link page
	 *
	 * @since 1.0
	 * @param Array $links
	 * @return Array
	 */
	public function support_links( $links, $file ) {

		if ( strpos( $file, 'wc-ame-digital.php' ) !== false ) {
			$new_links = [
				'support' => sprintf( '<a href="%s" target="_blank">%s</a>', Api_Entity::SUPPORT_URL, __( 'Suporte da Apiki', 'wc-ame-digital' ) ),
			];

			$links = array_merge( $links, $new_links );
		}

		return $links;
	}
}
