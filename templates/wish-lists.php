<?php
/**
 * Wish List template
*/

$private 	= edd_wl_get_query( 'private' );
$public 	= edd_wl_get_query( 'public' );
?>

<?php // create list
	echo edd_wl_create_list_link( array( 'class' => 'testing', 'wrapper_class' => 'wrappp', 'text' => sprintf( __( 'Create new %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) ) );
?>

<?php 
/**
 * Private lists
*/
if ( $private ) : ?>

	<h3><?php echo sprintf( __( 'Private %s', 'edd-wish-lists' ), edd_wl_get_label_plural() ); ?></h3>
	<ul class="edd-wish-list">

	<?php foreach ( $private as $id ) : ?>
		<li>
			<span class="edd-wl-item-title">
				<a href="<?php echo edd_wl_get_wish_list_view_uri( $id ); ?>" title="<?php echo the_title_attribute( array('post' => $id ) ); ?>"><?php echo get_the_title( $id ); ?></a>
				<span class="edd-wl-item-count"><?php echo edd_wl_get_item_count( $id ); ?></span>
			</span>

			<?php // edit link
				echo edd_wl_edit_link( $id, array( 'class' => 'testing', 'wrapper_class' => 'wrappp', 'text' => __( 'edit', 'edd-wish-lists' ) ) );
			?>
		</li>
	<?php endforeach; ?>
	
	</ul>

<?php endif; // if private lists ?>


<?php 
/**
 * Public lists
*/
if ( $public ) : ?>

	<h3><?php echo sprintf( __( 'Public %s', 'edd-wish-lists' ), edd_wl_get_label_plural() ); ?></h3>
	<ul class="edd-wish-list">

	<?php foreach ( $public as $id ) : ?>
		<li>
			<span class="edd-wl-item-title">
				<a href="<?php echo edd_wl_get_wish_list_view_uri( $id ); ?>" title="<?php echo the_title_attribute( array('post' => $id ) ); ?>"><?php echo get_the_title( $id ); ?></a>
				<span class="edd-wl-item-count"><?php echo edd_wl_get_item_count( $id ); ?></span>
			</span>

			<?php // edit link
				echo edd_wl_edit_link( $id, array( 'class' => 'testing', 'wrapper_class' => 'wrappp', 'text' => __( 'edit', 'edd-wish-lists' ) ) );
			?>
		</li>
	<?php endforeach; ?>
	
	</ul>

<?php endif; // if public lists ?>