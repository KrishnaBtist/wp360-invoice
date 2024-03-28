<?php
/*
  Plugin Name: Wp360 Invoice
  Description: The WP360 Invoice Plugin provides an intuitive solution to manage and create invoices seamlessly for woocommerce websites. 
  Requires at least: WP 5.2.0
  License:GPL2
  Tested up to: WP 6.3
  Author: wp360
  Author URI: https://wp360.in/
  Version: 1.0.0
  Text Domain: wp360-invoice
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! defined( 'WP360_VERSION' ) ) {
	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 */
	define( 'WP360_VERSION', '1.0.0' );
}
define( 'WP360_BASENAME', 'wp360 Invoice' );
define( 'WP360_SLUG', 'wp360-invoice' );

require_once('suite/index.php');
require_once('inc/functions.php');
require_once('front/myaccount_invoice_tab.php');
require_once('front/view_invoice.php');

add_action('admin_enqueue_scripts', 'wp360invoice_pluginAdminScripts');
function wp360invoice_pluginAdminScripts() {    
    wp_enqueue_style(WP360_SLUG.'_admin_style', plugin_dir_url(__FILE__).'admin/css/admin_style.css', array(), WP360_VERSION);
    wp_enqueue_style(WP360_SLUG.'_suite_style', plugin_dir_url(__FILE__).'suite/suite.css', array(), WP360_VERSION);
    wp_enqueue_script('jquery');
    wp_enqueue_script(WP360_SLUG.'_admin_js', plugin_dir_url(__FILE__).'admin/js/admin_script.js', array('jquery'), WP360_VERSION);  
}

add_action( 'wp_enqueue_scripts', 'wp360invoice_pluginFrontScripts');
function wp360invoice_pluginFrontScripts(){    
    wp_enqueue_style(WP360_SLUG.'_front_style', plugin_dir_url(__FILE__).'front/assets/css/front_style.css','',WP360_VERSION);
    wp_enqueue_script(WP360_SLUG.'_front_jspdf', plugin_dir_url(__FILE__).'front/assets/js/front-jspdf.js','',WP360_VERSION);
}

