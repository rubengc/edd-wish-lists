<?php
/**
 * Forms
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Form to add wish list
 */
function edd_wl_form_list_create() {
 
 // edd_wl_set_message( 'wish-list-create-messages', __( 'This is a test', 'edd-wish-lists' ) );

  edd_wl_print_messages( 'wish-list-create-messages' );

  // prevent guests from creating list

  if ( ! edd_wl_allow_guest_creation() )
    return;



  ob_start(); ?>

  <form action="<?php echo add_query_arg( 'created', true ); ?>" class="wish-list-form" method="post">
   
    <p>
        <label for="list-title"><?php _e( 'Title:', 'edd-wish-lists' ); ?></label>
        <input type="text" name="list-title" id="list-title">
    </p>
   
    <p>
        <label for="list-description"><?php _e( 'Description:', 'edd-wish-lists' ); ?></label>
        <textarea name="list-description" id="list-description" rows="3" cols="30"></textarea>
    </p>

    <p>
      <select name="privacy">
        <option value="publish"><?php _e( 'Public - viewable by anyone', 'edd-wish-lists' ); ?></option>
        <option value="private"><?php _e( 'Private - only viewable by you', 'edd-wish-lists' ); ?></option>
      </select>
    </p>

    <p> 
        <input type="submit" value="<?php _e( 'Create List', 'edd-wish-lists' ); ?>" class="button button-default">
    </p>
    
    <input type="hidden" name="submitted" id="submitted" value="true">

    <?php wp_nonce_field( 'list_nonce', 'list_nonce_field' ); ?>

  </form>

  <?php 
    $html = ob_get_clean();
    return apply_filters( 'edd_wl_form_list_create', $html );
}

/**
 * Form to edit wish list
 *
 * @since 1.0
*/
function edd_wl_form_list_edit() {

  // check if user is allowed to edit this link
  // if ( ! edd_wl_is_users_list( get_query_var('edit') ) )
  //   return;

  $edd_wish_lists = edd_wish_lists();
  $edd_wish_lists::$add_script = true;

  $wish_list  = get_post( get_query_var('edit') ); // get wish list
  $post_id    = $wish_list->ID;
  $content    = $wish_list->post_content;
  $title      = get_the_title( $post_id );
  $privacy    = get_post_status( $post_id );

  //edd_wl_print_messages('something');

  ob_start(); ?>

  <h3><?php printf( __( '%s Settings', 'edd-wish-lists'), edd_wl_get_label_singular() ); ?></h3>

  <form action="<?php echo add_query_arg( 'updated', true ); ?>" class="wish-list-form" method="post">
   
    <p>
        <label for="list-title"><?php _e( 'Title', 'edd-wish-lists' ); ?> <span class="required">*</span></label>
        <input type="text" name="list-title" id="list-title" value="<?php echo $title; ?>">
    </p>
   
    <p>
        <label for="list-description"><?php _e( 'Description', 'edd-wish-lists' ); ?></label>
        <textarea name="list-description" id="list-description" rows="2" cols="30"><?php echo $content; ?></textarea>
    </p>

    <p>
      <select name="privacy">
        <option value="private" <?php selected( $privacy, 'private' ); ?>><?php _e( 'Private', 'edd-wish-lists' ); ?></option>
        <option value="publish" <?php selected( $privacy, 'publish' ); ?>><?php _e( 'Public', 'edd-wish-lists' ); ?></option>
      </select>
    </p>

    <p> 
        <input type="submit" value="<?php _e( 'Update', 'edd-wish-lists' ); ?>" class="button button-default">
    </p>
    
    <input type="hidden" name="submitted" id="submitted" value="true">
    <?php wp_nonce_field( 'list_nonce', 'list_nonce_field' ); ?>

  </form>


   <h3><?php printf( __( 'Delete %s', 'edd-wish-lists'), edd_wl_get_label_singular() ); ?></h3>

  <p>
  <?php /*
    <a id="edd-wish-lists-delete-list" href="<?php echo get_delete_post_link( $post_id, '', true ); ?>">
      <?php _e( 'Delete this list', 'edd-wish-lists' ); ?>
    </a>
  */ ?>
 
    <a href="#" data-action="edd_wl_delete_list" data-post-id="<?php echo $post_id; ?>" class="eddwl-delete-list"><?php _e( 'delete list', 'edd-wish-lists' ); ?></a>

  </p>

 <?php 
    $html = ob_get_clean();
    return apply_filters( 'edd_wl_edit_list', $html );
}


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