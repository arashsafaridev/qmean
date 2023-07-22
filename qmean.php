<?php
/**
 * Plugin Name:       QMean
 * Description:       Ajax smart keyword suggestions and fix typos for better results by showing "Did You Mean", Google style! Simple, Minimal, Smart and Fast. Plus an analytics dashboard for searched queries
 * Version:           2.0
 * Author:            Arash Safari
 * Author URI:        https://github.com/arashsafaridev/qmean
 * Text Domain:       qmean
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/arashsafaridev/qmean/
 * Donate link: 	  https://www.paypal.com/paypalme/arashsafaris
 */


/*
 * Plugin constants
 */

if(!defined('QMEAN_PLUGIN_VERSION')) {
    define('QMEAN_PLUGIN_VERSION', '2.0.0');
}

if(!defined('QMEAN_URL')) {
    define('QMEAN_URL', plugin_dir_url( __FILE__ ));
}

if(!defined('QMEAN_PATH')) {
    define('QMEAN_PATH', plugin_dir_path( __FILE__ ));
}

if(!defined('QMEAN_FILE')) {
    define('QMEAN_FILE', __FILE__);
}

if(!defined('QMEAN_BASENAME')) {
    define('QMEAN_BASENAME', plugin_basename( __FILE__ ));
}


require_once(QMEAN_PATH.'/inc/class-qmean.php');
require_once(QMEAN_PATH.'/inc/class-fn.php');
require_once(QMEAN_PATH.'/inc/class-ajax.php');
require_once(QMEAN_PATH.'/inc/class-report.php');
require_once(QMEAN_PATH.'/inc/class-stopwords.php');
require_once(QMEAN_PATH.'/inc/class-filter.php');

require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );


/**
 * Run on plugin uninstallation
 * 
 * remove plugin's internal settings
 * drop the keywords table
 * 
 */
function qmean_do_on_uninstallation()
{
    global $wpdb;

    delete_option('_qmean_version');
    delete_option('_qmean_keyword_table');
    delete_option('qmean_options');

    $keyword_table_name = $wpdb->prefix . "qmean_keyword";
    $sql = "DROP TABLE  $keyword_table_name";
    $wpdb->query($sql);
}

register_uninstall_hook( __FILE__, 'qmean_do_on_uninstallation');

/**
 * Start Qmean
 */
QMean::get_instance();
