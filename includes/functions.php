<?php
/**
 * Functions
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns the path to the templates directory
 *
 * @since 1.0
 * @return string
 */
function edd_wl_get_templates_dir() {
	return EDD_WL_PLUGIN_DIR . 'templates';
}

/**
 * Add path for template files
 *
 * @since 1.0
*/
function edd_wl_edd_template_paths( $file_paths ) {
	$file_paths[95] = edd_wl_get_templates_dir();
	return $file_paths;
}
add_filter( 'edd_template_paths', 'edd_wl_edd_template_paths' );

/**
 * Get a specific wish list
 * @param  int $wish_list_id 	the ID of the wish list
 * @return array               	the contents of the wish list
 */
function edd_wl_get_wish_list( $wish_list_id ) {
	// retrieve the wish list
	return get_post_meta( $wish_list_id, 'edd_wish_list', true );
}

/**
 * Has pretty permalinks
 *
 * @since 1.0
*/
function edd_wl_has_pretty_permalinks() {
	global $wp_rewrite;
	
	if ( $wp_rewrite->using_permalinks() )
		return true;
	
	return false;
}

/**
 * List is private
 *
 * This is used to redirect, or prevent viewing or editing of private lists
 * @return [type]
 */
function edd_wl_is_private_list() {
	if ( get_query_var( 'view' ) )
		$list_id = get_query_var( 'view' );
	elseif ( get_query_var( 'edit' ) )
		$list_id = get_query_var( 'edit' );
	else
		$list_id = '';

	if ( ! $list_id )
		return;

	$list_status = get_post_status( $list_id );

	if ( 'private' == $list_status && ! edd_wl_is_users_list( $list_id ) && ( edd_wl_is_page( 'view' ) || edd_wl_is_page( 'edit' ) ) )
		return true;
}


/**
 * Get the status of a list (post)
 *
 * @since 1.0
*/
function edd_wl_get_list_status( $post_id = '' ) {
	$post_id = isset( $post_id ) ? $post_id : get_the_ID();

	$status = get_post_status( $post_id );

	switch ( $status ) {
		case 'publish':
			$status = 'public';
			break;

		case 'private':
			$status = 'private';
			break;	
	}

	return $status;
}

/**
 * List of statuses
 *
 * @since 1.0
 * @return  array statuses
*/
function edd_wl_get_list_statuses() {
	$statuses = array(
		'public', 
		'private'
	);

	return $statuses;
}

/**
 * Check if we're on a certain page
 * @return boolean true|false
 */
function edd_wl_is_page( $page = '' ) {
	global $edd_options;

	switch ( $page ) {
		case 'wish-lists':
			$id = isset( $edd_options['edd_wl_page'] ) ? $edd_options['edd_wl_page'] : false;
		break;

		case 'view':
			$id = isset( $edd_options['edd_wl_page_view'] ) ? $edd_options['edd_wl_page_view'] : false;
		break;

		case 'edit':
			$id = isset( $edd_options['edd_wl_page_edit'] ) ? $edd_options['edd_wl_page_edit'] : false;
		break;

		case 'create':
			$id = isset( $edd_options['edd_wl_page_create'] ) ? $edd_options['edd_wl_page_create'] : false;
		break;
	}

	if ( is_page( $id ) ) {
		return true;	
	}
	
	return false;
}



/**
 * Get Wish List URI
 * @return string
 */
function edd_wl_get_wish_list_uri() {
	global $edd_options;

	$uri = isset( $edd_options['edd_wl_page'] ) ?  get_permalink( $edd_options['edd_wl_page'] ) : false;

	if ( edd_wl_has_pretty_permalinks() ) {
		return apply_filters( 'edd_wl_get_wish_list_uri', trailingslashit( $uri ) );
	}		
	else {
		return apply_filters( 'edd_wl_get_wish_list_uri', $uri );
	}
}

/**
 * Get the URI for viewing a wish list
 * @return string
 */
function edd_wl_get_wish_list_view_uri( $id = '' ) {
	global $edd_options;

	$uri = isset( $edd_options['edd_wl_page_view'] ) ? get_permalink( $edd_options['edd_wl_page_view'] ) : false;
	
	if ( edd_wl_has_pretty_permalinks() ) {
		return apply_filters( 'edd_wl_get_wish_list_view_uri', trailingslashit( $uri ) . $id );
	}		
	else {
		return apply_filters( 'edd_wl_get_wish_list_view_uri', add_query_arg( 'view', $id, $uri ) );
	}
}

/**
 * Get Wish List Edit URI
 * @return string
 */
function edd_wl_get_wish_list_edit_uri( $id = '') {
	global $edd_options;

	$uri = isset( $edd_options['edd_wl_page_edit'] ) ? get_permalink( $edd_options['edd_wl_page_edit'] ) : false;
	
	if ( edd_wl_has_pretty_permalinks() ) {
		return apply_filters( 'edd_wl_get_wish_list_edit_uri', trailingslashit( $uri ) . $id );
	}		
	else {
		return apply_filters( 'edd_wl_get_wish_list_edit_uri', add_query_arg( 'edit', $id, $uri ) );
	}
}

/**
 * Get Wish List create URI
 * @return string
 */
function edd_wl_get_wish_list_create_uri() {
	global $edd_options;

	$uri = isset( $edd_options['edd_wl_page_create'] ) ? get_permalink( $edd_options['edd_wl_page_create'] ) : false;

	if ( edd_wl_has_pretty_permalinks() ) {
		return apply_filters( 'edd_wl_get_wish_list_create_uri', trailingslashit( $uri )  );
	}		
	else {
		return apply_filters( 'edd_wl_get_wish_list_create_uri', $uri );
	}

}

/**
 * Returns the slug of the page selected for view
 *
 * @since 1.0
 * @return string
 * @global $edd_options
 * @param string $page_name name of page
*/
function edd_wl_get_page_slug( $page_name = '' ) {
	global $edd_options;

	switch ( $page_name ) {
		case 'view':
			$page_id = isset( $edd_options['edd_wl_page_view'] ) && 'none' != $edd_options['edd_wl_page_view'] ? $edd_options['edd_wl_page_view'] : null;
			break;
		
		case 'edit':
			$page_id = isset( $edd_options['edd_wl_page_edit'] ) && 'none' != $edd_options['edd_wl_page_edit'] ? $edd_options['edd_wl_page_edit'] : null;
			break;

		case 'create':
			$page_id = isset( $edd_options['edd_wl_page_create'] ) && 'none' != $edd_options['edd_wl_page_create'] ? $edd_options['edd_wl_page_create'] : null;
			break;	
	}

	// get post slug from post object
	$slug = isset( $page_id ) ? get_post( $page_id )->post_name : null;
	
	return $slug;
}



/**
 * Returns the URL to remove an item from the wish list
 *
 * @since 1.0
 * @global $post
 * @param int $cart_key Cart item key
 * @param object $post Download (post) object
 * @param bool $ajax AJAX?
 * @return string $remove_url URL to remove the wish list item
 */
function edd_wl_remove_item_url( $cart_key, $post, $ajax = false ) {
	global $post;

	if( is_page() ) {
		$current_page = add_query_arg( 'page_id', $post->ID, home_url( 'index.php' ) );
	} else if( is_singular() ) {
		$current_page = add_query_arg( 'p', $post->ID, home_url( 'index.php' ) );
	} else {
		$current_page = edd_get_current_page_url();
	}
	$remove_url = add_query_arg( array( 'cart_item' => $cart_key, 'edd_action' => 'remove' ), $current_page );

	return apply_filters( 'edd_remove_item_url', $remove_url );
}

/**
 * The query to return the posts on the main wish lists page
 * retrieves post object for either logged in user or logged out
 * 
 * @since 1.0
*/
function edd_wl_get_query( $status = array( 'publish', 'private' ) ) {

	global $current_user;
	get_currentuserinfo();

	if ( 'public' == $status ) {
		$status = 'publish';
	}

	// return if user is logged out and they don't have a token
	if ( ! is_user_logged_in() && ! edd_wl_get_list_token() )
		return null;

	// initial query
	$query = array(
		'post_type' 		=> 'edd_wish_list',
		'posts_per_page' 	=> '-1',
		'post_status' 		=> $status,
	);

	// get lists that belong to the currently logged in user 
	if( is_user_logged_in() ) {
		$query['author'] = $current_user->ID;
	}

	// get token from cookie and lookup lists with that token
	if ( ! is_user_logged_in() ) {
		$query['meta_query'][] = array(
			'key' 		=> 'edd_wl_token',
			'value'		=> edd_wl_get_list_token()
		);
	}

	$lists = new WP_Query( $query );

	if ( isset( $lists->found_posts ) ) {
		return $lists;
	}
	
	return null;
}

/**
 * Check for existance of shortcode
 * 
 * @param  string  $shortcode
 * @return boolean
 * @since  1.0
 */
function edd_wl_has_shortcode( $shortcode = '' ) {
	global $post;

	// false because we have to search through the post content first
	$found = false;

	// if no short code was provided, return false
	if ( ! $shortcode ) {
		return $found;
	}

	if (  is_object( $post ) && stripos( $post->post_content, '[' . $shortcode ) !== false ) {
		// we have found the short code
		$found = true;
	}

	// return our final results
	return $found;
}


/**
 * Add all items in wish list to the cart
 *
 * Adds all downloads within a taxonomy term to the cart.
 *
 * @since 1.0.6
 * @param int $list_id ID of the list
 * @return array Array of IDs for each item added to the cart
 */
function edd_wl_add_all_to_cart( $list_id ) {
	$cart_item_ids = array();

	$items = edd_wl_get_wish_list( $list_id );

	if ( $items ) {
		foreach ( $items as $item ) {
			// check that they aren't already in the cart
			if ( edd_item_in_cart( $item['id'], $item['options'] ) )
				continue;

			edd_add_to_cart( $item['id'], $item['options'] );
			$cart_item_ids[] = $item['id'];
		}
	}
}

/**
 * Show which lists the current item is already added to
 *
 * @since 1.0
 * @uses edd_wl_item_in_wish_list()
 */
function edd_wl_lists_included( $download_id, $options ) {
	ob_start();

	$found_lists = edd_wl_item_in_wish_list( $download_id, $options );

	if ( $found_lists ) {
		$messages = edd_wl_messages();
		echo '<p>';
		echo $messages['lists_included'];

		$list_names = array();

		foreach ( $found_lists as $list_id ) {
			$list_names[] = get_the_title( $list_id );
		}

		// comma separate
		echo implode(', ', $list_names );

		echo '</p>';
	}

	$html = ob_get_clean();
	return apply_filters( 'edd_wl_lists_included', $html );
}

/**
 * Checks the see if an item is already in the wish_list and returns a boolean. Modelled from edd_item_in_cart()
 *
 * @since 1.0
 *
 * @param int   $download_id ID of the download to remove
 * @param array $options
 * @return bool Item in the cart or not?
 * @todo  modify function to accept list ID, or run a search with get_posts or osmething
 */
function edd_wl_item_in_wish_list( $download_id = 0, $options = array() ) {

	$posts = edd_wl_get_query()->posts;

	if ( $posts ) {
		$ids = array();

		foreach ( $posts as $post ) {
			$ids[] = $post->ID;
		}

	}

	if ( $ids ) {

		$found_ids = array();

		foreach ( $ids as $id ) {

			$cart_items = get_post_meta( $id, 'edd_wish_list', true );
			$found = false;

			if ( $cart_items ) {
				foreach ( $cart_items as $item ) {
					if ( $item['id'] == $download_id ) {
						if ( isset( $options['price_id'] ) && isset( $item['options']['price_id'] ) ) {
							if ( $options['price_id'] == $item['options']['price_id'] ) {
								$found = true;
								break;
							}
						} 
						else {
							$found = true;
							break;
						}
					}
				}
			}
			
			// add each found id to array
			if ( $found ) {
				$found_ids[] = $id;
			}

		}

		return $found_ids;
	}

}

/**
 * Allow guest creation of Wist List
 * @return boolean true if Guests are allowed, false otherwise
 */
function edd_wl_allow_guest_creation() {
	global $edd_options;

	// return true if 'allow guests' is enabled
	if ( ( isset( $edd_options['edd_wl_allow_guests'] ) && 'no' == $edd_options['edd_wl_allow_guests'] ) && ! is_user_logged_in() ) {
		return false;
	}
	
	return true;
}

/**
 * Total price of items in wish list
 * 
 * Used on front end and also admin
 * @since 1.0
 * @param $list_id ID of list
 * @todo  update total as items are removed from list via ajax
 */
function edd_wl_get_list_total( $list_id ) {
	// get contents of cart
	$list_items = get_post_meta( $list_id, 'edd_wish_list', true );

	$total = array();

	if ( $list_items ) {
		foreach ( $list_items as $item ) {
			$item_price = edd_get_cart_item_price( $item['id'], $item['options'] );
			$item_price = round( $item_price, 2 );
			$total[] = $item_price;
		}
	}

	// add up values
	$total = array_sum( $total );

	$total = esc_html( edd_currency_filter( edd_format_amount( $total ) ) );

	return apply_filters( 'edd_wl_list_total', $total );
}

/**
 * Let the customer know they have already purchased a particular download
 * @param  [type] $download_id       [description]
 * @param  [type] $variable_price_id [description]
 * @since  1.0
 * @return string
 */
function edd_wl_has_purchased( $download_id, $variable_price_id ) {
	global $user_ID;

	$has_purchased = edd_wl_get_purchases( $user_ID, $download_id, $variable_price_id );

	if ( $has_purchased ) 
		return apply_filters( 'edd_wl_has_purchased', '<span class="edd-wl-item-purchased">Already purchased</span>' );
	
	return null;
}

/**
 * Get a customer's purchases
 * @param  [type] $user_id           [description]
 * @param  [type] $download_id       [description]
 * @param  [type] $variable_price_id [description]
 * @since  1.0
 * @return [type]                    [description]
 */
function edd_wl_get_purchases( $user_id, $download_id, $variable_price_id = null ) {
	$users_purchases = edd_get_users_purchases( $user_id );

	$return = false;

	if ( $users_purchases ) {
		foreach ( $users_purchases as $purchase ) {
			$purchased_files = edd_get_payment_meta_downloads( $purchase->ID );

			if ( is_array( $purchased_files ) ) {
				foreach ( $purchased_files as $download ) {
					$variable_prices = edd_has_variable_prices( $download['id'] );

					if ( $variable_prices && ! is_null( $variable_price_id ) && $variable_price_id !== false ) {
						if ( isset( $download['options']['price_id'] ) && $variable_price_id == $download['options']['price_id'] ) {
							$return = true;
							break 2;
						} 
						else {
							$return = false;
						}
					} 
					elseif ( $download_id == $download['id']) {
						$return = true;
					}
				}
			}
		}
	}

	return $return;
}

/**
 * Validate emails used in the email share box
 * @param  string $emails string to emails to check
 * @return boolean true if all emails are valid, false if one is not valid
 */
function edd_wl_validate_share_emails( $emails ) {

	// explode string into array
	$emails = explode( ',', $emails );

	// remove whitespace and clean
	$emails = array_filter( array_map( 'trim', $emails ) );

	if ( $emails ) {
		foreach ( $emails as $email ) {

			if ( ! is_email( $email ) ) {
				$valid_email = false;
				break;
			}
			else {
				$valid_email = true;
				continue;
			}

		}
	}

	if ( $valid_email )
		return $valid_email;
	
	return null;
}