<?php

/*Bootstrap WC_PR Plugin, This is where the magic starts.*/

/**
@link http://codup.io
@since 1.1.1.0
@package WC_PR

*/

/**
 * @wordpress-plugin
 * Plugin Name:Codup WooCommerce Profit Reporting
 * Plugin URI: http://codup.io
 * Description: Adds Profit Reporting Capability to WooCommerce.
 * Version:  1.2.1.10
 * Author: Codup.io
 * Author URI: http://codup.io
 * Requires at least: 4.4
 * Tested up to: 5.0.2
 * 
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: codup-wc-profit-reporting
 * Domain Path: /i18n/languages/
 * 
 * @package WooCommerce
 * @category Core
 * @author Codup.io
*/




//Abort if file is directly accessed.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('CODUP_PR_PLUGIN_NAME','profit-reports');
define('CODUP_PR_PLUGIN_VER','1.1.3.7');
define('CODUP_PR_PAGE_SLUG','codup-pr-settings');

//Require Aactivator Class
require_once plugin_dir_path(__FILE__).'includes/class-wcpr-activator.php';

/**
 * Calls Activation Procedure from Activator Class
 *
 * @since 1.1.1.0
 * Documented in includes/class-wcpr-activator.php
*/
function codup_pr_activate_productor() {
    WC_PR_Activator::codup_pr_activate();
}

register_activation_hook(__FILE__,'codup_pr_activate_productor');



require plugin_dir_path( __FILE__ ) . 'includes/class-wcpr-plugin.php';


function codup_pr_run_wcpr() {
    $plugin = new WC_PR('CODUP_PR_PLUGIN_NAME','CODUP_PR_PLUGIN_VER');
    $plugin->codup_pr_run();
}


add_action("plugins_loaded", "codup_pr_check_woocommerce");

function codup_pr_check_woocommerce() {
        if(!class_exists('WooCommerce')) {
            add_action('admin_notices','codup_pr_woocommerce_error');
        }
        else {
            codup_pr_run_wcpr();

        }
}

function codup_pr_woocommerce_error() {
            echo '<div class="notice notice-error"><p><strong>Profit Reports for WooCommerce</strong>requires <strong>WooCommerce</strong> to run properly. <strong>Please Actiavte or Install the Plugin</strong>.</p></div>';
}

if (!class_exists('CodupAds')){
    require_once plugin_dir_path(__FILE__) . 'lib/codupads/codupads.php';
}
new CodupAds(); 
