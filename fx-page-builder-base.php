<?php
/**
 * Plugin Name: f(x) Page Builder Base
 * Plugin URI: http://shellcreeper.com/wp-page-builder-plugin-from-scratch/
 * Description: Custom WordPress Page Builder Plugin Tutorial.
 * Version: 1.0.0
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
**/

/* Do not access this file directly */
if ( ! defined( 'WPINC' ) ) { die; }

/* Constants
------------------------------------------ */

/* Set plugin version constant. */
define( 'FX_PBBASE_VERSION', '1.0.0' );

/* Set constant path to the plugin directory. */
define( 'FX_PBBASE_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );

/* Set the constant path to the plugin directory URI. */
define( 'FX_PBBASE_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );


/* Includes
------------------------------------------ */

/* Functions */
require_once( FX_PBBASE_PATH . 'includes/functions.php' );

/* Page Builder */
if( is_admin() ){
	require_once( FX_PBBASE_PATH . 'includes/page-builder.php' );
}

/* Functions */
require_once( FX_PBBASE_PATH . 'includes/front-end.php' );

