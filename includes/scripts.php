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
function edd_wl_css() {
	wp_register_style( 'edd-wish-lists', EDD_WL_PLUGIN_URL . 'includes/css/edd-wl.css', '', EDD_WL_VERSION, 'screen' );
	wp_enqueue_style( 'edd-wish-lists' );
}
add_action( 'wp_enqueue_scripts', 'edd_wl_css', 100 );


/**
 * Print scripts
 *
 * @since 1.0
*/
function edd_wl_print_script() {
	global $edd_options;

	$edd_wish_lists = edd_wish_lists();

	if ( ! $edd_wish_lists::$add_script )
		return;
	
	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// register and enqueue
	wp_register_script( 'edd-wl', EDD_WL_PLUGIN_URL . 'includes/js/edd-wl' .  $suffix . '.js', array( 'jquery' ), EDD_WL_VERSION, true );
	wp_register_script( 'edd-wl-validate', EDD_WL_PLUGIN_URL . 'includes/js/jquery.validate' .  $suffix . '.js', array( 'jquery' ), EDD_WL_VERSION, true );
	wp_register_script( 'edd-wl-modal', EDD_WL_PLUGIN_URL . 'includes/js/modal' .  $suffix . '.js', array( 'jquery' ), EDD_WL_VERSION, true );

	wp_enqueue_script( 'edd-wl' );
	wp_enqueue_script( 'edd-wl-modal' );

	if ( edd_wl_is_page( 'view' ) )
		wp_enqueue_script( 'edd-wl-validate' );

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
 * @todo  only load when email sharing is enabled
 */
function edd_wl_validate() {
	if ( ! edd_wl_is_page( 'view' ) )
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

			console.log( 'clicked' );
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