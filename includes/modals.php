<?php
/**
 * Modal dialogs
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Load skeleton for modal window in the footer
 *
 * @since 1.0
*/
function edd_wl_modal_window() {
	?>
	<div class="modal fade" id="edd-wl-modal" tabindex="-1" role="dialog" aria-labelledby="edd-wl-modal-label" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	    	<?php do_action( 'edd_wl_modal_content' ); ?>
	    </div>
	  </div>
	</div>
	<?php
}
add_action( 'wp_footer', 'edd_wl_modal_window', 100 );

/**
 * Confirm delete modal for edit wish list page
 *
 * @since 1.0
*/
function edd_wl_list_delete_confirm() { 
	// only load on edit page
	if ( ! edd_wl_is_page( 'edit' ) )
		return;
	?>
	<div class="modal-header">
		<h2 id="edd-wl-modal-label">
			<?php printf( __( 'Delete %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ); ?>
		</h2>
		<a class="edd-wl-close" href="#" data-dismiss="modal">
			<i class="glyphicon glyphicon-remove"></i>
			<span class="hide-text"><?php _e( 'Close', 'edd-wish-lists' ); ?></span>
		</a>
	</div>
	<div class="modal-body">
		<p>
			<?php printf( __( 'You are about to delete this %s, are you sure?', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ); ?>
		</p>
	</div>
	<div class="modal-footer">
		<a href="#" data-action="edd_wl_delete_list" data-post-id="<?php echo get_query_var( 'edit' ); ?>" class="button button-default edd-wl-action eddwl-delete-list-confirm">
			<span class="label"><?php printf( __( 'Yes, delete this %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ); ?></span>
			<span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span>
		</a>
	</div>
<?php }
add_action( 'edd_wl_modal_content', 'edd_wl_list_delete_confirm' );

/**
 * Get lists for post ID
 *
 * @since 1.0
*/
function edd_wl_get_wish_lists( $download_id, $price_ids, $items, $price_option_single ) {
	ob_start();

	global $edd_options;
	$text = ! empty( $edd_options[ 'edd_wl_add_to_wish_list' ] ) ? $edd_options[ 'edd_wl_add_to_wish_list' ] : __( 'Add To Wish List', 'edd-wish-lists' );
?>

<div class="modal-header">

	<h2 id="edd-wl-modal-label">
		<?php echo esc_attr( $text ); ?>
	</h2>

   <?php
        $download = $download_id ? get_the_title( $download_id ) : '';

        // price variations
        if ( edd_has_variable_prices( $download_id ) ) {
            $options = ' - ' . implode( ', ', array_map( function ( $item ) {
				  return edd_get_price_name( $item['id'], $item['options'] );
			}, $items ) );
    	}

    	$options = isset( $options ) ? $options : '';

        // show user what they have selected
        echo '<p>' . sprintf( '%1$s%2$s', $download, $options ) . '</p>';
    ?>

	<a class="edd-wl-close" href="#" data-dismiss="modal">
		<i class="glyphicon glyphicon-remove"></i>
		<span class="hide-text"><?php _e( 'Close', 'edd-wish-lists' ); ?></span>
	</a>
	
</div>

<div class="modal-body">

	<?php if ( ! edd_wl_allow_guest_creation() ) : ?>
		<?php
			$messages = edd_wl_messages();
			echo '<p>' . $messages['must-login'] . '</p>'; 
		?>

	<?php else : ?>
		
		<?php
			// get users public lists
			$private  = edd_wl_get_query( 'private' );
		  	$public   = edd_wl_get_query( 'public' );
				
			$list_query = null != edd_wl_get_query() && edd_wl_get_query()->found_posts > 0 ? true : false;

			$variable_pricing   = edd_has_variable_prices( $download_id );
			$data_variable      = $variable_pricing ? ' data-variable-price=yes' : 'data-variable-price=no';
			$type               = edd_single_price_option_mode( $download_id ) ? 'data-price-mode=multi' : 'data-price-mode=single';
		?>

		<form method="post" action="" class="form-modal">
		      
			<?php if ( $list_query ) : ?>
		            <p id="current_lists">
		            <input type="radio" checked="" id="existing-list" value="existing-list" name="list-options">
		            <label for="existing-list"><?php _e( 'Add to existing', 'edd-wish-lists' ); ?></label>

		              <select id="user-lists" name="user-lists">
		            	
		            	<?php
		            	/**
		            	 * Public lists
		            	*/
		            	if ( $public->have_posts() ) : ?>
		            	  <optgroup label="Public">
		            	 
		            	  <?php while ( $public->have_posts() ) : $public->the_post(); ?>
		            	    <?php
		            	      $items = get_post_meta( get_the_ID(), 'edd_wish_list', true );
		            	    ?>
		            	    <option value="<?php echo get_the_ID(); ?>"><?php echo get_the_title() . ' ' . edd_wl_get_item_count( get_the_ID() ); ?></option>  
		            	  <?php endwhile; wp_reset_query(); ?>
		            	  
		            	   </optgroup>
		            	<?php endif; ?>

		               <?php
		              /**
		               * Private lists
		              */
		              if ( $private->have_posts() ) : ?>
		                <optgroup label="Private">
		               
		                <?php while ( $private->have_posts() ) : $private->the_post(); ?>
		                  <?php
		                    $items = get_post_meta( get_the_ID(), 'edd_wish_list', true );
		                  ?>
		                  <option value="<?php echo get_the_ID(); ?>"><?php echo get_the_title() . ' ' . edd_wl_get_item_count( get_the_ID() ); ?></option>  
		                <?php endwhile; wp_reset_query(); ?>
		                
		                 </optgroup>
		              <?php endif; ?>


		              </select>

		            </p>

		    <?php endif; ?>

		             <p>
						<input type="radio" id="new-list" value="new-list" name="list-options">
						<label for="new-list"><?php _e( 'Add to new', 'edd-wish-lists' ); ?></label>

						<input type="text" id="list-name" name="list-name" placeholder="<?php _e( 'Title', 'edd-wish-lists' ); ?>">

						<select id="list-status" name="list-status">
							<option value="private"><?php _e( 'Private - only viewable by you', 'edd-wish-lists' ); ?></option>
							<option value="publish"><?php _e( 'Public - viewable by anyone', 'edd-wish-lists' ); ?></option>
						</select>
		            </p>

		              </div>

		         <?php
		         	// add a hidden input field for each price ID which our next ajax function will grab
		         	foreach ( $price_ids as $id ) { ?>
		         		<input name="edd-wish-lists-post-id" type="hidden" value="<?php echo $id; ?>">
		         	<?php }
		         ?>     

		        
		         <?php if ( $price_option_single ) : ?>
		         <input name="edd-wl-single-price-option" type="hidden" value="yes">
		     	<?php endif; ?>

		         <div class="modal-footer"> 			
        				<?php
        					$args = array(
        						'download_id' 	=> $download_id,
        						'text' 			=> __( 'Save', 'edd-wish-lists' ),
        						'icon'			=> '',
        						'action'		=> 'edd_add_to_wish_list',
        						'class'			=> 'edd-wish-list-save edd-wl-action',
        						'style'			=> 'button',
        					);
        					edd_wl_wish_list_link( $args );
        				?>

        				<a class="button button-default edd-wl-success edd-wl-action" href="#" data-dismiss="modal" style="display:none;">
        				<?php _e( 'Great, I\'m done', 'edd-wish-lists' ); ?>
        				</a>
	
      				</div>

		            </form>
	<?php endif; ?>
 	
  </div>
	<?php

	$html = ob_get_clean();
	return apply_filters( 'edd_wl_get_wish_lists', $html );
}