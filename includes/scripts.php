<?php
/**
 * Scripts
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CSS
 *
 * @since 1.0
*/
function edd_wl_css() {
	wp_register_style( 'edd-wish-lists', EDD_WL_PLUGIN_URL . 'includes/css/edd-wl.css', '', EDD_WL_VERSION, 'screen' );
	wp_enqueue_style( 'edd-wish-lists' );
}
add_action( 'wp_enqueue_scripts', 'edd_wl_css', 100 );


/**
 * Print scripts
 *
 * @since 1.0
*/
function edd_wl_print_script() {
	global $edd_options;

	$edd_wish_lists = edd_wish_lists();

	if ( ! $edd_wish_lists::$add_script )
		return;
	
	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// register and enqueue
	wp_register_script( 'edd-wish-lists', EDD_WL_PLUGIN_URL . 'includes/js/edd-wl' .  $suffix . '.js', array( 'jquery' ), EDD_WL_VERSION, true );
	wp_register_script( 'edd-wish-lists-modal', EDD_WL_PLUGIN_URL . 'includes/js/modal' .  $suffix . '.js', array( 'jquery' ), EDD_WL_VERSION, true );

	wp_enqueue_script( 'edd-wish-lists' );
	wp_enqueue_script( 'edd-wish-lists-modal' );

	wp_localize_script( 'edd-wish-lists', 'edd_wl_scripts', array(
		 'redirect_to_wish_list'    => ( edd_wl_redirect_to_wish_list() || edd_wl_is_wish_list() ) ? '1' : '0',
		 'wish_list_page'           => edd_wl_get_wish_list_uri(),
		 'wish_list_add'           => edd_wl_get_wish_list_create_uri(),
		 'ajax_nonce'              => wp_create_nonce( 'edd_wl_ajax_nonce' ),
		
		)
	);

}
add_action( 'wp_footer', 'edd_wl_print_script' );