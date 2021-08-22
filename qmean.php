<?php
/**
 * Plugin Name:      QMean
 * Description:      Ajax smart keyword suggestions and fix typos for better results by showing "Did You Mean", Google style! Simple, Minimal and Fast. Plus an analytics dashboard for searched queries
 * Version:            1.5.0
 * Author:             Arash Safari
 * Author URI:       https://github.com/arashsafaridev
 * Text Domain:     qmean
 * License:             GPL-2.0+
 * License URI:      http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/arashsafaridev/qmean/
 */


/*
 * Plugin constants
 */

// Crawler Detect

if(!defined('QMean_PLUGIN_VERSION'))
	define('QMean_PLUGIN_VERSION', '1.5.0');
if(!defined('QMean_URL'))
	define('QMean_URL', plugin_dir_url( __FILE__ ));
if(!defined('QMean_PATH'))
	define('QMean_PATH', plugin_dir_path( __FILE__ ));
// qmean libraries
require_once(QMean_PATH.'/inc/qmean-class.php');
require_once(QMean_PATH.'/inc/fn-class.php');
require_once(QMean_PATH.'/inc/ajax-class.php');
require_once(QMean_PATH.'/inc/report-class.php');

require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

// do on activation
register_activation_hook( __FILE__, 'qmean_do_on_activation');
// register_deactivation_hook( __FILE__, array($this,'do_on_deactivation') );
register_uninstall_hook( __FILE__, 'qmean_do_on_uninstallation' );
function qmean_do_on_activation()
{
		update_option('_qmean_version',QMean_PLUGIN_VERSION);
		// set defaults
		$input_selector = '#search-form-1';
		$submit_after_click = 'no';
		$zindex = '1000';
		$posx = '0';
		$posy = '-';
		$width = '-';
		$height = '300px';

		$options = array(
			'search_mode' => 'phrase',
			'sensitivity' => 3,
			'merge_previous_searched' => 'yes',
			'search_area' => array('posts_title','posts_content','posts_excerpt','terms'),
			'post_types' => array('post','page'),
			'input_selector' => $input_selector,
			'submit_after_click' => $submit_after_click,
			'suggestion_zindex' => $zindex,
			'suggestion_posx' => $posx,
			'suggestion_posy' => $posy,
			'suggestion_width' => $width,
			'suggestion_height' => $height,
			'cut_phrase_limit' => 50,
			'limit_results' => 10,
			'wrapper_background' => '#f5f5f5',
			'wrapper_border_radius' => '0px 0px 0px 0px',
			'wrapper_padding' => '0px 0px 0px 0px',
			'rtl_support' => 'no',
			'parent_position' => ''
		);
		update_option('qmean_options',$options);

		// create report db
		$keyword_table_status = get_option('_qmean_keyword_table','no');
		if($keyword_table_status != 'created'){
			global $wpdb;
			$keyword_table_name = $wpdb->prefix . "qmean_keyword";
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $keyword_table_name (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				keyword varchar(1000) NULL,
				hit int(20) NOT NULL DEFAULT '0',
				found_posts int(20) NOT NULL DEFAULT '0',
				created bigint(20) NOT NULL DEFAULT '0',
				PRIMARY KEY  (id)
			) $charset_collate;
			";
			maybe_create_table($keyword_table_name,$sql);
			update_option('_qmean_keyword_table','created');
		}
}

function qmean_do_on_uninstallation()
{
	delete_option('_qmean_version');
	delete_option('_qmean_keyword_table');
	delete_option('qmean_options');
	global $wpdb;
	$keyword_table_name = $wpdb->prefix . "qmean_keyword";
	$sql = "DROP TABLE  $keyword_table_name";
	$wpdb->query($sql);
}

$qmean_obj = new QMean();

// init QMean
// add_action('init',array($qmean_obj,'init'),100);

$qmean_settings = $qmean_obj->get_data();
$qmean_obj->set_settings($qmean_settings);
if(isset($qmean_settings['custom_hook']) && !empty($qmean_settings['custom_hook'])){
	add_action( $qmean_settings['custom_hook'], array($qmean_obj,'qmean_typo_suggestion'));
}

// check update compatibility
add_action( 'plugins_loaded', array($qmean_obj,'qmean_update_plugin'));
// Inject a Div before the search form in search page
add_action( 'get_search_form', array($qmean_obj,'qmean_typo_suggestion'));
// this will hook did you mean box. You just need to use do_action('qmean_suggestion') anywhere you want in your theme
add_action( 'qmean_suggestion', array($qmean_obj,'qmean_typo_suggestion'));
// adds qmean shortcode
add_shortcode( 'qmean', array($qmean_obj,'qmean_shortcode'));
// For analtics of queries on next version
add_action('wp_footer',array($qmean_obj,'qmean_analytics'));


// AJAX
$qmean_ajax = new QMeanAjax();
add_action( 'wp_ajax_qmean_search', array( $qmean_ajax, 'search' ) );
add_action( 'wp_ajax_nopriv_qmean_search', array( $qmean_ajax, 'search' ) );
add_action( 'wp_ajax_qmean_save_from_recognizer', array( $qmean_ajax, 'save_from_recognizer' ) );
add_action( 'wp_ajax_qmean_remove_keyword', array( $qmean_ajax, 'remove_keyword' ) );
