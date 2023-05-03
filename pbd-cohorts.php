<?php
/**
 * Plugin Name: PBD Cohorts
 * Plugin URI: https://pbdigital.com.au/
 * Description: This is PB Digital Cohorts plugin
 * Author:     PB Digital
 * Author URI: https://pbdigital.com.au/
 * Version: 1.0.2
 * Text Domain:   pbd-co
 */

// define constants
if ( ! defined( 'PBD_COHORT_PLUGIN_NAME' ) ) {
	define( 'PBD_COHORT_PLUGIN_NAME', 'PBD Cohorts' );
}
if ( ! defined( 'PBD_CO_PATH_CLASS' ) ) {
	define( 'PBD_CO_PATH_CLASS', dirname( __FILE__ ) . '/class' );
}
if ( ! defined( 'PBD_CO_PATH' ) ) {
	define( 'PBD_CO_PATH', dirname( __FILE__ ) );
}
if ( ! defined( 'PBD_CO_FOLDER' ) ) {
	define( 'PBD_CO_FOLDER', basename( PBD_CO_PATH ) );
}
if ( ! defined( 'PBD_CO_URL' ) ) {
	define( 'PBD_CO_URL', plugins_url() . '/' . PBD_CO_FOLDER );
}
if ( ! defined( 'PBD_CO_INCLUDES_PATH' ) ) {
	define( 'PBD_CO_INCLUDES_PATH', PBD_CO_PATH . '/includes' );
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
    if( ! class_exists( 'PBD_Cohorts' ) ):
		// only activate if GamiPress is installed and active
		if ( !function_exists( 'pbd_co_activation' ) ) {
			register_activation_hook( __FILE__, 'pbd_co_activation' );
			function pbd_co_activation(){
				// if ( ! class_exists('GamiPress') ) {
				// 	deactivate_plugins( plugin_basename( __FILE__ ) );
				// 	wp_die('Sorry, but this plugin requires the GamiPress to be installed and active.');
				// }
			}
		}
	
		add_action( 'admin_init', 'pbd_coplugin_activate' );
		function pbd_coplugin_activate(){
			// if ( ! class_exists( 'GamiPress' ) ) {
			// 	deactivate_plugins( plugin_basename( __FILE__ ) );
			// }
		}
	
		include_once( PBD_CO_PATH_CLASS.'/pbd-cohorts.class.php' );
		add_action( 'plugins_loaded', array( 'PBD_Cohorts', 'get_instance' ) );
	endif;
} else {
    // ACF Pro is not active, so you should not activate your plugin
	deactivate_plugins( plugin_basename( __FILE__ ) );
    add_action( 'admin_notices', 'my_plugin_acf_error_notice' );
}

function my_plugin_acf_error_notice() {
    ?>
    <div class="error">
        <p><?php _e( PBD_COHORT_PLUGIN_NAME . ' requires the Advanced Custom Fields Pro plugin to be installed and activated.', 'pbd' ); ?></p>
    </div>
    <?php
}
