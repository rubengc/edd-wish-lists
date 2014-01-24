<?php
/**
 * Wish List template
*/

$private 	= edd_wl_get_query( 'private' );
$public 	= edd_wl_get_query( 'public' );

?>

<?php
/**
 * Add new list button
*/
?>
<a class="button button-default edd-wl-action" href="<?php echo edd_wl_get_wish_list_create_uri(); ?>">
	<?php echo sprintf( __( 'Create new %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ); ?>
</a>

<?php
/**
 * Example link call
*/
	// $args = array(
	// 	'download_id' 	=> get_the_ID(),
	// 	'text' 			=> 'Create new list',
	// 	'icon'			=> 'add',
	// 	'style'			=> 'button',
	// 	'link'			=> edd_wl_get_wish_list_create_uri()
	// );
	// edd_wl_wish_list_link( $args );
?>

<?php
/**
 * Public lists
*/
if ( $public->have_posts() ) : ?>

	<h3><?php echo sprintf( __( 'Public %s', 'edd-wish-lists' ), edd_wl_get_label_plural() ); ?></h3>
	<ul class="edd-wish-list">
	<?php while ( $public->have_posts() ) : $public->the_post(); ?>
		<li>
			<span class="edd-wish-list-item-title">
				<a href="<?php echo edd_wl_get_wish_list_view_uri( get_the_ID() ); ?>" title="<?php echo the_title_attribute(); ?>"><?php echo get_the_title(); ?></a>
				<span class="edd-wish-list-item-count"><?php echo edd_wl_get_item_count( get_the_ID() ); ?></span>
			</span>

			<span class="edd-wish-list-edit">
				<a href="<?php echo edd_wl_get_wish_list_edit_uri( get_the_ID() ); ?>"><?php _e( 'edit', 'edd-wish-lists' ); ?></a>
			</span>
		</li>
	<?php endwhile; wp_reset_query(); ?>
	</ul>

<?php endif; ?>


<?php 
/**
 * Private lists
*/
if ( $private->have_posts() ) : ?>

	<h3><?php echo sprintf( __( 'Private %s', 'edd-wish-lists' ), edd_wl_get_label_plural() ); ?></h3>
	<ul class="edd-wish-list">
	<?php while ( $private->have_posts() ) : $private->the_post(); ?>
		<li>
			<span class="edd-wish-list-item-title">
				<a href="<?php echo edd_wl_get_wish_list_view_uri( get_the_ID() ); ?>" title="<?php echo the_title_attribute(); ?>"><?php echo get_the_title(); ?></a>
				<span class="edd-wish-list-item-count"><?php echo edd_wl_get_item_count( get_the_ID() ); ?></span>
			</span>

			<span class="edd-wish-list-edit">
				<a href="<?php echo edd_wl_get_wish_list_edit_uri( get_the_ID() ); ?>"><?php _e( 'edit', 'edd-wish-lists' ); ?></a>
			</span>
		</li>
	<?php endwhile; wp_reset_query(); ?>
	</ul>

<?php endif; ?>