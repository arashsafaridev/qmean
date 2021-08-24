<?php
// Class QMean - This class creates the option page and add the web app script
class QMean
{

	// The security nonce
	private $_nonce = 'qmean_nonce';

	// The option name
	private $option_name = 'qmean_options';
	private $settings = [];

	// QMean constructor - The main plugin actions registered for WordPress
	public function __construct()
    {
			// Admin page calls
			add_action('admin_menu',                array($this,'add_admin_menu'));
			add_action('wp_ajax_qmean_store_admin_data',  array($this,'store_admin_data'));
			add_action('admin_enqueue_scripts',     array($this,'add_admin_scripts'));
			add_action('wp_enqueue_scripts',     array($this,'add_scripts'));
	}

  public function set_settings($settings)
	{
		$this->settings = $settings;
  }

	 // record every query searched
	public function qmean_analytics(){
		$query = get_query_var('s');
		if($query){
			global $wp_query;
			try {
				$qmreport = new QMeanReport();
				$qmreport->save($query,$wp_query->found_posts);
			} catch (Exception $e) {

			}
		}
		// field auto reconginizer
		if(current_user_can('manage_options') && isset($_GET['qmean_field_recognizer'])){ ?>
			<div class="qmean-field-recognizer-header-tooltip"><?php _e('Please move your mouse and click on your search input field','oodev');?></div>
			<div class="qmean-field-recognizer-tooltip"><?php _e('Please click on your field','oodev');?></div>
		<?php }
	}

	public function qmean_shortcode( $atts ) {
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

	public function qmean_typo_suggestion()
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

	public function qmean_update_plugin(){
		$keyword_table_status = get_option('_qmean_keyword_table','no');
		$mode_column = get_option('_qmean_mode_column','no');
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

	// Returns the saved options data as an array
	public function get_data()
  {
    return get_option($this->option_name, array());
  }

	// saving admin settings data
	public function store_admin_data()
    {
			if (wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), $this->_nonce ) === false)
			{
				die('Invalid Request! Reload your page please.');
			}

		global $wpdb;
		$data = $this->settings;

		foreach ($_POST as $field => $value) {
		    if (substr($field, 0, 6) !== "qmean_")
				continue;

		    if (empty($value))
		        unset($data[$field]);

		    // We remove the qmean_ prefix to clean things up
		    $field = substr($field, 6);

				if(is_array($value)){
					$data[$field] = [];
					foreach ($value as $key => $v) {
						$data[$field][] = sanitize_text_field($v);
					}
				} else {
					$data[$field] = sanitize_text_field($value);
				}

		}
		update_option($this->option_name, $data);

		echo esc_html(__('Settings saved successfully!', 'qmean'));
		die();

	}

	// Adds Admin Scripts for the Ajax call
	public function add_admin_scripts()
    {
			wp_enqueue_script( 'jquery' );
			$screen = get_current_screen();

			if(strpos($screen->id,'qmean')){
				wp_enqueue_style('qmean-settings', QMean_URL. 'assets/css/admin.css', false, QMean_PLUGIN_VERSION);
				wp_enqueue_script('qmean-admin', QMean_URL. 'assets/js/admin.js', array('jquery'), QMean_PLUGIN_VERSION);
			} else if(strpos($screen->id,'qmean-settings')){
				wp_enqueue_style('qmean-settings', QMean_URL. 'assets/css/admin-settings.css', false, QMean_PLUGIN_VERSION);
				wp_enqueue_script('qmean-settings', QMean_URL. 'assets/js/admin-settings.js', array('jquery'), QMean_PLUGIN_VERSION);
			}

			$admin_options = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'_nonce'   => wp_create_nonce( $this->_nonce )
			);

			wp_localize_script('qmean-settings', 'qmean', $admin_options);
			wp_localize_script('qmean-admin', 'qmean', $admin_options);

	}

	// Adds Front-End Scripts for the Ajax call and Styles
	public function add_scripts()
    {
			if(current_user_can('manage_options') && isset($_GET['qmean_field_recognizer'])){
				wp_enqueue_script('qmean-recognizer-script', QMean_URL. 'assets/js/qmean-recognizer.js', array('jquery'), QMean_PLUGIN_VERSION);
			}
			wp_enqueue_style('qmean-style', QMean_URL. 'assets/css/qmean.css', false, QMean_PLUGIN_VERSION);
			wp_enqueue_script('qmean-script', QMean_URL. 'assets/js/qmean.js', array('jquery'), QMean_PLUGIN_VERSION);

			$settings = $this->settings;
			$input_selector = empty($settings['input_selector']) ? '.qmean-shortcode-search-field, #search-form-1' : '.qmean-shortcode-search-field, '.$settings['input_selector'];
			$zindex = !isset($settings['suggestion_zindex']) ? '0' : $settings['suggestion_zindex'];
			$posx = !isset($settings['suggestion_posx']) ? '-' : $settings['suggestion_posx'];
			$posy = !isset($settings['suggestion_posy']) ? '-' : $settings['suggestion_posy'];
			$width = !isset($settings['suggestion_width']) ? '-' : $settings['suggestion_width'];
			$height = !isset($settings['suggestion_height']) ? '-' : $settings['suggestion_height'];
			$parent_position = empty($settings['parent_position']) ? '' : $settings['parent_position'];
			$word_count = empty($settings['word_count']) ? 5 : $settings['word_count'];
			$limit_results = empty($settings['limit_results']) ? 10 : $settings['limit_results'];
			$search_mode = empty($settings['search_mode']) ? '' : $settings['search_mode'];
			$sensitivity = empty($settings['sensitivity']) ? 3 : $settings['sensitivity'];
			$merge_previous_searched = empty($settings['merge_previous_searched']) ? 'yes' : $settings['merge_previous_searched'];
			$keyword_efficiency = empty($settings['keyword_efficiency']) ? 'on' : $settings['keyword_efficiency'];
			$ignore_shortcodes = empty($settings['ignore_shortcodes']) ? 'no' : $settings['ignore_shortcodes'];
			$wrapper_background = empty($settings['wrapper_background']) ? '#f5f5f5' : $settings['wrapper_background'];
			$wrapper_border_radius = empty($settings['wrapper_border_radius']) ? '0px 0px 0px 0px' : $settings['wrapper_border_radius'];
			$wrapper_padding = empty($settings['wrapper_padding']) ? '0px 0px 0px 0px' : $settings['wrapper_padding'];
			$rtl_support = empty($settings['rtl_support']) ? 'no' : $settings['rtl_support'];


			$options = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'homeurl' => get_home_url(),
				'_nonce'   => wp_create_nonce( $this->_nonce ),
				'selector' => '.qmean-shortcode-search-field, '.$settings['input_selector'],
				'submit_after_click' =>$settings['submit_after_click'],
				'zindex' => $zindex,
				'posx' => $posx,
				'posy' => $posy,
				'width' => $width,
				'height' => $height,
				'word_count' => $word_count,
				'limit_results' => $limit_results,
				'parent_position' => $parent_position,
				'search_mode' => $search_mode,
				'sensitivity' => $sensitivity,
				'merge_previous_searched' => $merge_previous_searched,
				'keyword_efficiency' => $keyword_efficiency,
				'ignore_shortcodes' => $ignore_shortcodes,
				'wrapper_background' => $wrapper_background,
				'wrapper_border_radius' => $wrapper_border_radius,
				'wrapper_padding' => $wrapper_padding,
				'rtl_support' => $rtl_support,
				'labels'   => array(
					'loading' => __('Loading ...','qmean'),
					'back' => __('Back','qmean'),
					'notFound' => __('Nothing to suggest! Click to see all the results.','qmean'),
					'pleaseChooseAnInputType' => __('Please choose a field which is an input like input or textarea','qmean'),
					'isNotValid' => __('is not valid!','qmean'),
					'saveSelector' => __('Save Selector','qmean'),
					'yourSelectorIs' => __('Your selector is','qmean')
				),
			);

			wp_localize_script('qmean-script', 'qmean', $options);

	}

	// Adds the QMean label to the WordPress Admin Sidebar Menu
	public function add_admin_menu()
    {
		add_menu_page(
			__( 'QMean', 'qmean' ),
			__( 'QMean', 'qmean' ),
			'manage_options',
			'qmean',
			array($this, 'dashboard_layout'),
			QMean_URL.'/assets/images/qmean-dashicon.svg?v='.QMean_PLUGIN_VERSION,
			2
		);

		add_submenu_page(
      'qmean',
			__( 'Settings', 'qmean' ),
			__( 'Settings', 'qmean' ),
			'manage_options',
			'qmean-settings',
			array($this, 'settings_layout')
		);
	}

	// Get a Dashicon for a given status
  private function get_status_icon($valid)
  {

      return ($valid) ? '<span class="dashicons dashicons-yes success-message"></span>' : '<span class="dashicons dashicons-no-alt error-message"></span>';

  }

	 // Outputs the Admin Dashboard layout containing the form with all its options
   public function dashboard_layout()
   {
		 $qmreport = new QMeanReport();
		 $total = $qmreport->get_keywords_total();
		 $page = isset($_GET['qmp']) ? (int)$_GET['qmp'] : 1;
		 $number = 25;
		 $sort = isset($_GET['qmsort']) ? sanitize_text_field($_GET['qmsort']) : '';
		 $orderby = isset($_GET['qmorder']) ? sanitize_text_field($_GET['qmorder']) : '';
		 $orderby = empty($orderby) ? 'desc' : $orderby;
		 $order_map = array( 'kw' => 'keyword', 'ht' => 'hit', 'fp' => 'found_posts', 'time' => 'created');
		 if(empty($sort)){
			 $sortby = 'created';
		 } else {
			 $sortby = $order_map[$sort];
			 $sortby = empty($sortby) ? 'created' : $sortby;
		 }

		 $keywords = $qmreport->get_keywords(array('page' => $page, 'number' => $number, 'sort' => $sortby, 'orderby' => $orderby));
     require_once(QMean_PATH.'/templates/dashboard/dashboard.php');
   }

   public function settings_layout()
    {
			$data = $this->settings;
      require_once(QMean_PATH.'/templates/settings.php');
		}

}
