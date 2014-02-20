<?php
/**
 * Scripts
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CSS
 *
 * @since 1.0
*/
function edd_wl_register_styles() {

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	$file	= 'edd-wl' . $suffix . '.css';
	$templates_dir = edd_get_theme_template_dir_name();

	$child_theme_style_sheet    = trailingslashit( get_stylesheet_directory() ) . $templates_dir . $file;

	$child_theme_style_sheet_2  = trailingslashit( get_stylesheet_directory() ) . $templates_dir . 'edd-wl.css';
	$parent_theme_style_sheet   = trailingslashit( get_template_directory()   ) . $templates_dir . $file;
	$parent_theme_style_sheet_2 = trailingslashit( get_template_directory()   ) . $templates_dir . 'edd-wl.css';
	$edd_plugin_style_sheet     = trailingslashit( edd_wl_get_templates_dir()    ) . $file;

	// Look in the child theme directory first, followed by the parent theme, followed by the EDD core templates directory
	// Also look for the min version first, followed by non minified version, even if SCRIPT_DEBUG is not enabled.
	// This allows users to copy just edd-wl.css to their theme
	if ( file_exists( $child_theme_style_sheet ) || ( ! empty( $suffix ) && ( $nonmin = file_exists( $child_theme_style_sheet_2 ) ) ) ) {
		if( ! empty( $nonmin ) )
			$url = trailingslashit( get_stylesheet_directory_uri() ) . $templates_dir . 'edd-wl.css';
		else
			$url = trailingslashit( get_stylesheet_directory_uri() ) . $templates_dir . $file;
	} elseif ( file_exists( $parent_theme_style_sheet ) || ( ! empty( $suffix ) && ( $nonmin = file_exists( $parent_theme_style_sheet_2 ) ) ) ) {
		if( ! empty( $nonmin ) )
			$url = trailingslashit( get_template_directory_uri() ) . $templates_dir . 'edd-wl.css';
		else
			$url = trailingslashit( get_template_directory_uri() ) . $templates_dir . $file;
	} elseif ( file_exists( $edd_plugin_style_sheet ) || file_exists( $edd_plugin_style_sheet ) ) {
		$url = trailingslashit( edd_wl_get_templates_url() ) . $file;
	}

	wp_enqueue_style( 'edd-wl-styles', $url, array(), EDD_WL_VERSION, 'screen' );

}
add_action( 'wp_enqueue_scripts', 'edd_wl_register_styles', 100 );


/**
 * Print scripts
 *
 * @since 1.0
*/
function edd_wl_print_script() {
	global $edd_options, $edd_wl_scripts, $edd_wl_share_via_email;

	if ( ! $edd_wl_scripts )
		return;
	
	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// register and enqueue
	wp_register_script( 'edd-wl', EDD_WL_PLUGIN_URL . 'includes/js/edd-wl' .  $suffix . '.js', array( 'jquery' ), EDD_WL_VERSION, true );
	wp_register_script( 'edd-wl-validate', EDD_WL_PLUGIN_URL . 'includes/js/jquery.validate' .  $suffix . '.js', array( 'jquery' ), EDD_WL_VERSION, true );
	wp_register_script( 'edd-wl-modal', EDD_WL_PLUGIN_URL . 'includes/js/modal' .  $suffix . '.js', array( 'jquery' ), EDD_WL_VERSION, true );

	wp_enqueue_script( 'edd-wl' );
	wp_enqueue_script( 'edd-wl-modal' );

	// load validation if email sharing is present
	if ( edd_wl_is_page( 'view' ) && $edd_wl_share_via_email ) {
		wp_enqueue_script( 'edd-wl-validate' );
	}

	wp_localize_script( 'edd-wl', 'edd_wl_scripts', array(
		 'wish_list_page'          => edd_wl_get_wish_list_uri(),
		 'wish_list_add'           => edd_wl_get_wish_list_create_uri(),
		 'ajax_nonce'              => wp_create_nonce( 'edd_wl_ajax_nonce' ),
		)
	);

}
add_action( 'wp_footer', 'edd_wl_print_script' );

/**
 * Load validation on view page
 */
function edd_wl_validate() {
	global $edd_wl_share_via_email;

	if ( ! ( edd_wl_is_page( 'view' ) && $edd_wl_share_via_email ) )
		return;

	?>
	<script>

	jQuery(document).ready(function ($) {

		var clone = $('#edd-wl-modal .modal-content').clone(); 

		// replace modal with clone when closed

		// jQuery('#edd-wl-modal').on('hidden.bs.modal', function (e) {
		// 	jQuery("#edd-wl-modal .modal-content").replaceWith( clone );
		// 	console.log('modal closed');
		// });

		$('body').on('click.eddwlShareViaEmail', '.edd-wl-share-via-email', function (e) {
			e.preventDefault();

			// submit form
			$('#edd-wl-share-email-form').submit();
		});

		// multi email validation
		jQuery.validator.addMethod(
		    "multiemail",
		    function (value, element) {
		        var email = value.split(/[;,]+/); // split element by , and ;
		        valid = true;
		        for (var i in email) {
		            value = email[i];
		            valid = valid && jQuery.validator.methods.email.call(this, $.trim(value), element);
		        }
		        return valid;
		    },
		    jQuery.validator.messages.multiemail
		);
		
		$("#edd-wl-share-email-form").validate({
			errorClass: "edd_errors",
			highlight: function(element, errorClass) {
		       $(element).removeClass(errorClass);
		   	},
		    submitHandler: function(form) {
	            console.log('form submitted');

                var $spinner        = $('.edd-wl-share-via-email').find('.edd-loading'),
                spinnerWidth    	= $spinner.width(),
                spinnerHeight       = $spinner.height(),
                submitButton		= $('.edd-wl-share-via-email');

                // Show the spinner
                submitButton.attr('data-edd-loading', '');

                $spinner.css({
                    'margin-left': spinnerWidth / -2,
                    'margin-top' : spinnerHeight / -2
                });

                var data = {
                    action:        	submitButton.data('action'),
                    post_id:        submitButton.data('post-id'),
                    from_name:    	$('input[name=edd_wl_from_name]').val(),
                    from_email:   	$('input[name=edd_wl_from_email]').val(),
                    emails:         $('input[name=edd_wl_share_emails]').val(),
                    message:        $('textarea[name=edd_wl_share_message]').val(),
                    nonce:          edd_wl_scripts.ajax_nonce,
                };

                $.ajax({
                    type:       "POST",
                    data:       data,
                    dataType:   "json",
                    url:        edd_scripts.ajaxurl,
                    success: function (response) {
                        $('.edd-wl-share-via-email').removeAttr('data-edd-loading');
                        $('a.edd-wl-share-via-email').addClass('edd-has-js');
                        $('.edd-no-js').hide();
                        
                        // clear form
                        $('input, textarea', form).val('');

                       	// replace modal contents with success contents
                        $('#edd-wl-modal .modal-content').empty().append( response.success );

                    }
                })
                .fail(function (response) {
                    console.log(response);
                })
                .done(function (response) {
                    console.log(response);
                });
	        }
		});
			
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'edd_wl_validate' );