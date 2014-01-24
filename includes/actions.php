<?php
/**
 * Actions
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Process the Add all to cart request
 *
 * @since 1.0
 *
 * @param $data
 */
function edd_wl_process_purchase_all( $data ) {
	$cart_items = edd_wl_add_all_to_cart( $data['list_id'] );

	// redirect straight to checkout with items added
	wp_redirect( edd_get_checkout_uri() );

	edd_die();
}
add_action( 'edd_wl_purchase_all', 'edd_wl_process_purchase_all' );