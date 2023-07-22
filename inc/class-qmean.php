<?php

/**
 * Class QMean
 * create the option page and add the web app script
 */
class QMean
{

	/**
	 * AJAX nonce name 
	 */
	private $_nonce = 'qmean_nonce';
	
	/**
	 * QMean option name 
	 */
	private $option_name = 'qmean_options';

	/**
	 * Save plugin settings
	 */
	private $settings = [];

	/**
	 * Singleton
	 */
	public static $instance = NULL;

	/**
	 * Apply singleton
	 * 
	 * @return object QMean 
	 */
	public static function get_instance()
	{
		NULL === self::$instance and self::$instance = new self;
		return self::$instance;
	}

	/**
	 * Add activation and uninstallation hooks
	 * Add admin pages
	 * Add scripts and styles
	 * Add qmean did you mean hooks
	 * Add shortcodes
	 * Add Update plugin hook
	 * Add plugin settings menu
	 */
	public function __construct()
	{
		// do on activation
		register_activation_hook(QMEAN_FILE, [$this, 'qmean_do_on_activation']);

		add_action( 'admin_menu',                [$this, 'add_admin_menu'] );
		add_action( 'admin_enqueue_scripts',     [$this, 'add_admin_scripts'] );
		add_action( 'wp_enqueue_scripts',     	[$this, 'add_scripts'] );

		// Add Did You Mean block
		add_action( 'init', 					[$this, 'create_block'] );
		
		// customize core search block
		add_filter( 'render_block', array( $this, 'customize_search_block' ), 10, 2 );


		$settings = $this->get_data();
		$this->settings = $settings;

		if (isset($settings['custom_hook']) && !empty($settings['custom_hook'])) {
			add_action( $settings['custom_hook'], [$this, 'typo_suggestion']);
		}

		// check update compatibility
		add_action('upgrader_process_complete',   [$this, 'update_plugin'], 10, 2);
		
		// Inject a Div before the search form in search page
		add_action('get_search_form',  [$this, 'typo_suggestion']);
		// this will hook did you mean box. You just need to use do_action('qmean_suggestion') anywhere you want in your theme
		add_action('qmean_suggestion', [$this, 'typo_suggestion']);
		// For analtics of queries on next version
		add_action('wp_footer',				 [$this, 'analytics']);
		// adds qmean shortcode
		add_shortcode('qmean', 				 [$this, 'shortcode']);
		// adds did you mean shortcode
		add_shortcode('qmean-dym', 		 [$this, 'did_you_mean_shortcode']);

		// add settings action link to plugins page
		add_filter( 'plugin_action_links_'.plugin_basename(QMEAN_FILE), [$this, "add_plugin_action_row_links"], 10, 2 );
	}

	/**
	 * Get predefined and default options
	 * needed on activation and plugin update
	 * 
	 * @return array $options 	default options
	 */
	private function get_default_options()
	{
		$input_selector = 'input[name="s"]';
		$submit_after_click = 'no';
		$zindex = '1000';
		$posx = '0';
		$posy = '-';
		$width = '-';
		$height = '300px';

		$sql_patterner = $this->qmean_test_mysql_compatibility();
		$sql_patterner = empty($sql_patterner) ? '\\\\b(%s)\\\\b' : $sql_patterner;

		$options = [
			'sql_patterner' 		  => $sql_patterner,
			'suggest_engine' 		  => 'qmean',
			'search_mode' 			  => 'word_by_word',
			'sensitivity' 			  => 3,
			'merge_previous_searched' => 'yes',
			'keyword_efficiency'      => 'on',
			'ignore_shortcodes' 	  => 'no',
			'search_area' 			  => [
											'posts_title',
											'posts_content',
											'posts_excerpt',
											'terms'
										],
			'post_types' 			  => [
											'post',
											'page'
										],
			'input_selector' 		  => $input_selector,
			'submit_after_click' 	  => $submit_after_click,
			'suggestion_zindex' 	  => $zindex,
			'suggestion_posx'  	      => $posx,
			'suggestion_posy' 		  => $posy,
			'suggestion_width' 		  => $width,
			'suggestion_height' 	  => $height,
			'word_count' 			  => 5,
			'limit_results' 		  => 10,
			'wrapper_background' 	  => '#f5f5f5',
			'wrapper_border_radius'   => '0px 0px 0px 0px',
			'wrapper_padding' 		  => '0px 0px 0px 0px',
			'rtl_support' 			  => 'no',
			'parent_position' 		  => '',
			'remove_stop_words'		  => 'no'
		];

		return $options;
	}

	/**
	 * Run on plugin activation
	 * 
	 * Set plugin's internal settings
	 * Create keyword table
	 * 
	 */
	public function qmean_do_on_activation()
	{
		update_option( '_qmean_version',QMEAN_PLUGIN_VERSION, 'no' );
		// set defaults
		
		$options = $this->get_default_options();

		$updated_options = wp_parse_args($this->settings, $options);
		update_option( 'qmean_options', $updated_options, 'no' );

		// create report db
		$keyword_table_status = get_option('_qmean_keyword_table','no');

		if ($keyword_table_status != 'created') {
			global $wpdb;
			$keyword_table_name = $wpdb->prefix . "qmean_keyword";
			$charset_collate 	= $wpdb->get_charset_collate();
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


	/**
	 * Check the regex compatibily base on mysql version
	 * 
	 * Check if pattern one is working
	 * if not pattern too is valid
	 * 
	 * @return string 	the valid pattern 
	 */
	public function qmean_test_mysql_compatibility()
	{
		global $wpdb;

		// get current error reporting level.
		$error_level = error_reporting();
		// diable warnings for huge content.
		// to avoid warning: Timeout exceeded in regulur expression.
		error_reporting(E_ALL ^ E_WARNING);
		// don't log WPDB errors. just for this query
		$wpdb->suppress_errors(true);

		$table = $wpdb->prefix.'options';
		$sql = "SELECT option_name FROM $table WHERE LOWER(option_name) REGEXP %s";
		$results = $wpdb->get_results(
			$wpdb->prepare($sql, array("\\b(site.*)\\b"))
		);

		// restore WPDB error logging.
		$wpdb->suppress_errors(false);
		// restore error reporting level.
		error_reporting($error_level);
		
		if (count($results) > 0) {
			return '\\\\b(%s)\\\\b';
		} else {
			$results = $wpdb->get_results(
				$wpdb->prepare($sql, array("[[:<:]](site.*)"))
			);

			if ( count($results) ) {
				return '[[:<:]](%s)';
			} else {
				return '/[[:<:]](%s)/';
			}
		}
	}

	 /**
	  * Record searched keyword
	  * 
	  * Hook to wp_footer and record `s` query var
	  */
	public function analytics()
	{
		$query = get_query_var('s');

		if ($query) {
			global $wp_query;
			try {
				$qmreport = new QMeanReport();
				$qmreport->save($query,$wp_query->found_posts);
			} catch (Exception $e) {
				error_log(__('Saving query %s failed', 'qmean'), $query);
			}
		}
		// field auto reconginizer
		if (current_user_can('manage_options') && isset($_GET['qmean_field_recognizer'])) { ?>
			<div class="qmean-field-recognizer-header-tooltip"><?php _e('Please move your mouse and click on your search input field','oodev');?></div>
			<div class="qmean-field-recognizer-tooltip"><?php _e('Please click on your field','oodev');?></div>
		<?php }
	}

	/**
	 * Add search field shortcode
	 * 
	 * @param  array  $atts 	the attributes passed with shortcode
	 * @return string $out 		rendered search form in HTML
	 */
	public function shortcode( $atts )
	{
	    $atts = shortcode_atts([
	        'areas' 		=> [],
	        'post_types' 	=> [],
	        'form_class' 	=> '',
	        'input_class' 	=> '',
	        'button_class'  => '',
			'form_style' 	=> '',
			'input_style' 	=> '',
			'button_style' 	=> '',
	        'placeholder' 	=> __('Type to search','qmean'),
			'button_text' 	=> '',
	        'button_height' => '40px',
	        'button_width'  => '40px',
	        'button_bg' 	=> '#1a1a1a',
			'icon' 			=> 'yes'
	    ], $atts, 'qmean');

			$button_text = isset($atts['button_text']) ? $atts['button_text'] : __('Search','qmean');

			if ($atts['button_width'] == '40px') {
				$atts['button_width'] = $atts['icon'] == 'yes' ? $atts['button_width'] : '100px';
			}

			$form_class 	= empty($atts['form_class']) ? ' class="qmean-shortcode-search-form"' : ' class="qmean-shortcode-search-form '.$atts['form_class'].'"';
			$input_class  = empty($atts['input_class']) ? ' class="qmean-shortcode-search-field"' : ' class="qmean-shortcode-search-field '.$atts['input_class'].'"';
			$button_class = empty($atts['button_class']) ? ' class="qmean-shortcode-submit-button"' : ' class="qmean-shortcode-submit-button '.$atts['button_class'].'"';

			$form_style 	= empty($atts['form_style']) ? '' : ' style="'.$atts['form_style'].'"';
			$button_style = empty($atts['button_style']) ? ' style="height:'.$atts['button_height'].';width:'.$atts['button_width'].';background-color:'.$atts['button_bg'].'"' : ' style="'.$atts['button_style'].'"';
			$input_style  = empty($atts['input_style']) ? ' style="height:'.$atts['button_height'].'"' : ' style="'.$atts['input_style'].'""';

			$custom_areas = isset($atts['areas']) && !empty($atts['areas']) ? ' data-areas="'.$atts['areas'].'"' : '';
			$custom_post_types = isset($atts['post_types']) && !empty($atts['post_types']) ? ' data-post_types="'.$atts['post_types'].'"' : '';
			$out  = '<form'.$form_class.$form_style.' method="get" action="'.get_home_url().'"'.$custom_areas.$custom_post_types.'>';
			$out .='<input type="text" name="s" autocomplete="off" id="qmean-shortcode-search-field"'.$input_class.$input_style.' placeholder="'.$atts['placeholder'].'" value="'.get_search_query().'">';
			if ($atts['icon'] == 'yes') {
				$out .='<button'.$button_style.$button_class.' type="submit"><svg width="25" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"	 viewBox="-255 347 100 100" style="enable-background:new -255 347 100 100;" xml:space="preserve"><path fill="#fff"  d="M-215.8,357c-17.5,0-31.8,14.3-31.8,31.8c0,17.5,14.3,31.8,31.8,31.8c8,0,15.3-3,20.9-7.8c2-1.8,3.8-3.8,5.3-6	c3.5-5.1,5.6-11.3,5.6-17.9C-184,371.3-198.3,357-215.8,357z M-215.8,412.6c-13.1,0-23.8-10.7-23.8-23.8c0-13.1,10.7-23.8,23.8-23.8	s23.8,10.7,23.8,23.8C-192,401.9-202.7,412.6-215.8,412.6z"/><path fill="#fff"  d="M-169.6,433.7L-169.6,433.7c-1.6,1.5-4.1,1.4-5.7-0.2l-19.7-20.8c2-1.8,3.8-3.8,5.3-6l20.2,21.3	C-167.9,429.7-168,432.2-169.6,433.7z"/><path fill="#fff"  d="M-189.6,406.7c-1.5,2.2-3.3,4.2-5.3,6L-189.6,406.7z"/></svg></button>';

			} else {
				$out .='<button'.$button_style.$button_class.' type="submit">'.$button_text.'</button>';
			}

			$out .='</form>';

	    return $out;
	}

	/**
	 * Add did you mean shortcode
	 * 
	 * If used it will suggest base on query var searched
	 * 
	 * @param  array  $atts 	the attributes passed with the shortcode
	 * @return string $out 		the rendered found keyword in HTML
	 */
	public function did_you_mean_shortcode($atts)
	{
	    $atts = shortcode_atts( array(
	        'wrapper_class' => ''
	    ), $atts, 'qmean-dym' );

	    	$out = '';
			$wrapper_class = empty($atts['wrapper_class']) ? ' class="qmean-shortcode-did-you-mean"' : ' class="qmean-shortcode-did-you-mean '.$atts['wrapper_class'].'"';
			$out .='<div'.$wrapper_class.'>';
			ob_start();
			$this->_typo_suggestion();
			$out .= ob_get_clean();

			$out .='</div>';

	    return $out;
	}

	/**
	 * Add settings link to plugin page
	 *
	 * @since 1.0.0
	 * @param array $links_array        	plugin actions link
	 * @param string $plugin_file_name  	the plugin file
	 * @return array $actions 				updated links
	 */
	public function add_plugin_action_row_links( $links_array, $plugin_file_name ) {
		$settings_link = ['<a href="' . admin_url( 'admin.php?page=qmean-settings' ) . '">'.esc_html__('Settings', 'qmean').'</a>'];
		$actions = array_merge( $settings_link, $links_array);
   	return $actions;
	}

	/**
	 * Find best keyword matched
	 * 
	 * private method which has a public interface
	 * 
	 */
	private function _typo_suggestion( $echo = true )
	{
		$qmean_fn = new QMeanFN();
		$query = sanitize_text_field(get_query_var('s'));

		// clean spaces
		$query = trim($query," ");
		$keywords = explode(" ",$query);

		$similar_words = [];

		/**
		 * Get suggested words for each word in the query
		 * and get the first one ranked higher
		 * 
		 * use index to replace words in the correct order
		 */
		if ($keywords) {
				foreach ($keywords as $index => $keyword) {
					$similar_words = $qmean_fn->find_typos($keyword);
					if ($similar_words) {
						// most matched keywords is on top the array
						$keywords[$index] = $similar_words[0];
					}
				}
		}

    	$qmean_keyword = implode(" ",$keywords);

		// if the queries are not the same as correct one
		if (!empty($query) && mb_strtolower($query) != mb_strtolower($qmean_keyword)) {
			
			if ($echo) {
				$out = '<div class="qmean-typo-suggestion">'.__('Did you mean','qmean').': <a class="qmean-typo-suggestion-link" href="'.get_search_link($qmean_keyword).'">'.$qmean_keyword.'</a></div>';
				echo $out;
			} else {
				$out = '<a class="qmean-typo-suggestion-link" href="'.get_search_link($qmean_keyword).'">'.$qmean_keyword.'</a>';
				return $out;
			}
		}
	}

	/**
	 * Public interface of _typo_suggestion method
	 */
	public function typo_suggestion()
	{
		global $wp_query;
		if ( $wp_query->found_posts <= 0) {
			$this->_typo_suggestion();
		}
	}

	/**
	 * Run on plugin updates 
	 * check update process with wp options
	 */
	public function update_plugin( $upgrader, $extra )
	{
		if( $extra['action'] === 'update' && $extra['type'] === 'plugin' && isset( $extra['plugins'] ) ) {
			foreach( $extra['plugins'] as $plugin ) {
				if( $plugin === QMEAN_BASENAME) {
					// $current_version = get_option('_qmean_upgrade_version', '19');

					// if (version_compare($current_version, QMEAN_PLUGIN_VERSION, '<')) {
						
					// }

					update_option( '_qmean_version', QMEAN_PLUGIN_VERSION, 'no' );

					$options = $this->get_default_options();

					$updated_options = wp_parse_args($this->settings, $options);
					update_option('qmean_options',$updated_options, 'no');
					set_transient( 'qmean_updated', 1, 604800 );
				}
			}
		}

	}

	/**
	  * Return the saved options data
	  * 
	  * @return array 	user saved option
	  */ 
	private function get_data()
	{
		return get_option($this->option_name, []);
	}

	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	public function create_block() {
		register_block_type( QMEAN_PATH . '/blocks/did-you-mean/build', array(
			'render_callback' => array( $this, 'render_block' ),
		) );
	}
	
	public function render_block($attributes, $content)
	{
		$QMean = new QMean();
		$out = $QMean->_typo_suggestion(false);

		if ( ! empty( $out ) ) {
			return preg_replace( '/<code>(.*?)<\/code>/s', $out, $content );
		}
	}

	/**
	 * Customize Search Block
	 * 
	 * @since  1.9.0
	 * @param  string $block_content 	the block content
	 * @param  array  $block 			the block attributes
	 * @return string $block_content 	the updated block content
	 */
	public function customize_search_block( $block_content, $block ){
		// If block is `core/image` we add new content related to the new attribute
		if ( $block['blockName'] === 'core/search' && isset( $block['attrs']['isQmeanActive'] ) && $block['attrs']['isQmeanActive']) {
			$attributes = $block['attrs'];
			$post_types = isset( $attributes['postTypes'] ) ? implode( ",", array_column($attributes['postTypes'], 'id')) : '';
			$areas      = isset( $attributes['searchIn'] ) ? implode( ",", array_column($attributes['searchIn'], 'id')) : '';

			if ( !empty( $post_types ) ) {
				$block_content = str_replace( '<form', '<form data-post_types="'.$post_types.'"', $block_content );
			}

			if ( !empty( $areas ) ) {
				$block_content = str_replace( '<form', '<form data-areas="'.$areas.'"', $block_content );
			}
		}

		return $block_content;
	}

	/**
	 * Add Admin Scripts for the Ajax call
	 * 
	 * for dashboard and settings page 
	 */
	public function add_admin_scripts()
	{
		wp_enqueue_script( 'jquery' );
		$screen = get_current_screen();

		if (strpos($screen->id,'qmean')) {
			wp_enqueue_style('qmean-settings', QMEAN_URL. 'assets/css/admin.css', false, QMEAN_PLUGIN_VERSION);
			wp_enqueue_script('qmean-admin', QMEAN_URL. 'assets/js/admin.js', ['jquery'], QMEAN_PLUGIN_VERSION);
		} else if (strpos($screen->id,'qmean-settings')) {
			wp_enqueue_style('qmean-settings', QMEAN_URL. 'assets/css/admin-settings.css', false, QMEAN_PLUGIN_VERSION);
			wp_enqueue_script('qmean-settings', QMEAN_URL. 'assets/js/admin-settings.js', ['jquery', 'wp-color-picker'], QMEAN_PLUGIN_VERSION);
			wp_enqueue_style('wp-pointer');
    		wp_enqueue_script('wp-pointer');
    		wp_enqueue_style( 'wp-color-picker'); 
		}

		$admin_options = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'_nonce'   => wp_create_nonce( $this->_nonce )
		);

		wp_localize_script('qmean-settings', 'qmean', $admin_options);
		wp_localize_script('qmean-admin', 'qmean',	  $admin_options);
	}


	/**
	 * Add Front-End Scripts for the Ajax call and Styles
	 */
	public function add_scripts()
	{
		if (current_user_can('manage_options') && isset($_GET['qmean_field_recognizer'])) {
			wp_enqueue_script('qmean-recognizer-script', QMEAN_URL. 'assets/js/qmean-recognizer.js', ['jquery'], QMEAN_PLUGIN_VERSION);
		}

		wp_enqueue_style('qmean-style', QMEAN_URL. 'assets/css/qmean.css', false, QMEAN_PLUGIN_VERSION);
		wp_enqueue_script('qmean-script', QMEAN_URL. 'assets/js/qmean.js', ['jquery'], QMEAN_PLUGIN_VERSION);
		$settings 				 = $this->settings;
		$input_selector 		 = !isset($settings['input_selector']) || empty($settings['input_selector']) ? '.qmean-block-search-form input[type=search],.qmean-shortcode-search-field, input[name="s"]' : '.qmean-block-search-form input[type=search], .qmean-shortcode-search-field, '.stripslashes($settings['input_selector']);
		$zindex 				 = !isset($settings['suggestion_zindex']) ? '0' : $settings['suggestion_zindex'];
		$posx 					 = !isset($settings['suggestion_posx']) ? '-' : $settings['suggestion_posx'];
		$posy 					 = !isset($settings['suggestion_posy']) ? '-' : $settings['suggestion_posy'];
		$width 					 = !isset($settings['suggestion_width']) ? '-' : $settings['suggestion_width'];
		$height 				 = !isset($settings['suggestion_height']) ? '-' : $settings['suggestion_height'];
		$parent_position 		 = empty($settings['parent_position']) ? '' : $settings['parent_position'];
		$word_count 			 = empty($settings['word_count']) ? 5 : $settings['word_count'];
		$limit_results 			 = empty($settings['limit_results']) ? 10 : $settings['limit_results'];
		$search_mode 			 = empty($settings['search_mode']) ? '' : $settings['search_mode'];
		$suggest_engine 		 = empty($settings['suggest_engine']) ? 'QMean' : $settings['suggest_engine'];
		$merge_previous_searched = empty($settings['merge_previous_searched']) ? 'yes' : $settings['merge_previous_searched'];
		$keyword_efficiency 	 = empty($settings['keyword_efficiency']) ? 'on' : $settings['keyword_efficiency'];
		$ignore_shortcodes  	 = empty($settings['ignore_shortcodes']) ? 'no' : $settings['ignore_shortcodes'];
		$wrapper_background 	 = empty($settings['wrapper_background']) ? '#f5f5f5' : $settings['wrapper_background'];
		$wrapper_border_radius   = empty($settings['wrapper_border_radius']) ? '0px 0px 0px 0px' : $settings['wrapper_border_radius'];
		$wrapper_padding 		 = empty($settings['wrapper_padding']) ? '0px 0px 0px 0px' : $settings['wrapper_padding'];
		$rtl_support 			 = empty($settings['rtl_support']) ? 'no' : $settings['rtl_support'];


		$options = [
			'ajax_url' 					  => admin_url( 'admin-ajax.php' ),
			'home_url' 					  => get_home_url(),
			'_nonce'   					  => wp_create_nonce( $this->_nonce ),
			'selector' 					  => $input_selector,
			'submit_after_click' 		  => $settings['submit_after_click'],
			'zindex' 					  => $zindex,
			'posx' 						  => $posx,
			'posy' 						  => $posy,
			'width' 					  => $width,
			'height' 					  => $height,
			'word_count' 				  => $word_count,
			'limit_results' 			  => $limit_results,
			'parent_position' 			  => $parent_position,
			'search_mode' 				  => $search_mode,
			'suggest_engine' 			  => $suggest_engine,
			'merge_previous_searched' 	  => $merge_previous_searched,
			'keyword_efficiency' 		  => $keyword_efficiency,
			'ignore_shortcodes' 		  => $ignore_shortcodes,
			'wrapper_background' 		  => $wrapper_background,
			'wrapper_border_radius' 	  => $wrapper_border_radius,
			'wrapper_padding' 			  => $wrapper_padding,
			'rtl_support' 				  => $rtl_support,
			'labels'   					  => [
				'loading' 				  => __('Loading ...','qmean'),
				'back' 					  => __('Back','qmean'),
				'notFound' 				  => __('Nothing to suggest! Click to see all the results.','qmean'),
				'pleaseChooseAnInputType' => __('Please choose a field which is an input like input or textarea','qmean'),
				'isNotValid' 			  => __('is not valid!','qmean'),
				'saveSelector' 			  => __('Save Selector','qmean'),
				'yourSelectorIs' 		  => __('Your selector is','qmean')
			]
		];

		wp_localize_script('qmean-script', 'qmean', $options);
	}

	/**
	 * Add the QMean label to the WordPress Admin Sidebar Menu
	 */
	public function add_admin_menu()
    {
		add_menu_page(
			__( 'QMean', 'qmean' ),
			__( 'QMean', 'qmean' ),
			'manage_options',
			'qmean',
			[$this, 'dashboard_layout'],
			QMEAN_URL.'/assets/images/qmean-dashicon.svg?v='.QMEAN_PLUGIN_VERSION,
			2
		);

		add_submenu_page(
      'qmean',
			__( 'Settings', 'qmean' ),
			__( 'Settings', 'qmean' ),
			'manage_options',
			'qmean-settings',
			[$this, 'settings_layout']
		);
	}

	/**
	 * Output the admin dashboard layout
	 * containing recorder keywords 
	 */
	public function dashboard_layout()
	{
		$qmreport 		   = new QMeanReport();
		$total_hits  	   = $qmreport->get_hits_total();
		$total_hits  	   = empty($total_hits) ? 0 : $total_hits;
		$total_no_results  = $qmreport->get_no_result_total();
		$search 		   = isset($_GET['qmsearch']) && !empty($_GET['qmsearch']) ? sanitize_text_field($_GET['qmsearch']) : false;
		$total_keywords    = $qmreport->get_keywords_total(['search' => $search]);
		$page 			   = isset($_GET['qmp']) ? intval($_GET['qmp']) : 1;
		$number 		   = 25;
		$sort 			   = isset($_GET['qmsort']) ? sanitize_text_field($_GET['qmsort']) : '';
		$orderby 		   = isset($_GET['qmorder']) ? sanitize_text_field($_GET['qmorder']) : '';
		$orderby 		   = empty($orderby) ? 'desc' : $orderby;

		$order_map 		   = [
		  'kw'   => 'keyword',
		  'ht'   => 'hit',
		  'fp'   => 'found_posts',
		  'time' => 'created'
		];

		if (empty($sort)) {
		 $sortby = 'created';
		} else {
		 $sortby = $order_map[$sort];
		 $sortby = empty($sortby) ? 'created' : $sortby;
		}

		$keywords = $qmreport->get_keywords([
				'search'  => $search,
				'page' 	  => $page,
				'number'  => $number,
				'sort' 	  => $sortby,
				'orderby' => $orderby
		]);

		require_once(QMEAN_PATH.'/templates/dashboard/dashboard.php');
	}

	/**
	 * Output the admin settings page layout
	 * containing the form with all its options
	 */
	public function settings_layout()
	{
		$data = $this->settings;
		require_once(QMEAN_PATH.'/templates/settings.php');
	}

}