<?php
// Class QMean - This class creates the option page and add the web app script
class QMean
{

	// The security nonce
	private $_nonce = 'qmean_nonce';

	// The option name
	private $option_name = 'qmean_options';

	// QMean constructor - The main plugin actions registered for WordPress
	public function __construct()
    {
			// Admin page calls
			add_action('admin_menu',                array($this,'add_admin_menu'));
			add_action('wp_ajax_qmean_store_admin_data',  array($this,'store_admin_data'));
			add_action('admin_enqueue_scripts',     array($this,'add_admin_scripts'));
			add_action('wp_enqueue_scripts',     array($this,'add_scripts'));
	}



	// Returns the saved options data as an array
	private function get_data()
  {
    return get_option($this->option_name, array());
  }
  public function init()
	{
		$settings = $this->get_data();
		if(isset($settings['custom_hook'])){
			add_action( $settings['custom_hook'], 'qmean_typo_suggestion');
		}
  }

	// saving admin settings data
	public function store_admin_data()
    {
			if (wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), $this->_nonce ) === false)
			{
				die('Invalid Request! Reload your page please.');
			}

		global $wpdb;
		$data = $this->get_data();

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

			$settings = get_option('qmean_options');
			$input_selector = empty($settings['input_selector']) ? '.qmean-shortcode-search-field, #search-form-1' : '.qmean-shortcode-search-field, '.$settings['input_selector'];
			$zindex = !isset($settings['suggestion_zindex']) ? '0' : $settings['suggestion_zindex'];
			$posx = !isset($settings['suggestion_posx']) ? '-' : $settings['suggestion_posx'];
			$posy = !isset($settings['suggestion_posy']) ? '-' : $settings['suggestion_posy'];
			$width = !isset($settings['suggestion_width']) ? '-' : $settings['suggestion_width'];
			$height = !isset($settings['suggestion_height']) ? '-' : $settings['suggestion_height'];
			$parent_position = empty($settings['parent_position']) ? '' : $settings['parent_position'];
			$cut_phrase_limit = empty($settings['cut_phrase_limit']) ? 50 : $settings['cut_phrase_limit'];
			$limit_results = empty($settings['limit_results']) ? 10 : $settings['limit_results'];
			$search_mode = empty($settings['search_mode']) ? '' : $settings['search_mode'];
			$sensitivity = empty($settings['sensitivity']) ? 3 : $settings['sensitivity'];
			$merge_previous_searched = empty($settings['merge_previous_searched']) ? 'yes' : $settings['merge_previous_searched'];
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
				'cut_phrase_limit' => $cut_phrase_limit,
				'limit_results' => $limit_results,
				'parent_position' => $parent_position,
				'search_mode' => $search_mode,
				'sensitivity' => $sensitivity,
				'merge_previous_searched' => $merge_previous_searched,
				'wrapper_background' => $wrapper_background,
				'wrapper_border_radius' => $wrapper_border_radius,
				'wrapper_padding' => $wrapper_padding,
				'rtl_support' => $rtl_support,
				'labels'   => array(
					'loading' => __('Loading ...','qmean'),
					'back' => __('Back','qmean'),
					'notFound' => __('Not Found!','qmean'),
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
		 $page = (int)$_GET['qmp'] ? (int)$_GET['qmp'] : 1;
		 $number = 10;
		 $sort = sanitize_text_field($_GET['qmsort']);
		 $orderby = sanitize_text_field($_GET['qmorder']);
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
			$data = $this->get_data();
      require_once(QMean_PATH.'/templates/settings.php');
		}

}
