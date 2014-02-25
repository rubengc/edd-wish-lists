<?php
/**
 * Wish list functions
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Removes a Download from the Wish List. Based off edd_remove_from_cart()
 *
 * @since 1.0
 * @param int $wish_list_key the cart key to remove
 * @return array Updated cart items
 */
function edd_remove_from_wish_list( $wish_list_key, $list_id ) {

	// get list
	$wish_list = get_post_meta( $list_id, 'edd_wish_list', true );

	do_action( 'edd_wl_pre_remove_from_wish_list', $wish_list_key );

	if ( ! is_array( $wish_list ) ) {
		return true; // Empty cart
	} 
	else {
		$item_id = isset( $wish_list[ $wish_list_key ][ 'id' ] ) ? $wish_list[ $wish_list_key ][ 'id' ] : null;
		unset( $wish_list[ $wish_list_key ] );
	}

	// update list
	update_post_meta( $list_id, 'edd_wish_list', $wish_list );

	do_action( 'edd_wl_post_remove_from_wish_list', $wish_list_key, $item_id );

	return $wish_list; // The updated wish list
}

/**
 * Add To Wish List
 *
 * Adds a download ID to the wish list. Based off edd_add_to_cart()
 *
 * @since 1.0
 *
 * @param int $download_id Download IDs to be added to the cart
 * @param array $options Array of options, such as variable price
 *
 * @return string Cart key of the new item
 */
function edd_wl_add_to_wish_list( $download_id, $options = array(), $list_id ) {
	
	// get current post meta for wish list	
	$list = get_post_meta( $list_id, 'edd_wish_list', true );

	$download = get_post( $download_id );

	if( 'download' != $download->post_type )
		return; // Not a download product

	if ( ! current_user_can( 'edit_post', $download->ID ) && ( $download->post_status == 'draft' || $download->post_status == 'pending' ) )
		return; // Do not allow draft/pending to be purchased if can't edit. Fixes #1056

	if ( edd_has_variable_prices( $download_id )  && ! isset( $options['price_id'] ) ) {
		// Forces to the first price ID if none is specified and download has variable prices
		$options['price_id'] = 0;
	}

	$to_add = array();

	if( isset( $options['quantity'] ) ) {
		$quantity = absint( $options['quantity'] );
		unset( $options['quantity'] );
	} else {
		$quantity = 1;
	}

	if ( isset( $options['price_id'] ) && is_array( $options['price_id'] ) ) {
		// Process multiple price options at once
		foreach ( $options['price_id'] as $price ) {
			$price_options = array( 'price_id' => $price );
			$to_add[] = apply_filters( 'edd_add_to_wish_list_item', array( 'id' => $download_id, 'options' => $price_options, 'quantity' => $quantity ) );
		}
	} else {
		// Add a single item
		$to_add[] = apply_filters( 'edd_add_to_wish_list_item', array( 'id' => $download_id, 'options' => $options, 'quantity' => $quantity ) );
	}

	if ( is_array( $list ) ) {
		$list = array_merge( $list, $to_add );
	} else {
		$list = $to_add;
	}

	// store in meta_key. Will either be new array or a merged array
	update_post_meta( $list_id, 'edd_wish_list', $list );	

	// create token for logged out user
	edd_wl_create_token( $list_id );

	return $list;
}

/**
 * Get the Item Position in list
 *
 * @since 1.0.2
 *
 * @param int   $download_id ID of the download to get position of
 * @param array $options array of price options
 * @return bool|int|string false if empty cart |  position of the item in the cart
 */
function edd_wl_get_item_position_in_list( $download_id = 0, $options = array() ) {
	$cart_items = edd_get_cart_contents();

	$list_id = edd_wl_get_query()->posts[0]->ID;
	$list_items = edd_wl_get_wish_list( $list_id );

	if ( ! is_array( $list_items ) ) {
		return false; // Empty list
	} else {
		foreach ( $list_items as $position => $item ) {
			if ( $item['id'] == $download_id ) {
				if ( isset( $options['price_id'] ) && isset( $item['options']['price_id'] ) ) {
					if ( (int) $options['price_id'] == (int) $item['options']['price_id'] ) {
						return $position;
					}
				} else {
					return $position;
				}
			}
		}
	}
	return false; // Not found
}