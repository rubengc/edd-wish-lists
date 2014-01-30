<?php
/**
 * Messages
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Messages
 *
 * @since 1.0
*/
function edd_wl_messages() {
	$messages = array(
		'must-login' 		=> sprintf( __( 'Sorry, you must login to create a %s', 'edd-wish-lists' ), 		edd_wl_get_label_singular( true ) ),
		'list-updated'		=> sprintf( __( '%s successfully updated', 'edd-wish-lists' ), 						edd_wl_get_label_singular() ),
		'list-created'		=> sprintf( __( '%s successfully created', 'edd-wish-lists' ), 						edd_wl_get_label_singular() ),
		'list-deleted'		=> sprintf( __( '%s successfully deleted', 'edd-wish-lists' ), 						edd_wl_get_label_singular() ),
		'no-lists' 			=> sprintf( __( 'You currently have no %s', 'edd-wish-lists' ), 					edd_wl_get_label_plural( true ) ),
		'no-downloads' 		=> sprintf( __( 'Nothing here yet, how about adding some %s?', 'edd-wish-lists' ), 	edd_get_label_plural( true ) ),
		'lists-included'	=> __( 'This item has already been added to: ', 'edd-wish-lists' ),
	);

	return apply_filters( 'edd_wl_messages', $messages );
}

/**
 * Set various messages
 *
 * @since 1.0
 * @todo  provide better filtering of messages
*/
function edd_wl_set_messages() {
	// get array of messages
	$messages = edd_wl_messages();

	/**
	 * wish-lists.php
	*/

	// no lists
	$list_query = null != edd_wl_get_query() && edd_wl_get_query()->found_posts > 0 ? true : false;
	if ( ! $list_query && edd_wl_is_page( 'wish-lists' ) ) {
		edd_wl_set_message( 'no-lists', $messages['no-lists'] );
	}

	/**
	 * wish-list-create.php
	*/
	// must login
	if ( edd_wl_is_page( 'create' ) && ! edd_wl_allow_guest_creation() ) {
//	if ( ! edd_wl_allow_guest_creation() ) {
		edd_wl_set_message( 'must-login', $messages['must-login'] );
	}
	
	/**
	 * wish-list-view.php
	*/
	if ( edd_wl_is_page( 'view' ) ) {
		$downloads = edd_wl_get_wish_list( get_query_var( 'view' ) );

		// no downloads
		if ( empty( $downloads ) ) {
			edd_wl_set_message( 'no-downloads', $messages['no-downloads'] );
		}

		// list updated
		if ( isset( $_GET['list'] ) && $_GET['list'] == 'updated' ) {
			edd_wl_set_message( 'list-updated', $messages['list-updated'] );
		}

		// list created
		if ( isset( $_GET['list'] ) && $_GET['list'] == 'created' ) {
			edd_wl_set_message( 'list-created', $messages['list-created'] );
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
function edd_wl_print_messages() {
	$messages = edd_wl_get_messages();
	if ( $messages ) {
		$classes = apply_filters( 'edd_wl_classes', array(
			'edd_errors', 
			'edd-wl-msgs',
		) );
		echo '<div class="' . implode( ' ', $classes ) . '">';
		   foreach ( $messages as $msg_id => $msg ) {
		        echo '<p class="edd-wl-msg" id="edd-wl-msg-' . $msg_id . '">' . $msg . '</p>';
		   }
		echo '</div>';
		edd_wl_clear_messages();
	}
}

// print messages inside the modal window
add_action( 'edd_wl_modal_body', 'edd_wl_print_messages' );

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
 * @since 1.0
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