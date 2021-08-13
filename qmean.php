<?php
/**
 * Plugin Name:      QMean
 * Description:      Ajax smart keyword suggestions and fix typos for better results by showing "Did You Mean", Google style! Simple, Minimal and Fast. Plus an analytics dashboard for searched queries
 * Version:            1.4.0
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
	define('QMean_PLUGIN_VERSION', '1.4.0');
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

add_action('init',array(new QMean(),'init'),100);

// do on activation
register_activation_hook( __FILE__, 'qmean_do_on_activation');
// register_deactivation_hook( __FILE__, array($this,'do_on_deactivation') );
register_uninstall_hook( __FILE__, 'qmean_do_on_uninstallation' );
function qmean_do_on_activation()
{
		update_option('_qmean_version',QMean_PLUGIN_VERSION);
		// set defaults
		$input_selector = '#search-form-1';
		$zindex = '0';
		$posx = '0';
		$posy = '-';
		$width = '-';
		$height = '200px';

		$options = array(
			'search_mode' => 'phrase',
			'sensitivity' => 3,
			'search_area' => array('posts_title','posts_content','posts_excerpt','terms'),
			'post_types' => array('post','page'),
			'input_selector' => $input_selector,
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

function qmean_update_plugin(){
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
	}
}


function qmean_typo_suggestion()
{
			$qmean_fn = new QMeanFN();
			$query = sanitize_text_field(get_query_var('s'));

			// clean spaces
			$query = trim($query," ");
			$keywords = explode(" ",$query);

			$similar_words = [];

			if($keywords){
					foreach ($keywords as $index => $keyword) {
						$similar_words = $qmean_fn->find_typos($keyword);
						if($similar_words){
							// most matched keywords is on top the array
							$keywords[$index] = $similar_words[0];
						}
					}
			}

    $qmean_keyword = implode(" ",$keywords);

		// if the queries are not he same as correct one
		if(!empty($query) && mb_strtolower($query) != mb_strtolower($qmean_keyword)){
			echo '<div class="qmean-typo-suggestion">'.__('Did you mean','qmean').': <a class="qmean-typo-suggestion-link" href="'.get_search_link($qmean_keyword).'">'.$qmean_keyword.'</a></div>';
		}
}

function qmean_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'form_class' => '',
        'input_class' => '',
        'button_class' => '',
				'form_style' => '',
				'input_style' => '',
				'button_style' => '',
        'placeholder' => __('Type to search','qmean'),
        'button_height' => '40px',
        'button_width' => '40px',
        'button_bg' => '#1a1a1a',
				'icon' => 'yes'
    ), $atts, 'qmean' );

		if($atts['button_width'] == '40px'){
			$atts['button_width'] = $atts['icon'] == 'yes' ? $atts['button_width'] : '100px';
		}

		$form_class = empty($atts['form_class']) ? ' class="qmean-shortcode-search-form"' : ' class="qmean-shortcode-search-form '.$atts['form_class'].'"';
		$input_class = empty($atts['input_class']) ? ' class="qmean-shortcode-search-field"' : ' class="qmean-shortcode-search-field '.$atts['input_class'].'"';
		$button_class = empty($atts['button_class']) ? ' class="qmean-shortcode-submit-button"' : ' class="qmean-shortcode-submit-button '.$atts['button_class'].'"';

		$form_style = empty($atts['form_style']) ? '' : ' style="'.$atts['form_style'].'"';
		$button_style = empty($atts['button_style']) ? ' style="height:'.$atts['button_height'].';width:'.$atts['button_width'].';background-color:'.$atts['button_bg'].'"' : ' style="'.$atts['button_style'].'"';
		$input_style = empty($atts['input_style']) ? ' style="height:'.$atts['button_height'].'"' : ' style="'.$atts['input_style'].'""';

		$out = '<form'.$form_class.$form_style.' method="get" action="'.get_home_url().'">';
		$out .='<input type="text" name="s" autocomplete="off" id="qmean-shortcode-search-field"'.$input_class.$input_style.' placeholder="'.$atts['placeholder'].'" value="'.get_search_query().'">';
		if($atts['icon'] == 'yes'){
			$out .='<button'.$button_style.$button_class.' type="submit"><svg width="25" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"	 viewBox="-255 347 100 100" style="enable-background:new -255 347 100 100;" xml:space="preserve"><path fill="#fff"  d="M-215.8,357c-17.5,0-31.8,14.3-31.8,31.8c0,17.5,14.3,31.8,31.8,31.8c8,0,15.3-3,20.9-7.8c2-1.8,3.8-3.8,5.3-6	c3.5-5.1,5.6-11.3,5.6-17.9C-184,371.3-198.3,357-215.8,357z M-215.8,412.6c-13.1,0-23.8-10.7-23.8-23.8c0-13.1,10.7-23.8,23.8-23.8	s23.8,10.7,23.8,23.8C-192,401.9-202.7,412.6-215.8,412.6z"/><path fill="#fff"  d="M-169.6,433.7L-169.6,433.7c-1.6,1.5-4.1,1.4-5.7-0.2l-19.7-20.8c2-1.8,3.8-3.8,5.3-6l20.2,21.3	C-167.9,429.7-168,432.2-169.6,433.7z"/><path fill="#fff"  d="M-189.6,406.7c-1.5,2.2-3.3,4.2-5.3,6L-189.6,406.7z"/></svg></button>';

		} else {
			$out .='<button'.$button_style.$button_class.' type="submit">'.__('Search','qmean').'</button>';
		}
		$out .='</form>';

    return $out;
}

// record every query searched
function qmean_analytics(){
	$query = get_query_var('s');
	if($query){
		global $wp_query;
		try {
			$qmreport = new QMeanReport();
			$qmreport->save($query,$wp_query->found_posts);
		} catch (Exception $e) {

		}
	}
}


function qmean_init(){
	// Inject a Div before the search form in search page
	add_action( 'get_search_form', 'qmean_typo_suggestion');
	// this will hook did you mean box. You just need to use do_action('qmean_suggestion') anywhere you want in your theme
	add_action( 'qmean_suggestion', 'qmean_typo_suggestion');
	// adds qmean shortcode
	add_shortcode( 'qmean', 'qmean_shortcode' );
	// For analtics of queries on next version
	add_action('wp_footer','qmean_analytics');
}

// check update compatibility
add_action( 'plugins_loaded', 'qmean_update_plugin');
// init functions
add_action('init','qmean_init');
