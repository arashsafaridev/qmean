<?php
/**
 * Plugin Name:      QMean
 * Description:      Ajax smart keyword suggestions and fix typos for better results by showing "Did You Mean", Google style!
 * Version:            1.1.0
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
	define('QMean_PLUGIN_VERSION', '1.1.0');
if(!defined('QMean_URL'))
	define('QMean_URL', plugin_dir_url( __FILE__ ));
if(!defined('QMean_PATH'))
	define('QMean_PATH', plugin_dir_path( __FILE__ ));
// qmean libraries
require_once(QMean_PATH.'/inc/qmean-class.php');
require_once(QMean_PATH.'/inc/fn-class.php');
require_once(QMean_PATH.'/inc/ajax-class.php');

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
		$posx = '0';
		$posy = '-';
		$width = '-';
		$height = '200px';

		$options = array(
			'search_mode' => 'phrase',
			'sensitivity' => 3,
			'search_area' => array('posts_title','posts_content','posts_excerpt'),
			'post_types' => array('post','page'),
			'input_selector' => $input_selector,
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
}

function qmean_do_on_uninstallation()
{
	delete_option('_qmean_version');
	delete_option('qmean_options');
}


 // Inject a Div before the search form in search page
add_action( 'get_search_form', 'qmean_typo_suggestion');

// this will hook did you mean box. You just need to use do_action('qmean_suggestion') anywhere you want in your theme
add_action( 'qmean_suggestion', 'qmean_typo_suggestion');
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

    $qmean_query = implode(" ",$keywords);

		// if the queries are not he same as correct one
		if(!empty($query) && mb_strtolower($query) != mb_strtolower($qmean_query)){
			echo '<div class="qmean-typo-suggestion">'.__('Did you mean','qmean').': <a class="qmean-typo-suggestion-link" href="'.get_search_link($qmean_query).'">'.$qmean_query.'</a></div>';
		}
}
