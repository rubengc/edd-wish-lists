<?php
/**
 * Template functions
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Add 'wish-list' body class to wish list page
 * @return [type] [description]
 */
function edd_wl_body_class( $classes ) {
	global $wp_query;

//	if ( edd_wl_is_wish_list() ) {
	// if ( edd_wl_is_wish_list_page() ) {
	// 	$classes[] = apply_filters( 'edd_wl_body_class', 'eddwl-page' );
	// }

	// if ( isset( $wp_query->query_vars['view'] ) && '' != $wp_query->query_vars['view'] ) {
	// 	$classes[] = 'wish-list-view';
	// }

	$view = edd_wl_get_page_slug( 'view' );
//	$edit = edd_wl_get_page_slug( 'edit' );

	// class for view wish list
	// if ( isset( $wp_query->query_vars[ $view ] ) && '' != $wp_query->query_vars[ $view ] ) {
	// 	$classes[] = 'wish-list-view';
	// }

		// //	add wishlist
		// elseif ( isset( $wp_query->query_vars['add'] ) ) {
		// 	$this->get_template_part( 'wish-list-add' );
		// }
		// // show wishlists
		// else {
		// 	$this->get_template_part( 'wish-lists' );
		// }



	return $classes;
}
// add body class
//add_filter( 'body_class', 'edd_wl_body_class' );

/**
 * Get number of items in list
 *
 * @since 1.0
*/
function edd_wl_get_item_count( $list_id ) {
	$items = get_post_meta( $list_id, 'edd_wish_list', true );
	$count = ! empty ( $items ) ? '(' . count ( $items ) . ')' : '(0)';

	return $count;
}


/**
 * Handles loading of the wish list link
 *
 * @since 1.0
*/
function edd_wl_load_wish_list_link() {
	// return if page has [downloads] shortcode
	// try and fix this
	// if ( edd_wl_has_shortcode( 'downloads' ) )
	// 	return;



	//add_filter( 'edd_purchase_download_form', 'edd_wl_wish_list_link' );

	
		

	$args = array(
		'action'		=> 'edd_wl_open_modal',
		'class'			=> 'edd-add-to-wish-list edd-wl-action'
	);
	edd_wl_wish_list_link( $args );		

}
//add_action( 'template_redirect', 'edd_wl_load_wish_list_link' );

// after standard purchase button (used by shortcode and standard buttons) (displays within form tag). Will also show on a single page with purchase_link shortcode
//add_action( 'edd_purchase_link_end', 'edd_wl_load_wish_list_link' );

// before purchase button. Will also show on a single page with purchase_link shortcode
//add_action( 'edd_purchase_link_top', 'edd_wl_load_wish_list_link' );

// after download content (outside of form tag)
add_action( 'edd_after_download_content', 'edd_wl_load_wish_list_link' );

// or custom hook
//add_action( 'edd_wl_test_hook', 'edd_wl_wish_list_link' );


/**
 * loads Modal window on single download page
 *
 * @since 1.0
 * @todo  may load differently
*/
// function edd_wl_load_modal() {
// 	echo edd_wl_modal();
// }
//add_action( 'edd_after_download_content', 'edd_wl_load_modal' );

// load in download grid
//add_action( 'edd_wl_test_hook', 'edd_wl_wish_list_link' );





/**
 * The Wish list link
 * Used by ...
 *
 * @since 1.0
*/

function edd_wl_wish_list_link( $args = array() ) {
	global $edd_options, $post;

	// get main wish list class
	$edd_wish_lists = edd_wish_lists();

	// load required scripts if template tag or shortcode has been used
	$edd_wish_lists::$add_script = true;


	$defaults = apply_filters( 'edd_wl_link_defaults', 
		array(
			'download_id' 	=> isset( $post->ID ) ? $post->ID : '',
			'text'        	=> ! empty( $edd_options[ 'edd_wl_add_to_wish_list' ] ) ? $edd_options[ 'edd_wl_add_to_wish_list' ] : __( 'Add To Wish List', 'edd-wish-lists' ),
			'style'       	=> edd_get_option( 'edd_wl_button_style', 'plain' ),
			'color'       	=> '',
			'class'       	=> 'edd-wl-action',
			'icon'			=> edd_get_option( 'edd_wl_icon', 'gift' ),
			'action'		=> '',
			'link'			=> ''
		) 
	);



	// merge arrays
	$args = wp_parse_args( $args, $defaults );

	//	var_dump( $download_id );

	// extract $args so we can use the variable names
	extract( $args, EXTR_SKIP );
	
	// if ( edd_wl_has_shortcode( 'downloads' ) )
	// 	return;

	// prevent pages from showing add to wish list button and also being added to wishlist instead of real download id
	if ( is_page() && edd_wl_has_shortcode( 'downloads' ) && $edd_wish_lists::$shortcode )
		return;	
	
	// $edd_wish_lists::$shortcode && 
	// if is page

	$variable_pricing 	= edd_has_variable_prices( $args['download_id'] );
	$data_variable  	= $variable_pricing ? ' data-variable-price=yes' : 'data-variable-price=no';
	$type             	= edd_single_price_option_mode( $args['download_id'] ) ? 'data-price-mode=multi' : 'data-price-mode=single';

	ob_start();
?>
	
<?php /*
<a class="edd-cart-saving-button edd-submit button<?php echo ' ' . $color; ?>" id="edd-restore-cart-button" href="<?php echo add_query_arg( array( 'edd_action' =>'view_wish_list', 'download_id' => get_the_ID() ) ) ?>"><?php _e( 'View Wish List', 'edd' ); ?></a>
*/ ?>

<?php /*
<a class="edd-add-to-wish-list edd-submit button<?php echo ' ' . $color; ?>" id="edd-save-cart-button" href="<?php echo add_query_arg( array( 'edd_action' =>'add_wish_list', 'download_id' => get_the_ID() ) ) ?>"><?php _e( 'Add To Wish List', 'edd' ); ?></a>
*/ ?>

<?php 


	$icon = $icon && 'none' != $icon ? '<i class="glyphicon glyphicon-' . $icon . '"></i>' : '';

	$button_size = 'button' == edd_get_option( 'edd_wl_button_style', 'plain' ) ?  apply_filters( 'edd_wl_button_size', 'button-default' ) : '';	
	?>


	<?php

	// show the icon on either the left or right
	$icon_position = apply_filters( 'edd_wl_icon_position' , 'left' );

	// move the icon based on the location of the icon
	$icon_left = 'left' == $icon_position ? $icon : '';
	$icon_right = 'right' == $icon_position ? $icon : '';

	$class .= 'right' == $icon_position ? ' glyph-right' : ' glyph-left';

	// if link is specified, don't show spinner
	$loading = ! $link ? '<span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span>' : '';
	$link = ! $link ? '#' : $link; 

	//if ( edd_is_ajax_enabled() ) {
		printf(
			'<a href="%1$s" class="%2$s %3$s" data-action="%4$s" data-download-id="%5$s" %6$s %7$s>%8$s<span class="edd-add-to-wish-list-label">%9$s</span>%10$s%11$s</a>',
			$link, 														// 1
			implode( ' ', array( $style, $color, trim( $class ) ) ), 	// 2
			$button_size, 												// 3
			$action, 													// 4
			esc_attr( $args['download_id'] ), 							// 5
			esc_attr( $data_variable ), 								// 6
			esc_attr( $type ), 											// 7
			$icon_left, 												// 8
			esc_attr( $args['text'] ),									// 9
			$loading, 													// 10
			$icon_right 												// 11	
		);	
	//}

	// this one is only shown when there is no JS
	
	// printf(
	// 	'<input type="submit" class="edd-add-to-wish-list edd-no-js %1$s" name="edd_purchase_download" value="%2$s" data-action="edd_add_to_wish_list" data-download-id="%3$s" %4$s %5$s/>',
	// 	implode( ' ', array( $args['style'], $args['color'], trim( $args['class'] ) ) ),
	// 	esc_attr( $args['text'] ),
	// 	esc_attr( $args['download_id'] ),
	// 	esc_attr( $data_variable ),
	// 	esc_attr( $type )
	// );


	$html = apply_filters( 'edd_wl_link', ob_get_clean() );

	// return for shortcode, else echo
	if ( $edd_wish_lists::$shortcode ) {
		return $html;
	}
	else {
		echo $html;
	}
}

/**
 * Load the create wishlist form
 *
 * @since 1.0
*/
function edd_wl_load_template( $type ) {
	ob_start();

	edd_wl_print_messages( 'wish-list-' . $type );

	edd_wl_get_template_part( 'wish-list-' . $type );

	$template = ob_get_clean();
	return apply_filters( 'edd_wl_load_template', $template );
}

/**
 * Main Wish List function called by shortcode
 * This template can be found in the /templates folder. 
 * Copy wish-list.php to your edd_templates folder in your child theme
 * Would be nice to use get_template_part but you cannot pass variables along
 *
 * @since  1.0
 * @return [type]        [description]
 */
function edd_wl_wish_list() {
	// load required scripts if template tag or shortcode has been used
	$edd_wish_lists = edd_wish_lists();
	$edd_wish_lists::$add_script = true;

	edd_wl_print_messages( 'wish-list-messages' );

	$private 	= edd_wl_get_query( 'private' );
	$public 	= edd_wl_get_query( 'public' );

	// return if no query. Does a bit of security in there also
	if ( ! ( $private || $public ) ) {
		return;
	}

	ob_start();

	edd_wl_get_template_part( 'wish-lists' );
	
	$html = ob_get_clean();
	return apply_filters( 'edd_wl_wish_list', $html );
}

/**
 * Wish list item purchase link
 * @param  [type] $item [description]
 * @return [type]       [description]
 */
function edd_wl_wish_list_item_purchase( $item, $args = array() ) {
	global $edd_options;

	ob_start();
	
	$defaults = apply_filters( 'edd_wl_add_to_cart_defaults', array(
		'download_id' 	=> $item['id'],
		'text'        	=> ! empty( $edd_options[ 'edd_wl_add_to_cart' ] ) ? $edd_options[ 'edd_wl_add_to_cart' ] : __( 'Add to cart', 'edd-wish-lists' ),
		'checkout_text' => __( 'Checkout', 'edd-wish-lists' ),
		'style'       	=> edd_get_option( 'edd_wl_button_style', 'button' ),
		'color'       	=> '',
		'class'       	=> 'edd-wl-action'
	) );

	$args = wp_parse_args( $args, $defaults );

	extract( $args, EXTR_SKIP );

	$variable_pricing 	= edd_has_variable_prices( $download_id );
	$data_variable  	= $variable_pricing ? ' data-variable-price=yes' : 'data-variable-price=no';

	// price option
	$data_price_option  = $variable_pricing ? ' data-price-option=' . $item['options']['price_id'] : '';

	$type             	= edd_single_price_option_mode( $download_id ) ? 'data-price-mode=multi' : 'data-price-mode=single';

	if ( edd_item_in_cart( $download_id ) && ! $variable_pricing ) {
		// hide the 'add to cart' link
		$button_display   = 'style="display:none;"';
		// show the 'checkout' link
		$checkout_display = '';
	}
	// if the variable priced download is in cart, show 'checkout'
	elseif( $variable_pricing &&  edd_item_in_cart( $download_id, array( 'price_id' => $item['options']['price_id'] ) ) ) {
		// hide the 'add to cart' link
		$button_display   = 'style="display:none;"';
		// show the 'checkout' link
		$checkout_display = '';
	}
	else {
		// show the 'add to cart' link
		$button_display   = '';
		// hide the 'checkout' link
		$checkout_display = 'style="display:none;"';
	}

	$button_size = 'button' == edd_get_option( 'edd_wl_button_style', 'plain' ) ? apply_filters( 'edd_wl_button_size', 'button-default' ) : '';

	$form_id = ! empty( $form_id ) ? $form_id : 'edd_purchase_' . $download_id;
	?>

	<form id="<?php echo $form_id; ?>" class="edd_download_purchase_form" method="post">
	<div class="edd_purchase_submit_wrapper">
	<?php 
	printf(
		'<a href="#" class="edd-add-to-cart-from-wish-list %1$s %8$s" data-action="edd_add_to_cart_from_wish_list" data-download-id="%3$s" %4$s %5$s %6$s %7$s><span class="edd-add-to-cart-label">%2$s</span></a>',
		implode( ' ', array( $style, $color, trim( $class ) ) ), 	// 1
		esc_attr( $text ),											// 2
		esc_attr( $download_id ),									// 3
		esc_attr( $data_variable ),									// 4
		esc_attr( $type ),											// 5
		$button_display,											// 6
		esc_attr( $data_price_option ),								// 7
		$button_size 												// 8
	);

	// checkout link that shows when item is added to the cart
	printf(
		'<a href="%1$s" class="%2$s %3$s" %4$s>' . $checkout_text . '</a>',
		esc_url( edd_get_checkout_uri() ),
		esc_attr( 'edd-go-to-checkout-from-wish-list' ),
		implode( ' ', array( $style, $color, trim( $class ) ) ),
		$checkout_display
	);

	?>
	</div>
	</form>
	
<?php 
	$html = ob_get_clean();
	return apply_filters( 'edd_wl_item_purchase', $html );
}

/**
 * Purchase all items in wish list
 * @param  [type] $item [description]
 * @return [type]       [description]
 */
function edd_wl_add_all_to_cart_link( $args = array() ) {
	$defaults = apply_filters( 'edd_wl_add_all_to_cart_link_defaults', 
		array(
			'list_id'	=> $args['list_id'],
			'text' 		=> __( 'Add all to cart', 'edd-wish-lists' ),
			'style'		=> edd_get_option( 'edd_wl_button_style', 'button' ),
			'color'		=> '',
			'class'		=> 'edd-wl-action'
		)
	);

	$args = wp_parse_args( $args, $defaults );

	extract( $args, EXTR_SKIP );

	$button_size = 'button' == edd_get_option( 'edd_wl_button_style', 'plain' ) ?  apply_filters( 'edd_wl_button_size', 'button-default' ) : '';

	// return if there's only 1 item in list
	//if ( edd_wl_get_list( $list_id ) )
	$list = edd_wl_get_wish_list( $list_id );
	if ( count ( $list ) == 1 )
		return;

	printf(
		'<a href="' . add_query_arg( array( 'edd_action' => 'wl_purchase_all', 'list_id' => $list_id ) ) . '" class="%1$s %3$s">%2$s</a>',
		implode( ' ', array( $style, $color, trim( $class ) ) ),
		esc_attr( $text ),
		$button_size
	);
}

/**
 * Load skeleton for modal window in the footer
 *
 * @since 1.0
*/
function edd_wl_modal_window() {
	?>
	<div class="modal fade" id="edd-wl-modal" tabindex="-1" role="dialog" aria-labelledby="edd-wl-modal-label" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content"></div>
	  </div>
	</div>
	<?php
}
add_action( 'wp_footer', 'edd_wl_modal_window', 100 );


/**
 * Get lists for post ID
 *
 * @since 1.0
*/
function edd_wl_get_wish_lists( $download_id, $price_ids, $items ) {
	ob_start();

	global $edd_options;
	$text = ! empty( $edd_options[ 'edd_wl_add_to_wish_list' ] ) ? $edd_options[ 'edd_wl_add_to_wish_list' ] : __( 'Add To Wish List', 'edd-wish-lists' );
?>

<div class="modal-header">

	<h2 id="edd-wl-modal-label">
		<?php echo esc_attr( $text ); ?>
	</h2>

	<a class="edd-wl-close" href="#" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i><span class="hide-text"><?php _e( 'Close', 'edd-wish-lists' ); ?></span></a>
	
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
		        printf( '%1$s%2$s', $download, $options );
	        ?>

</div>

<div class="modal-body">

 	
        

	<?php if ( ! edd_wl_allow_guest_creation() ) : ?>


		<?php echo '<p>' . apply_filters( 'edd_wl_no_guests', sprintf( __( 'Sorry, you must be logged in to create a %s', 'edd-wish-lists' ), edd_wl_get_label_singular() ) ) . '</p>'; ?>

	<?php else : ?>

		
		
		

		

		
		<?php
			// get users public lists
			$private  = edd_wl_get_query( 'private' );
		  	$public   = edd_wl_get_query( 'public' );
			
			$variable_pricing   = edd_has_variable_prices( $download_id );
			$data_variable      = $variable_pricing ? ' data-variable-price=yes' : 'data-variable-price=no';
			$type               = edd_single_price_option_mode( $download_id ) ? 'data-price-mode=multi' : 'data-price-mode=single';
		?>

		<form method="post" action="" class="form-modal">
		      
			<?php if ( edd_wl_get_query()->found_posts > 0 ) : // check that user has wish lists ?>
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

					<?php 
						// printf(
						// '<a href="#" class="edd-submit button edd-wish-list-save" data-action="edd_add_to_wish_list" data-download-id="%1$s" %2$s %3$s><span class="edd-add-to-wish-list-label">Save</span><span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span></a>',
						// esc_attr( $download_id ),
						// esc_attr( $data_variable ),
						// esc_attr( $type )
						// );
					?>

					

					<?php
		    //           if ( edd_is_ajax_enabled() ) {
						// printf(
						// 	'<a href="#" class="edd-add-to-wish-list %1$s" data-action="edd_wl_open_modal" data-download-id="%3$s" %4$s %5$s %6$s>%7$s<span class="edd-add-to-wish-list-label">%2$s</span><span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span></a>',
						// 	implode( ' ', array( $args['style'], $args['color'], trim( $args['class'] ) ) ),
						// 	esc_attr( $args['text'] ),
						// 	esc_attr( $args['download_id'] ),
						// 	esc_attr( $data_variable ),
						// 	esc_attr( $type ),
						// 	$add_to_wish_list_display,
						// 	$icon
						// );	
						// }

					?>

		              </div>

		         <?php
		         	// add a hidden input field for each price ID which our next ajax function will grab
		         	foreach ( $price_ids as $id ) { ?>
		         		<input name="edd-wish-lists-post-id" type="hidden" value="<?php echo $id; ?>">
		         	<?php }
		         ?>     

		         <div class="modal-footer">
        			
        				<?php
        				/**
        				 * @todo  make text filterable
        				 */
        						
        			//	echo $download_id;
        					$args = array(
        						'download_id' 	=> $download_id,
        						'text' 			=> __( 'Save', 'edd-wish-lists' ),
        						'icon'			=> '',
        						'action'		=> 'edd_add_to_wish_list',
        						'class'			=> 'edd-wish-list-save edd-wl-action'
        					);
        					edd_wl_wish_list_link( $args );
        				?>

      				</div>

		            </form>
	<?php endif; ?>
 	
  </div>
	<?php
	//$items = get_post_meta( $download_id, 'edd_wish_list', true );

	//$count = edd_wl_get_query()->found_posts;

	$html = ob_get_clean();
	return apply_filters( 'edd_wl_get_wish_lists', $html );
}