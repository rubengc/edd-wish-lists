<?php
/**
 * Template functions
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
 * Returns the URL to the EDD templates directory
 *
 * @since 1.0
 * @return string
 */
function edd_wl_get_templates_url() {
	return EDD_WL_PLUGIN_URL . 'templates';
}

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
 * Displays the total price of downloads in a wishlist
 * @param  int $list_id ID of lsit
 * @return string total
 * @uses  edd_wl_get_list_total()
 * @since  1.0
 */
function edd_wl_list_total( $list_id ) {
	// get the list total
	$total = edd_wl_get_list_total( $list_id );

	echo apply_filters( 'edd_wl_list_total', '<p>' . __( ' Total: ', 'edd-wish-lists' ) . $total . '</p>' );
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
		
		// prevent the title from changing
		if ( edd_wl_is_private_list() )
			return $title;

		if ( is_page( $view_page ) ) {
			$list_id = get_query_var( 'view' );
		}
		elseif ( is_page( $edit_page ) ) {
			$list_id = get_query_var( 'edit' );
		}

		$list_title = get_the_title( $list_id );

		// Prepend the list name to the site title.
		$title = $list_title . " $sep " . $title;
	}
	
	return $title;
}
add_filter( 'wp_title', 'edd_wl_wp_title', 10, 2 );

/**
 * Filter the page titles
 *
 * @since 1.0
*/
function edd_wl_the_title( $title, $id ) {

	// prevent the title from changing
	if ( edd_wl_is_private_list() )
		return $title;

	// View page - replace the main page title with the name of the list
	if ( get_query_var( 'view' ) && in_the_loop() && $id == get_the_ID() ) {
		$title = get_the_title( get_query_var( 'view' ) );
	}	

    return $title;
}
add_filter( 'the_title', 'edd_wl_the_title', 10, 2 );


/**
 * Handles loading of the wish list link
 * @param  int $download_id download ID
 * @return void
 * @since  1.0
 */
function edd_wl_load_wish_list_link( $download_id = '' ) {
	// set the $download_id to the post ID if $download_id is not present
	if ( ! $download_id ) {
		$download_id = get_the_ID();
	}

	$classes = array();
	// assign a class to the link depending on where it's hooked to
	// this way we can control the margin needed at the top or bottom of the link
	if( has_action( 'edd_purchase_link_end', 'edd_wl_load_wish_list_link' ) ) {
		$classes[] = 'after';
	}
	elseif( has_action( 'edd_purchase_link_top', 'edd_wl_load_wish_list_link' ) ) {
		$classes[] = 'before';
	}

	// default classes
	$classes[] = 'edd-wl-action';
	$classes[] = 'edd-wl-open-modal';

	$args = array(
		'download_id'	=> $download_id,			// available on edd_purchase_link_end, edd_purchase_link_top hooks
		'action'		=> 'edd_wl_open_modal',
		'class'			=> implode( ' ', $classes ),
		'link_size'		=> apply_filters( 'edd_wl_link_size', '' )
	);
	edd_wl_wish_list_link( $args );
}

/**
 * Possible hooks this link could be loaded on
 *
 * edd_purchase_link_end 				After standard purchase button which is used by shortcode and standard buttons. Displays within form tag. Will also show on a single page with purchase_link shortcode
 * edd_purchase_link_top (default)		Before purchase button. Will also show on a single page with purchase_link shortcode
 * edd_after_download_content 			After download content, outside of form tag
 * your_own_custom_hook					Your own hook using do_action()
*/
add_action( 'edd_purchase_link_top', 'edd_wl_load_wish_list_link' );


/**
 * The Wish list link
 *
 * @since 1.0
*/
function edd_wl_wish_list_link( $args = array() ) {
	global $edd_options, $edd_wl_scripts, $post;

	// load required scripts if template tag or shortcode has been used
	$edd_wl_scripts = true;

	$defaults = apply_filters( 'edd_wl_link_defaults', 
		array(
			'download_id' 	=> isset( $post->ID ) ? $post->ID : '',
			'text'        	=> ! empty( $edd_options[ 'edd_wl_add_to_wish_list' ] ) ? $edd_options[ 'edd_wl_add_to_wish_list' ] : sprintf( __( 'Add to %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ),
			'style'       	=> edd_get_option( 'edd_wl_button_style', 'button' ),
			'color'       	=> '',
			'class'       	=> 'edd-wl-action',
			'icon'			=> edd_get_option( 'edd_wl_icon', 'gift' ),
			'action'		=> '',
			'link'			=> '',
			'link_size'		=> '',
			'price_option'	=> '',
		) 
	);

	// merge arrays
	$args = wp_parse_args( $args, $defaults );

	// extract $args so we can use the variable names
	extract( $args, EXTR_SKIP );


	// manually select price option for shortcode
	$price_opt 				= isset( $price_option ) ? ( $price_option - 1 ) : ''; // so user can enter in 1, 2,3 instead of 0, 1, 2 as option
	$price_option 			= $price_option ? ' data-price-option="' . $price_opt . '"' : '';

	if ( ! $price_option ) {
		$variable_pricing 	= edd_has_variable_prices( $args['download_id'] );
		$data_variable  	= $variable_pricing ? ' data-variable-price=yes' : 'data-variable-price=no';
		$type             	= edd_single_price_option_mode( $args['download_id'] ) ? 'data-price-mode=multi' : 'data-price-mode=single';	
	}
	else {
		$data_variable = '';
		$type = '';
	}

	ob_start();

	$icon = $icon && 'none' != $icon ? '<i class="glyphicon glyphicon-' . $icon . '"></i>' : '';

	// shortcode parameter for returning function
	$shortcode = isset( $shortcode ) ? $shortcode : '';

	// size of plain text or button link
	$link_size = $link_size ? $link_size : '';

	// show the icon on either the left or right
	$icon_position = apply_filters( 'edd_wl_icon_position' , 'left' );

	// move the icon based on the location of the icon
	$icon_left = 'left' == $icon_position ? $icon : '';
	$icon_right = 'right' == $icon_position ? $icon : '';

	$class .= 'right' == $icon_position ? ' glyph-right' : ' glyph-left';
	//$class .= 'button' == $style ? ' button-default' : '';
	
	// change CSS class based on style chosen
	if ( 'button' == $style )
		$style = 'edd-wl-button';
	elseif ( 'plain' == $style )
		$style = 'plain';

	// if link is specified, don't show spinner
	$loading = ! $link ? '<span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span>' : '';
	$link = ! $link ? '#' : $link; 

	printf(
		'<a href="%1$s" class="%2$s %3$s" data-action="%4$s" data-download-id="%5$s" %6$s %7$s %12$s>%8$s<span class="label">%9$s</span>%10$s%11$s</a>',
		$link, 														// 1
		implode( ' ', array( $style, $color, trim( $class ) ) ), 	// 2
		$link_size, 												// 3
		$action, 													// 4
		esc_attr( $args['download_id'] ), 							// 5
		esc_attr( $data_variable ), 								// 6
		esc_attr( $type ), 											// 7
		$icon_left, 												// 8
		esc_attr( $args['text'] ),									// 9
		$loading, 													// 10
		$icon_right, 												// 11
		$price_option 												// 12
	);

	$html = apply_filters( 'edd_wl_link', ob_get_clean() );

	// return for shortcode, else echo
	if ( $shortcode ) {
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
	global $edd_wl_scripts;
	
	if ( edd_wl_is_page( 'view' ) ) {
		$edd_wl_scripts = true;
	}
	
	ob_start();

	// display messages
	echo edd_wl_print_messages();

	// get template
	edd_get_template_part( 'wish-list-' . $type );

	$template = ob_get_clean();
	return apply_filters( 'edd_wl_load_template', $template );
}

/**
 * Main Wish List function called by [edd_wish_lists] shortcode
 * This template can be found in the /templates folder. 
 * Copy wish-lists.php to your edd_templates folder in your child theme
 * Would be nice to use get_template_part but you cannot pass variables along
 *
 * @since  1.0
 * @return [type]        [description]
 */
function edd_wl_wish_list() {
	ob_start();
	echo edd_wl_print_messages();
	edd_get_template_part( 'wish-lists' );
	
	return ob_get_clean();
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

	$style = 'edd-wl-button';

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

	$button_size = '';
	//$button_size = 'button' == edd_get_option( 'edd_wl_button_style', 'button' ) ? '' : '';


	$loading = '<span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span>';
	$form_id = ! empty( $form_id ) ? $form_id : 'edd_purchase_' . $download_id;
	?>

	<form id="<?php echo $form_id; ?>" class="edd_download_purchase_form" method="post">
		<div class="edd_purchase_submit_wrapper">
		<?php 
		printf(
			'<a href="#" class="edd-add-to-cart-from-wish-list %1$s %8$s" data-action="edd_add_to_cart_from_wish_list" data-download-id="%3$s" %4$s %5$s %6$s %7$s><span class="label">%2$s</span>%9$s</a>',
			implode( ' ', array( $style, $color, trim( $class ) ) ), 	// 1
			esc_attr( $text ),											// 2
			esc_attr( $download_id ),									// 3
			esc_attr( $data_variable ),									// 4
			esc_attr( $type ),											// 5
			$button_display,											// 6
			esc_attr( $data_price_option ),								// 7
			$button_size, 												// 8
			$loading 													// 9
		);

	


		// checkout link that shows when item is added to the cart
		printf(
			'<a href="%1$s" class="%2$s %3$s %5$s" %4$s>' . $checkout_text . '</a>',
			esc_url( edd_get_checkout_uri() ),									// 1
			esc_attr( 'edd-go-to-checkout-from-wish-list' ),					// 2
			implode( ' ', array( $style, $color, trim( $class ) ) ),			// 3
			$checkout_display,													// 4
			$button_size 														// 5
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
			'style'		=> 'button',
			'color'		=> '',
			'class'		=> 'edd-wl-action edd-wl-add-all-to-cart'
		)
	);

	$args = wp_parse_args( $args, $defaults );

	extract( $args, EXTR_SKIP );

	// change CSS class based on style chosen
	if ( 'button' == $style )
		$style = 'edd-wl-button';
	elseif ( 'plain' == $style )
		$style = 'plain';

	// return if there's only 1 item in list
	$list = edd_wl_get_wish_list( $list_id );
	if ( count ( $list ) == 1 )
		return;

	$button = sprintf(
		'<a href="' . add_query_arg( array( 'edd_action' => 'wl_purchase_all', 'list_id' => $list_id ) ) . '" class="%1$s">%2$s</a>',
		implode( ' ', array( $style, $color, trim( $class ) ) ), 	// 1
		esc_attr( $text )											// 2
	);

	return $button;
}