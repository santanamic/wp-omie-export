<?php 

/**
 * Plugin Name: Omie Export
 * Author: WILLIAN SANTANA
 * Version: v1.0.2
 */
 
define( 'OMIE_EXPORT_TITLE', __( 'Omie Export', 'omie_export' ) );
define( 'OMIE_EXPORT_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'OMIE_EXPORT_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'OMIE_EXPORT_VERSION', '1.0.2' );

/**
 * Loads plugin resources.
 */
require_once( OMIE_EXPORT_PATH . 'inc/helper.php' );
require_once( OMIE_EXPORT_PATH . 'inc/admin.php' );

/**
 * Begins execution of the plugin admin.
 */
add_action( 'init', 'omie_export_admin_init', -90 );

if ( ! function_exists( 'omie_export_admin_init' ) ) {

	/**
	 * Start plugin in admin.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function omie_export_admin_init() 
	{
		if ( is_admin() ) {
			/* Plugin settings */
			add_action( 'admin_menu', 'omie_export_admin_register_submenu' );
			add_action( 'admin_enqueue_scripts', 'omie_export_admin_enqueue' );

			/* Plugin links */
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'omie_export_admin_plugin_links' );

			//init export data
 			if( isset( $_POST['omie-export-submit'] ) ) {
				if( current_user_can( 'administrator' ) ) {
					omie_export_request_data();
				}
			}

		}
	}
	
}