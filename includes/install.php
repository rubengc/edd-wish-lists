<?php
/**
 * Install function
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Installs the pages recommended by the plugin and inserts shortcodes
 *
 * @since 1.0
 * @return void
 */
function edd_wl_install() {
	global $wpdb;

	// Flush the rewrite rules
	flush_rewrite_rules();

	// wishlist
	$wishlist = wp_insert_post( 
		array(
			'post_title'     	=> sprintf( __( '%s', 'edd-wish-lists' ), edd_wl_get_label_plural() ),
			'post_content'   	=> '[edd_wish_lists]',
			'post_status'    	=> 'publish',
			'post_author'    	=> 1,
			'post_type'      	=> 'page',
			'comment_status' 	=> 'closed'
		)
	);

	// view
	$view = wp_insert_post( 
		array(
			'post_title'     	=> __( 'View', 'edd-wish-lists' ),
			'post_content'   	=> '[edd_wish_lists_view]',
			'post_status'    	=> 'publish',
			'post_author'    	=> 1,
			'post_parent'		=> $wishlist,
			'post_type'      	=> 'page',
			'comment_status' 	=> 'closed'
		)
	);

	// edit
	$edit = wp_insert_post( 
		array(
			'post_title'     	=> __( 'Edit', 'edd-wish-lists' ),
			'post_content'   	=> '[edd_wish_lists_edit]',
			'post_status'    	=> 'publish',
			'post_author'    	=> 1,
			'post_parent'		=> $wishlist,
			'post_type'      	=> 'page',
			'comment_status' 	=> 'closed'
		)
	);

	// create
	$create = wp_insert_post( 
		array(
			'post_title'     	=> __( 'Create', 'edd-wish-lists' ),
			'post_content'   	=> '[edd_wish_lists_create]',
			'post_status'    	=> 'publish',
			'post_author'    	=> 1,
			'post_parent'		=> $wishlist,
			'post_type'      	=> 'page',
			'comment_status' 	=> 'closed'
		)
	);

	// Store our page IDs
	$options = array(
		'edd_wl_page' 			=> $wishlist,
		'edd_wl_page_view'  	=> $view,
		'edd_wl_page_edit'  	=> $edit,
		'edd_wl_page_create'  	=> $create
	);

	// get EDD options
	$edd_options = get_option( 'edd_settings' );

	// our options
	$options = array_merge( $options, $edd_options );

	// update the plugin settings to show these pages
	update_option( 'edd_settings', $options );
}