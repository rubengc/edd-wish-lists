<?php
/**
 * User functions
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Determines if a user has a wishlist or not
 *
 * @since  1.0
 * @return boolean true if posts exist, false otherwise
 * @todo  modify to allow for detection of guest wishlists
 * @todo  delete - can just use edd_wl_get_query() to see if there are lists
 */
function edd_wl_has_wish_lists() {
	// if ( ! is_user_logged_in() )
	// 	return;

	global $current_user;
	get_currentuserinfo();                      

	$args =  array(
		'post_type' => 'edd_wish_list',
		'posts_per_page' => '-1',
		'author' => $current_user->ID,
	);

	$posts = get_posts( $args );

	// author has posts
	if ( $posts )
		return true;

	return false;
}




/**
 * Create a token for logged out users
 *
 * @since 1.0
 * @param int $list_id list ID
*/
function edd_wl_create_token( $list_id = '' ) {
	// if user is not logged in, we check to see if they already have a token.
	// If not, we create a token and store it as a cookie
	// Each list that is created will have this token stored with it
	if ( ! is_user_logged_in() ) {
		$token = edd_wl_get_list_token();

		if ( $token ) {
			update_post_meta( $list_id, 'edd_wl_token', $token );
		}
		else {
			$cookie = setcookie( 'edd_wl_token', time(), time()+3600*24*30, COOKIEPATH, COOKIE_DOMAIN );
			// store edd_wl_token against list with the same time stamp
			// we'll use this to verify that this belongs to the user
			update_post_meta( $list_id, 'edd_wl_token', time() );	
		}
	}
}


/**
 * Retrieve a saved wish list token. Used in validating wish list
 * 
 * @since 1.0
 * @return int
 */
function edd_wl_get_list_token() {
	if( ! is_user_logged_in() ) {
		$token = isset( $_COOKIE['edd_wl_token'] ) ? $_COOKIE['edd_wl_token'] : null;

		return apply_filters( 'edd_wl_get_list_token', $token );
	}
		
	return null;
}

/**
 * Get lists by specific user (author)
 *
 * @since 1.0
*/
function edd_wl_get_guest_lists( $token ) {
	
	$args = array(
	    'post_type'			=> 'edd_wish_list',
	    'posts_per_page' 	=> -1,
	    'post_status'		=> array( 'publish', 'private' ),
	    'meta_key'			=> 'edd_wl_token',
	    'meta_value'		=> $token
	);

	$lists = get_posts( $args );

	if ( $lists )
		return $lists;

	return null;
}

/**
 * When user registers and has guest lists, remove token meta key so their lists are saved indefinately
 *
 * @since 1.0
 * @param  int $user_id newly created user id
 * @return void
 */
function edd_wl_new_user_registration( $user_id ) {
	// get user's token if present
	$lists = edd_wl_get_guest_lists( edd_wl_get_list_token() );


//	var_dump( $lists ); wp_die();

	// attribute posts to new author
	if ( $lists ) {
		
		// loop throgh each list and assign the new user ID to their list
		foreach ( $lists as $key => $list ) {
			$args = array(
				'ID'          => $list->ID,
				'post_author' => $user_id
			);
			wp_update_post( $args );

			// delete token one each list
			delete_post_meta( $list->ID, 'edd_wl_token', edd_wl_get_list_token() );
		}
	}

	// remove cookie from user's computer
	setcookie( 'edd_wl_token', '', time()-3600, COOKIEPATH, COOKIE_DOMAIN );
}
add_action( 'user_register', 'edd_wl_new_user_registration', 10, 1 );
//add_action( 'wpmu_new_user', 'edd_wl_new_user_registration', 10, 1 );
