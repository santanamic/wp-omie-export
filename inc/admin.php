<?php

if ( ! function_exists( 'omie_export_admin_register_submenu' ) ) {

	/**
	 * Register a plugin sub page in WC menu.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function omie_export_admin_register_submenu() 
	{
		add_submenu_page(
			'tools.php',
			OMIE_EXPORT_TITLE,
			'<i style="font-style: normal;font-size: 1.4em;position: relative;top: 0.04em;left: -0.3em; margin-left: 0.2em;">★</i>' . OMIE_EXPORT_TITLE,
			'manage_options',
			'omie_export',
			function() {
				return (
					include ( 
						OMIE_EXPORT_PATH . '/view/admin/callback/html-admin-callback-page.php' 
					)
				);
			}
		);
		
	}
	
}

if ( ! function_exists( 'omie_export_admin_add_notice' ) ) {

	/**
	 * Adds a message in the administrative panel.
	 *
	 * @since    1.0.0
	 * @param    bool    $class   The class priority supported by WordPress
	 * @param    bool    $msg     The message body in plain text or HTML
	 * @return   void
	 */
	function omie_export_admin_add_notice( string $class = 'notice notice-info is-dismissible', string $msg = '' ) 
	{			
		add_action( 
			'admin_notices', 
			function() use ( $class, $msg ) {
				return ( 
					omie_export_include_view( 
						OMIE_EXPORT_PATH . '/view/admin/html-admin-notice.php', 
						array(
							'class'   => $class,
							'message' => $msg,
						),
						true
					)
				);
			} 
		);
	}

}

if ( ! function_exists( 'omie_export_admin_enqueue' ) ) {

	/**
	 * Register the JavaScrip and CSS for admin
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function omie_export_admin_enqueue() 
	{
		wp_enqueue_style( 
			'omie_export', 
			OMIE_EXPORT_URI . '/assets/css/admin.css', 
			array(), 
			OMIE_EXPORT_VERSION,
			'all' 
		);
		
		wp_enqueue_script( 
			'omie_export', 
			OMIE_EXPORT_URI . '/assets/js/admin.js', 
			array( 'jquery' ), 
			OMIE_EXPORT_VERSION, 
			false 
		);

	}
	
}

if ( ! function_exists( 'omie_export_admin_plugin_links' ) ) {

	/**
	 * Set plugin setting link.
	 *
	 * @since    1.0.0
	 * @param    array    $links  Initial registered  links.
	 * @return   array    $links
	 */
	function omie_export_admin_plugin_links( $links ) 
	{
		$links[] = '<a href="'. esc_url( get_admin_url( null, 'admin.php?page=omie_export' ) ) .'">Settings</a>';
		
		return $links;
	}
	
}