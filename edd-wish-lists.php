<?php
/*
Plugin Name: Easy Digital Downloads - Wish Lists
Plugin URI: https://easydigitaldownloads.com/extensions/edd-wish-lists/
Description: Allow your customers to create a wish list
Version: 1.0
Author: Andrew Munro, Sumobi
Author URI: http://sumobi.com/
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Wish_Lists' ) ) :

	final class EDD_Wish_Lists {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of EDD Wish Lists exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * Holds the required scripts for the plugin
		 *
		 * @since 1.0
		*/
		public static $add_script;

		/**
		 * Shortcode
		 *
		 * @since 1.0
		*/
		public static $shortcode;
		
		/**
		 * Main Instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 *
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Wish_Lists ) ) {
				self::$instance = new EDD_Wish_Lists;
				self::$instance->setup_globals();
				self::$instance->includes();
				self::$instance->hooks();
				self::$instance->licensing();
			}

			return self::$instance;
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 * @see EDD_Wish_Lists::init()
		 * @see EDD_Wish_Lists::activation()
		 */
		private function __construct() {
			self::$instance = $this;

			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Globals
		 *
		 * @since 1.0
		 * @return void
		 */
		private function setup_globals() {
			$this->version 		= '1.0';
			$this->title 		= 'EDD Wish Lists';

			// constants
			
			if ( ! defined( 'EDD_WL_VERSION' ) )
				define( 'EDD_WL_VERSION', '1.0' );

			if ( ! defined( 'EDD_WL_PLUGIN_FILE' ) )
				define( 'EDD_WL_PLUGIN_FILE', __FILE__ );

			if ( ! defined( 'EDD_WL_PLUGIN_URL' ) )
				define( 'EDD_WL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

			if ( ! defined( 'EDD_WL_PLUGIN_DIR' ) )
				define( 'EDD_WL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

			// paths
			$this->file         = __FILE__;
			$this->basename     = apply_filters( 'edd_wl_plugin_basenname', plugin_basename( $this->file ) );
			$this->plugin_dir   = apply_filters( 'edd_wl_plugin_dir_path',  plugin_dir_path( $this->file ) );
			$this->plugin_url   = apply_filters( 'edd_wl_plugin_dir_url',   plugin_dir_url ( $this->file ) );
		}

		/**
		 * Function fired on init
		 *
		 * This function is called on WordPress 'init'. It's triggered from the
		 * constructor function.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @uses EDD_Wish_Lists::load_textdomain()
		 *
		 * @return void
		 */
		public function init() {
			do_action( 'edd_wl_before_init' );

			$this->load_textdomain();

			do_action( 'edd_wl_after_init' );
		}

		/**
		 * Includes
		 *
		 * @since 1.0
		 * @access private
		 * @return void
		 */
		private function includes() {
			if( ! class_exists( 'EDD_License' ) ) 
				require_once( dirname( $this->file ) . '/includes/EDD_License_Handler.php' );

			require_once( dirname( $this->file ) . '/includes/post-type.php' ); 
			require_once( dirname( $this->file ) . '/includes/shortcodes.php' );
			require_once( dirname( $this->file ) . '/includes/forms.php' );
			require_once( dirname( $this->file ) . '/includes/user-functions.php' );
			require_once( dirname( $this->file ) . '/includes/functions.php' );
			require_once( dirname( $this->file ) . '/includes/template-loader.php' );
			require_once( dirname( $this->file ) . '/includes/template-functions.php' );
			require_once( dirname( $this->file ) . '/includes/wish-list-functions.php' );
			require_once( dirname( $this->file ) . '/includes/ajax-functions.php' );
			require_once( dirname( $this->file ) . '/includes/scripts.php' );
			require_once( dirname( $this->file ) . '/includes/sharing.php' );
			require_once( dirname( $this->file ) . '/includes/rewrites.php' );
			require_once( dirname( $this->file ) . '/includes/actions.php' );
			require_once( dirname( $this->file ) . '/includes/messages.php' );

			if ( ! is_admin() )
				return;

			require_once( dirname( $this->file ) . '/includes/metabox.php' );
			require_once( dirname( $this->file ) . '/includes/admin-settings.php' );
			require_once( dirname( $this->file ) . '/includes/dashboard-columns.php' );
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {
			add_action( 'admin_init', array( $this, 'activation' ) );
			

			// insert actions
			do_action( 'edd_wl_setup_actions' );
		}

		/**
		 * Licensing
		 *
		 * @since 1.0
		*/
		private function licensing() {
			$license = new EDD_License( $this->file, $this->title, $this->version, 'Andrew Munro' );
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */

		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( $this->file ) ) . '/languages/';
			$lang_dir = apply_filters( 'edd_wl_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'edd-wish-lists' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'edd-wish-lists', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-wish-lists/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				load_textdomain( 'edd-wish-lists', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				load_textdomain( 'edd-wish-lists', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-wish-lists', false, $lang_dir );
			}
		}

		/**
		 * Activation function fires when the plugin is activated.
		 *
		 * This function is fired when the activation hook is called by WordPress,
		 * it flushes the rewrite rules and disables the plugin if EDD isn't active
		 * and throws an error.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @return void
		 */
		public function activation() {
			global $wpdb;

			if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
				// is this plugin active?
				if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
					// deactivate the plugin
			 		deactivate_plugins( plugin_basename( __FILE__ ) );
			 		// unset activation notice
			 		unset( $_GET[ 'activate' ] );
			 		// display notice
			 		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}
			}
			else {
				add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'settings_link' ), 10, 2 );
			}

		}

		/**
		 * Admin notices
		 *
		 * @since 1.0
		*/
		public function admin_notices() {
			$edd_plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/easy-digital-downloads/easy-digital-downloads.php', false, false );

			if ( ! is_plugin_active('easy-digital-downloads/easy-digital-downloads.php') ) {
				echo '<div class="error"><p>' . sprintf( __( 'You must install %sEasy Digital Downloads%s to use EDD Wish Lists.', 'edd-wish-lists' ), '<a href="http://wordpress.org/plugins/easy-digital-downloads/" title="Easy Digital Downloads" target="_blank">', '</a>' ) . '</p></div>';
			}

			if ( $edd_plugin_data['Version'] < '1.8.4' ) {
				echo '<div class="error"><p>' . __( 'EDD Wish Lists requires Easy Digital Downloads Version 1.8.4 or greater. Please update Easy Digital Downloads.', 'edd-wish-lists' ) . '</p></div>';
			}
		}

		/**
		 * Plugin settings link
		 *
		 * @since 1.0
		*/
		public function settings_link( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions' ) . '">' . __( 'Settings', 'edd-wish-lists' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}

	}


/**
 * Loads a single instance of EDD Wish Lists
 *
 * This follows the PHP singleton design pattern.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @example <?php $edd_wish_lists = edd_wish_lists(); ?>
 *
 * @since 1.0
 *
 * @see EDD_Wish_Lists::get_instance()
 *
 * @return object Returns an instance of the EDD_Wish_Lists class
 */
function edd_wish_lists() {
	return EDD_Wish_Lists::get_instance();
}

/**
 * Loads plugin after all the others have loaded and have registered their hooks and filters
 *
 * @since 1.0
*/
add_action( 'plugins_loaded', 'edd_wish_lists', apply_filters( 'edd_wl_action_priority', 10 ) );

/**
 * Installation
 * 
 * Registering the hook inside the 'plugins_loaded' hook will not work. 
 * You can't call register_activation_hook() inside a function hooked to the 'plugins_loaded' or 'init' hooks (or any other hook). 
 * These hooks are called before the plugin is loaded or activated.
 *
 * @since 1.0
*/
function edd_wl_plugin_activate() {
	add_option( 'Activated_Plugin', 'edd-wish-lists' );
}
register_activation_hook( __FILE__, 'edd_wl_plugin_activate' );

function edd_wl_load_plugin() {
	include_once dirname( __FILE__ ) . '/includes/install.php';

    if ( is_admin() && get_option( 'Activated_Plugin' ) == 'edd-wish-lists' ) {

        delete_option( 'Activated_Plugin' );

        // run install script
        edd_wl_install();
    }
}
add_action( 'admin_init', 'edd_wl_load_plugin' );

endif;