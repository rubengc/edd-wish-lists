<?php
/**
 * Admin settings
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Settings
 *
 * @since 1.0
*/
function edd_wl_settings( $settings ) {
	
	$pages = get_pages();
	$pages_options = array( 0 => '' ); // Blank option

	if ( $pages ) {
		foreach ( $pages as $page ) {
			$pages_options[ $page->ID ] = $page->post_title;
		}
	}

	$plugin_settings = array(
		array(
			'id' => 'edd_wl_header',
			'name' => '<strong>' . sprintf( __( '%s', 'edd-wish-lists' ), edd_wl_get_label_plural() ) . '</strong>',
			'type' => 'header'
		),
		array(
			'id' => 'edd_wl_page',
			'name' => sprintf( __( '%s Page', 'edd-wish-lists' ), edd_wl_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Select the page where users will view their %s. This page should include the [edd_wish_lists] shortcode.', 'edd-wish-lists' ), edd_wl_get_label_plural( true ) ) . '</p>',
			'type' => 'select',
			'options' => $pages_options
		),
		array(
			'id' => 'edd_wl_page_view',
			'name' => sprintf( __( '%s View Page', 'edd-wish-lists' ), edd_wl_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Select the page where users will view each %s. This page should include the [edd_wish_lists_view] shortcode.', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'select',
			'options' => $pages_options
		),
		array(
			'id' => 'edd_wl_page_edit',
			'name' => sprintf( __( '%s Edit Page', 'edd-wish-lists' ), edd_wl_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Select the page where users will edit a %s. This page should include the [edd_wish_lists_edit] shortcode.', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'select',
			'options' => $pages_options
		),
		array(
			'id' => 'edd_wl_page_create',
			'name' => sprintf( __( '%s Create Page', 'edd-wish-lists' ), edd_wl_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Select the page where users will create a %s. This page should include the [edd_wish_lists_create] shortcode.', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'select',
			'options' => $pages_options
		),
		array(
			'id' => 'edd_wl_add_to_wish_list',
			'name' => sprintf( __( '%s Text', 'edd-wish-lists' ), edd_wl_get_label_singular() ),
			'desc' => '<p class="description">' . sprintf( __( 'Enter the text you\'d like to appear for adding a %s to the %s', 'edd-wish-lists' ), edd_get_label_singular( true ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'text',
			'std' => sprintf( __( 'Add to %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ),
		),
		array(
			'id' => 'edd_wl_add_to_cart',
			'name' => __( 'Add To Cart Text', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . sprintf( __( 'Enter the text you\'d like to appear for adding a %s from the %s to the cart', 'edd-wish-lists' ), edd_get_label_singular( true ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'text',
			'std' => 'Add to cart'
		),
		array(
			'id' => 'edd_wl_allow_guests',
			'name' => sprintf( __( 'Allow Guest %s Creation', 'edd-wish-lists' ), edd_wl_get_label_singular() ),
			'desc' => '<p class="description">' . sprintf( __( 'Allow guests to create a %s', 'edd-wish-lists' ), edd_wl_get_label_singular() ) . '</p>',
			'type' => 'select',
			'options' =>  array(
				'yes' =>  __( 'Yes', 'edd-wish-lists' ),
				'no' =>  __( 'No', 'edd-wish-lists' ),
			),
			'std' => 'yes'
		),
		array(
			'id' => 'edd_wl_redirect',
			'name' => sprintf( __( 'Redirect To %s', 'edd-wish-lists' ), edd_wl_get_label_singular() ),
			'desc' => '<p class="description">' . sprintf( __( 'Customer will be redirected to their %s once download has been added', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'select',
			'options' =>  array(
				'yes' =>  __( 'Yes', 'edd-wish-lists' ),
				'no' =>  __( 'No', 'edd-wish-lists' ),
			),
			'std' => 'no'
		),
		array(
			'id' => 'edd_wl_icon',
			'name' => __( 'Icon', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . sprintf( __( 'The icon to show next to the add to %s links', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'select',
			'options' =>  apply_filters( 'edd_wl_icons', 
				array(
					'gift' =>  __( 'Gift', 'edd-wish-lists' ),
					'heart' =>  __( 'Heart', 'edd-wish-lists' ),
					'star' =>  __( 'Star', 'edd-wish-lists' ),
					'add' =>  __( 'Add', 'edd-wish-lists' ),
					'bookmark' =>  __( 'Bookmark', 'edd-wish-lists' ),
					'none' =>  __( 'No Icon', 'edd-wish-lists' ),
				)
			),
			'std' => 'gift'
		),
		array(
			'id' => 'edd_wl_button_style',
			'name' => __( 'Default Button Style', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'Display a button or a plain text link', 'edd-wish-lists' ) . '</p>',
			'type' => 'select',
			'options' =>  array(
				'plain' =>  __( 'Plain Text', 'edd-wish-lists' ),
				'button' =>  __( 'Button', 'edd-wish-lists' ),
			),
			'std' => 'button'
		),
		array(
			'id' => 'edd_wl_services',
			'name' => __( 'Sharing Services', 'edd-wish-lists' ),
			'desc' => __( 'Select the services you\'d like to show', 'edd-wish-lists' ),
			'type' => 'multicheck',
			'options' => apply_filters( 'edd_wl_settings_services', array(
					'twitter' =>  __( 'Twitter', 'edd-wish-lists' ),
					'facebook' =>  __( 'Facebook', 'edd-wish-lists' ),
					'googleplus' =>  __( 'Google+', 'edd-wish-lists' ),
					'linkedin' =>  __( 'LinkedIn', 'edd-wish-lists' ),
				)
			)
		),
	);

	return array_merge( $settings, $plugin_settings );
}
add_filter( 'edd_settings_extensions', 'edd_wl_settings' );