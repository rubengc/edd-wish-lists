<?php
/**
 * Functions
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Get a specific wish list
 * @param  int $wish_list_id the ID of the wish list
 * @return array               the contents of the wish list
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
 * Check if we're on the wish list page
 * @return boolean true|false
 */
function edd_wl_is_wish_list() {
	global $edd_options;

	if ( isset( $edd_options['edd_wl_page'] ) && is_page ( $edd_options['edd_wl_page'] ) ) {
		return true;	
	}
	
	return false;
}

/**
 * Check if we're on any of the wish list pages
 * @return boolean true|false
 */
function edd_wl_is_wish_list_page() {
	global $edd_options;

	$main = edd_get_option( 'edd_wl_page', '' );
	$view = edd_get_option( 'edd_wl_page_view', '' );

	if ( is_page ( $main ) || is_page ( $view ) || is_singular( 'download' ) || edd_wl_has_shortcode( 'downloads' ) ) {
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

	$uri = isset( $edd_options['edd_wl_page'] ) ? trailingslashit( get_permalink( $edd_options['edd_wl_page'] ) ) : false;

	return apply_filters( 'edd_wl_get_wish_list_uri', $uri );
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
			$page_id = isset( $edd_options['edd_wl_page_view'] ) ? $edd_options['edd_wl_page_view'] : null;
			break;
		
		case 'edit':
			$page_id = isset( $edd_options['edd_wl_page_edit'] ) ? $edd_options['edd_wl_page_edit'] : null;
			break;

		case 'create':
			$page_id = isset( $edd_options['edd_wl_page_create'] ) ? $edd_options['edd_wl_page_create'] : null;
			break;	
	}

	// get post slug from post object
	$slug = isset( $page_id ) ? get_post( $page_id )->post_name : null;
	
	return $slug;
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
		return apply_filters( 'edd_wl_get_wish_list_create_uri', add_query_arg( 'create', $uri ) );
	}

}

/**
 * Get Wish List Success URI
 * @return string
 */
function edd_wl_get_wish_list_success_uri( $type = '' ) {
	global $edd_options;

	$uri = isset( $edd_options['edd_wl_page'] ) ? get_permalink( $edd_options['edd_wl_page'] ) : false;

	if ( edd_wl_has_pretty_permalinks() ) {
		return apply_filters( 'edd_wl_get_wish_list_success_uri', edd_wl_get_wish_list_uri() . 'list/' . $type );
	}
	else {
		return apply_filters( 'edd_wl_get_wish_list_success_uri', add_query_arg( 'list', true, $uri ) );
	}
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
 *
 * @since 1.0
 * @todo  make filterable
 * @todo  make sure users get right posts
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
		'post_type' => 'edd_wish_list',
		'posts_per_page' => '-1',
		'post_status' => $status,
	);

	// get lists that belong to the currently logged in user 
	if( is_user_logged_in() ) {
		$query['author'] = $current_user->ID;
	}

	// get token from cookie and lookup lists with that token
	if ( ! is_user_logged_in() ) {
		$query['meta_query'][] = array(
			'key' => 'edd_wl_token',
			'value'    => edd_wl_get_list_token()
		);
	}

	$lists = new WP_Query( $query );

	if ( isset( $lists->found_posts ) ) {
		return $lists;
	}
	else {
		return false;
	}
	
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
 * Redirect to Wish List
 * @return [type] [description]
 */
function edd_wl_redirect_to_wish_list() {
	global $edd_options;

	// return true if 'allow guests' is enabled
	if ( isset( $edd_options['edd_wl_redirect'] ) && 'yes' == $edd_options['edd_wl_redirect'] ) {
		return true;	
	}
	
	return false;
	
}

/**
 * Filter title to include the list name on either the view or edit pages
 *
 * @since 1.0
*/
function edd_wl_wp_title( $title, $sep ) {
	$view_page = edd_get_option( 'edd_wl_page_view' );
	$edit_page = edd_get_option( 'edd_wl_page_edit' );
	
	if ( is_page( $view_page ) || is_page( $edit_page ) ) {
		if ( is_page( $view_page ) )
			$list_id = get_query_var( 'view' );
		elseif ( is_page( $edit_page ) )
			$list_id = get_query_var( 'edit' );

		$list_title = get_the_title( $list_id );

		// Prepend the list name to the site title.
		$title = $list_title . " $sep " . $title;
	}
	
	return $title;
}
add_filter( 'wp_title', 'edd_wl_wp_title', 10, 2 );


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
//	$cart_items = edd_wl_get_contents();
//	$cart_items = get_post_meta( $list_id, 'edd_wish_list', true );

	$ret = false;

	if ( is_array( $cart_items ) ) {
		foreach ( $cart_items as $item ) {
			if ( $item['id'] == $download_id ) {
				if ( isset( $options['price_id'] ) && isset( $item['options']['price_id'] ) ) {
					if ( $options['price_id'] == $item['options']['price_id'] ) {
						$ret = true;
						break;
					}
				} else {
					$ret = true;
					break;
				}
			}
		}
	}

	return (bool) apply_filters( 'edd_wl_item_in_wish_list', $ret, $download_id, $options );
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
 * Determines if the current user is on their sharing URL
 * @return boolean [description]
 * @todo  might not need this
 */
function edd_wl_is_share_url() {
	global $current_user, $wp;

	// get username from query var
	//$username = get_query_var( 'wishlist_user' );

	//$username = $current_user->user_login;

	// sharing URL. Constructed for the currently logged in user
	//$share_url = site_url('/') . edd_get_option( 'edd_wl_sharing_slug', 'wishlists' ) . '/' . $username;

	// current URL
	$current_url = home_url( add_query_arg( array(), $wp->request ) );

	if ( $current_url == $share_url ) {
		return true;
	}

	return false;
}