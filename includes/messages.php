<?php
/**
 * Messages
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Set various messages
 *
 * @since 1.0
 * @todo  provide better filtering
*/
function edd_wl_set_messages() {
	global $edd_options;
	$token = edd_wl_get_list_token();

	// no lists
	if ( $token ) {
		$lists = edd_wl_get_guest_lists( $token );

		// no lists
		if ( ! $lists ) {
			edd_wl_set_message( 'wish-list-messages', sprintf( __( 'You don\'t have any %s, why not create one?', 'edd-wish-lists' ), edd_wl_get_label_plural( true ) ) );
		}
	}

	// must login to create list
	if ( ! edd_wl_allow_guest_creation() ) {
		edd_wl_set_message( 'wish-list-create-messages', sprintf( __( 'Sorry, you must login to create a %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) );
	}

	// no products in list
	$wish_list_view_id = isset( $edd_options['edd_wl_page_view'] ) ? $edd_options['edd_wl_page_view'] : false;
	if ( is_page( $wish_list_view_id ) ) {
		
		$downloads = edd_wl_get_wish_list( get_query_var( 'view' ) );

		if ( ! $downloads ) {
			edd_wl_set_message( 'wish-list-view', sprintf( __( 'Nothing here yet, how about adding some %s?', 'edd-wish-lists' ), edd_get_label_plural( true ) ) );
		}
	}
}
add_action( 'template_redirect', 'edd_wl_set_messages' );


/**
 * Print Messages
 *
 * Prints all stored messages.
 * If messages exist, they are returned.
 *
 * @since 1.0
 * @uses edd_wl_get_messages()
 * @uses edd_wl_clear_errors()
 * @return void
 */
function edd_wl_print_messages( $message_id = '' ) {
	$msgs = edd_wl_get_messages();

	$message_to_show = $msgs && array_key_exists( $message_id, $msgs ) ? $msgs[ $message_id ] : '';

	if ( $message_to_show ) {

		$classes = apply_filters( 'edd_wl_msg_class', 
			array(
				'edd-wl-msgs'
			)
		);

		echo '<div class="' . implode( ' ', $classes ) . '">';
		        echo '<p class="edd-wl-msg">' . $msgs[ $message_id ] . '</p>';
		echo '</div>';

		edd_wl_clear_messages();
	}
}


/**
 * Get Messages
 *
 * Retrieves all messages stored
 *
 * @since 1.0
 * @uses EDD_Session::get()
 * @return mixed array if errors are present, false if none found
 */
function edd_wl_get_messages() {
	return EDD()->session->get( 'edd_wl_messages' );
}

/**
 * Set Message
 *
 * Stores a message  in a session var.
 *
 * @since 1.0
 * @uses EDD_Session::get()
 * @param int $msg_id ID of the message being set
 * @param string $message Message to store
 * @return void
 */
function edd_wl_set_message( $msg_id, $message ) {
	$msgs = edd_wl_get_messages();

	if ( ! $msgs ) {
		$msgs = array();
	}

	$msgs[ $msg_id ] = $message;
	EDD()->session->set( 'edd_wl_messages', $msgs );
}

/**
 * Clears all stored messages.
 *
 * @since 1.0
 * @uses EDD_Session::set()
 * @return void
 */
function edd_wl_clear_messages() {
	EDD()->session->set( 'edd_wl_messages', null );
}

/**
 * Removes (unsets) a stored message
 *
 * @since 1.3.4
 * @uses EDD_Session::set()
 * @param int $msg_id ID of the error being set
 * @return void
 */
function edd_wl_unset_message( $msg_id ) {
	$msgs = edd_wl_get_messages();
	if ( $msgs ) {
		unset( $msgs[ $msg_id ] );
		EDD()->session->set( 'edd_wl_messages', $msgs );
	}
}