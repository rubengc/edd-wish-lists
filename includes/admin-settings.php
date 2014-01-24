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
			'name' => '<strong>' . __( 'Wish Lists', 'edd-wish-lists' ) . '</strong>',
			'type' => 'header'
		),
		array(
			'id' => 'edd_wl_page',
			'name' => __( 'Wish Lists Page', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'Select the page where users will view their wish lists.', 'edd-wish-lists' ) . '</p>',
			'type' => 'select',
			'options' => $pages_options
		),
		array(
			'id' => 'edd_wl_page_view',
			'name' => __( 'Wish Lists View Page', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'Select the page where users will view a wish list.', 'edd-wish-lists' ) . '</p>',
			'type' => 'select',
			'options' => $pages_options
		),
		array(
			'id' => 'edd_wl_page_edit',
			'name' => __( 'Wish Lists Edit Page', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'Select the page where users will edit a wish list.', 'edd-wish-lists' ) . '</p>',
			'type' => 'select',
			'options' => $pages_options
		),
		array(
			'id' => 'edd_wl_page_create',
			'name' => __( 'Wish Lists Create Page', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'Select the page where users will create a wish list.', 'edd-wish-lists' ) . '</p>',
			'type' => 'select',
			'options' => $pages_options
		),
		array(
			'id' => 'edd_wl_add_to_wish_list',
			'name' => __( 'Wish List Text', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'Enter the text you\'d like to appear for adding a product to the wish list', 'edd-wish-lists' ) . '</p>',
			'type' => 'text',
			'std' => 'Add to wish list'
		),
		array(
			'id' => 'edd_wl_add_to_cart',
			'name' => __( 'Add To Cart Text', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'Enter the text you\'d like to appear for adding a product from the wish list to the cart', 'edd-wish-lists' ) . '</p>',
			'type' => 'text',
			'std' => 'Add to cart'
		),
		array(
			'id' => 'edd_wl_allow_guests',
			'name' => __( 'Allow Guest List Creation', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'Allow guests to create a Wish List', 'edd-wish-lists' ) . '</p>',
			'type' => 'select',
			'options' =>  array(
				'yes' =>  __( 'Yes', 'edd-wish-lists' ),
				'no' =>  __( 'No', 'edd-wish-lists' ),
			),
			'std' => 'yes'
		),
		array(
			'id' => 'edd_wl_redirect',
			'name' => __( 'Redirect To Wish List', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'Customer will be redirected to their Wish List once download has been added', 'edd-wish-lists' ) . '</p>',
			'type' => 'select',
			'options' =>  array(
				'yes' =>  __( 'Yes', 'edd-wish-lists' ),
				'no' =>  __( 'No', 'edd-wish-lists' ),
			),
			'std' => 'yes'
		),
		array(
			'id' => 'edd_wl_icon',
			'name' => __( 'Icon', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'The icon to show next to the add to wish list', 'edd-wish-lists' ) . '</p>',
			'type' => 'select',
			'options' =>  array(
				'gift' =>  __( 'Gift', 'edd-wish-lists' ),
				'heart' =>  __( 'Heart', 'edd-wish-lists' ),
				'star' =>  __( 'Star', 'edd-wish-lists' ),
				'add' =>  __( 'Add', 'edd-wish-lists' ),
				'bookmark' =>  __( 'Bookmark', 'edd-wish-lists' ),
				'none' =>  __( 'No Icon', 'edd-wish-lists' ),
			),
			'std' => 'star'
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
			'std' => 'plain'
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