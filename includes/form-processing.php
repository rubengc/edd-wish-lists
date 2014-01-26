<?php
/**
 * Forms
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Processes the form requests
 *
 * @since 1.0
*/
function edd_wl_process_form_requests() {

  global $edd_options;

  // if not users list, redirect to homepage
  // make this into reusable function
  if ( isset( $edd_options['edd_wl_page_edit'] ) && is_page ( $edd_options['edd_wl_page_edit'] ) ) {
    if ( ! edd_wl_is_users_list( get_query_var( 'edit' ) ) ) {
      wp_redirect( site_url() ); exit;
    }
  }

 
  if ( isset( $_POST['submitted'] ) && isset( $_POST['list_nonce_field'] ) && wp_verify_nonce( $_POST['list_nonce_field'], 'list_nonce' ) ) {

    // list title
    if ( trim( $_POST['list-title'] ) === '' ) {
        $list_name_error  = edd_set_error( 'list_title_required', __( 'You need to enter a title', 'edd-wish-lists' ) );
        $has_error        = true;
    }

    // only process the form if there are no errors
    if( ! isset( $has_error ) ) {
      // edit form
      if ( isset( $_GET['created'] ) && $_GET['created'] == true ) {
        $args = array(
          'post_title'    => isset( $_POST['list-title'] ) ? wp_strip_all_tags( $_POST['list-title'] ) : '',
          'post_content'  => isset( $_POST['list-description'] ) ? $_POST['list-description'] : '',
          'post_status'   => $_POST['privacy'],
          'post_type'     => 'edd_wish_list',
        );

        $post_id = wp_insert_post( $args );

        // redirect to success page if successful
        if ( $post_id ) {
          // create token for logged user user and store against list
          edd_wl_create_token( $post_id );
          // set message
        //  edd_wl_set_message( 'list-created', __( 'Wish List successfully created', 'edd-wish-lists' ) );
          edd_wl_set_message( 'wish-list-messages', sprintf( __( '%s successfully created', 'edd-wish-lists' ), edd_wl_get_label_singular() ) );

          // redirect user to success page
          wp_redirect( edd_wl_get_wish_list_success_uri( 'created' ) ); exit;
        }
      }
      // update form
      elseif ( isset( $_GET['updated'] ) && $_GET['updated'] == true ) {
        
        $wish_list  = get_post( get_query_var('edit') ); // get wish list
        $post_id    = $wish_list->ID;

        $args = array(
          'ID'            => $post_id,
          'post_title'    => esc_attr( strip_tags( $_POST['list-title'] ) ),
          'post_content'  => esc_attr( strip_tags( $_POST['list-description'] ) ),
          'post_type'     => 'edd_wish_list',
          'post_status'   => $_POST['privacy'],
        );

        $update_list = wp_update_post( $args );

        //  redirect to success page
        if ( $update_list ) {
          // set message
          edd_wl_set_message( 'wish-list-messages', sprintf( __( '%s successfully updated', 'edd-wish-lists' ), edd_wl_get_label_singular() ) );
           // redirect user to success page
          wp_redirect( edd_wl_get_wish_list_success_uri( 'updated' ) ); exit;
        }
      } // end edit form process
    } // end has error
  }
}
add_action( 'template_redirect', 'edd_wl_process_form_requests' );