<?php
/**
 * Wish List template
 *
 * 
 * @since 1.0
*/

$list_id = get_query_var( 'view' );

// gets the list
$downloads = edd_wl_get_wish_list( $list_id );

// get list post object
$list = get_post( $list_id );
// title
$title = get_the_title( $list_id );
//status
$privacy = get_post_status( $list_id );

?>

<?php if ( $downloads ) : ?>

	<h2><?php echo $list->post_title; ?></h2>
	<p><?php echo $list->post_content; ?></p>

	<?php 
		/**
		 * All all items in list to cart
		*/
		echo edd_wl_add_all_to_cart_link( array( 'list_id' => $list_id ) );
	?>

	<ul class="edd-wish-list">
		<?php foreach ( $downloads as $key => $item ) : ?>
			<li>
				<span class="edd-wish-list-item-title">
				<?php
					$item_option 		= ! empty( $item['options'] ) ? '<span class="edd-wish-list-item-title-option">' . edd_get_cart_item_price_name( $item ) . '</span>' : '';
					$variable_pricing 	= edd_has_variable_prices( $item['id'] );
				?>
					<a href="<?php echo post_permalink( $item['id'] ); ?>" title="<?php echo the_title_attribute( array( 'post' => $item['id'] ) ); ?>">
						<?php echo get_the_title( $item['id'] ); ?>
					</a>
					<?php echo $item_option; ?>
				</span>

				<span class="edd-wish-list-item-image">
				<?php if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( $item['id'] ) ) : ?>
					<?php echo get_the_post_thumbnail( $item['id'], apply_filters( 'edd_checkout_image_size', array( 50, 50 ) ) ); ?>
				<?php endif; ?>
				</span>

				<span class="edd-wish-list-item-price">
					<?php echo edd_cart_item_price( $item['id'], $item['options'] ); ?>
				</span>

				<span class="edd-wish-list-item-purchase">
					<?php echo edd_wl_wish_list_item_purchase( $item ); ?>
				</span>
				
				<?php if ( edd_wl_is_users_list( $list_id ) ) : ?>
				<span class="edd-wish-list-item-remove">
					<a title="<?php _e( 'Remove', 'edd-wish-lists' ); ?>" href="#" data-cart-item="<?php echo $key; ?>" data-download-id="<?php echo $item['id']; ?>" data-list-id="<?php echo $list_id; ?>" data-action="edd_remove_from_wish_list" class="edd-remove-from-wish-list">
					<i class="glyphicon glyphicon-remove"></i>
					<span class="hide-text"><?php _e( 'Remove', 'edd-wish-lists' ); ?></span>
					</a>
				</span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>



	<?php 
	/**
	 * Sharing - only shown for public lists
	*/
	if ( 'private' !== get_post_status( $list_id ) ) : ?>
		<h3>
			<?php _e( 'Share', 'edd-wish-lists' ); ?>
		</h3>
		<p>
			<?php echo wp_get_shortlink( $list_id ); ?>
		</p>
		<?php echo edd_wl_sharing_services(); ?>
	<?php endif; ?>

<?php endif; ?>