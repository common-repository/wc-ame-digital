<?php
/**
 * Config.
 *
 * Define configurations for this plugin,
 * use it for define your service providers and hooks providers,
 * the classes will be loaded in order as defined.
 *
 * @package PQAD
 */

return [
	'service_providers' => [],
	'hook_providers'    => [
		PQAD\Providers\Assets\Pqad_Admin::class,
		PQAD\Providers\Assets\Pqad_Editor::class,
		PQAD\Providers\Assets\Pqad_Login::class,
		PQAD\Providers\Assets\Pqad_Theme::class,
		PQAD\Providers\Woocommerce\Pqad_Checkouts::class,
		PQAD\Providers\Dashboard\Pqad_Options::class,
		PQAD\Providers\Gateways\Pqad_QrCode::class,
		PQAD\Providers\Orders\Pqad_QrCode::class,
	],
];
