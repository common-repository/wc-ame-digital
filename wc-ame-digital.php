<?php
/**
 * Pagamento QR Code Ame Digital
 *
 * @package PQAD
 *
 * Plugin Name: Pagamento QR Code Ame Digital
 * Description: Gateway de pagamento QR Code Ame Digital para WooCommerce.
 * Version: 1.2.7
 * Requires at least: 5.0
 * Requires PHP: 7.1
 * WC tested up to: 6.9.3
 * Author: Apiki
 * Author URI: https://apiki.com/
 * Text Domain: wc-ame-digital
 * Domain Path: /languages
 */

use Cedaro\WP\Plugin\PluginFactory;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}
/**
 * Retrieve the main plugin instance.
 *
 * @return \Cedaro\WP\Plugin
 */
function wc_ame_digital() {
	static $instance;

	if ( null === $instance ) {
		$instance = PluginFactory::create( 'wc-ame-digital' );
	}

	return $instance;
}

$container = new League\Container\Container();

/* register the reflection container as a delegate to enable auto wiring. */
$container->delegate(
	( new League\Container\ReflectionContainer() )->cacheResolutions()
);

$plugin = wc_ame_digital();

$plugin->set_container( $container );
$plugin->register_hooks( $container->get( Cedaro\WP\Plugin\Provider\I18n::class ) );
$plugin->register_hooks( $container->get( WPSteak\Providers\I18n::class ) );

$config = ( require __DIR__ . '/config.php' );

foreach ( $config['service_providers'] as $service_provider ) {
	$container->addServiceProvider( $service_provider );
}

foreach ( $config['hook_providers'] as $hook_provider ) {
	$plugin->register_hooks( $container->get( $hook_provider ) );
}
