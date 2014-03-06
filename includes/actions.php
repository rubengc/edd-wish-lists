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

/**
 * Performs redirect actions
 *
 * @since  	1.0
 * @uses  	edd_wl_is_private_list()
 * @uses 	edd_wl_get_wish_list_uri()
 * @return 	void
 */
function edd_wl_redirects() {

	// Prevent private lists from being viewed. Also only allows users to access edit slugs with own list ID
	if ( edd_wl_is_private_list() || ( ! edd_wl_is_users_list( get_query_var( 'edit' ) ) && get_query_var( 'edit' ) ) ) {
		$redirect = apply_filters( 'edd_wl_private_redirect', edd_wl_get_wish_list_uri() );
		wp_redirect( $redirect );
		edd_die();	
	}

	// redirect if edit or view page is accessed but edit/view query_var does not exist
	if ( ( edd_wl_is_page( 'view' ) && ! get_query_var( 'view' ) ) || ( edd_wl_is_page( 'edit' ) && ! get_query_var( 'edit' ) ) ) {
		wp_redirect( edd_wl_get_wish_list_uri() );
		edd_die();
	}

}
add_action( 'template_redirect', 'edd_wl_redirects' );