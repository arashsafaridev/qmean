<?php

/**
 * QMeanAjax class
 * Handle all AJAX requests
 */
class QMeanAjax
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
	 * Singleton
	 */
	public static $instance = NULL;

	/**
	 * Singleton implementation
	 */
	public static function get_instance()
	{
		NULL === self::$instance and self::$instance = new self;
		return self::$instance;
	}

	/**
	 * Register all ajax action
	 */
	public function __construct()
	{
		add_action( 'wp_ajax_qmean_search', 			  [$this, 'search'] );
		add_action( 'wp_ajax_nopriv_qmean_search', 		  [$this, 'search'] );
		add_action( 'wp_ajax_qmean_save_from_recognizer', [$this, 'save_from_recognizer']);
		add_action( 'wp_ajax_qmean_remove_keyword', 	  [$this, 'remove_keyword']);
		add_action( 'wp_ajax_qmean_get_modal', 			  [$this, 'get_modal'] );

		add_action('wp_ajax_qmean_store_admin_data',  	  [$this, 'store_admin_data']);
	}

	/**
	 * Check request nonce
	 * 
	 * @param 	string 	$_POST['_wpnonce']
	 */
	public function check_nonce()
	{
		if (wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), $this->_nonce ) === false)
		{
			wp_send_json([
				 'status'	   => 'not_found',
				 'suggestions' => []
			], 404);
		}
	}

	/**
	 * Save admin settings data
	 * 
	 * @param 	array 	$_POST
	 */
	public function store_admin_data()
	{
		$this->check_nonce();

		$data = get_option($this->option_name, []);

		$sql_patterner = $data['sql_patterner'];

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
		$data['sql_patterner'] = $sql_patterner;
		update_option($this->option_name, $data, 'no');

		echo esc_html(__('Settings saved successfully!', 'qmean'));
		die();

	}

	/**
	 * Get modal template
	 * 
	 * @param 	string 	$_POST['type'] 		type of modal
	 * @param 	string 	$_POST['keyword'] 	keyword selected
	 */
	public function get_modal()
	{
		// Checks if the request is valid
		$this->check_nonce();

		$type 	 = isset($_POST['type']) 	? sanitize_text_field($_POST['type']) 	 : '';
		$keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';

		ob_start();

		if ($type == 'user-eye') {
			$dyn_fn   = new QMeanFN();
			$settings = get_option($this->option_name);
			$mode 	  = $settings['search_mode'];

			if ($mode == 'word_by_word') {
				$suggestions = $dyn_fn->suggestion_tree($keyword);
			} else {
				$suggestions = $dyn_fn->query_suggestions($keyword);
			}

			require_once(QMEAN_PATH.'/templates/dashboard/modals/user-eye.php');
		}

		$html = ob_get_clean();

		wp_send_json( array('status'=> 'success', 'html' => $html ) );
	}

	/**
	 * Search for suggestions
	 * 
	 * @param 	string 	$_POST['query'] 		the searched query
	 * @param 	string 	$_POST['areas'] 		comma separated areas to search
	 * @param 	string 	$_POST['post_types'] 	comma separated post_types to search
	 */
	public function search()
	{
		// Checks if the request is valid
		$this->check_nonce();

		$query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
		$areas = isset($_POST['areas']) ? sanitize_text_field($_POST['areas']) : '';
		$post_types = isset($_POST['post_types']) ? sanitize_text_field($_POST['post_types']) : '';

		if(empty($query)){
			wp_send_json([
				'status'=> 'not_found','suggestions' => []
			], 404);
		}

		$custom_areas 	   = !empty($areas) ? explode(",", $areas) : [];
		$custom_post_types = !empty($post_types) ? explode(",", $post_types) : [];

		$dyn_fn = new QMeanFN();

		$suggestions = $dyn_fn->query_suggestions($query, '', $custom_areas, $custom_post_types);

		wp_send_json([
			'status'	  => empty($suggestions) ? 'not_found' : 'success',
			'suggestions' => $suggestions
		]);
	}

	/**
	 * Save selector that recognizer detected
	 * 
	 * @param 	string 	$_POST['selector'] 		the CSS selector
	 */
	public function save_from_recognizer()
	{
		// Checks if the request is valid
		$this->check_nonce();

		$selector = isset($_POST['selector']) ? sanitize_text_field($_POST['selector']) : '';

		if(empty($selector)){
			wp_send_json([
				'status'  => 'danger',
				'message' => __('Please select an element','qmean') 
			]);
		}

		$settings = get_option($this->option_name);

		$settings['input_selector'] = $selector;

		update_option('qmean_options',$settings, 'no');

		wp_send_json([
			'status'  => 'success',
			'message' => __('Saved successfully! Please refresh the page to check the selector by typing three characters which you know it will lead to results','qmean'),
			'url' 	  => admin_url('admin.php?page=qmean-settings')
		]);
	}

	/**
	 * Remove keyword from the DB
	 * works on admin dashboard
	 * 
	 * @param 	integer 	$_POST['id'] 		the id of the keyword
	 */
	public function remove_keyword()
	{
		// Checks if the request is valid
		$this->check_nonce();

		$id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : 0;

		if (empty($id)) {
			wp_send_json([
			 'status'  => 'danger',
			 'message' => __('Please select an element','qmean') 
			]);
		}

		$report = new QMeanReport();

		$result = $report->remove_keyword($id);

		wp_send_json([
			'status'  => intval($result) > 0 ? 'success' : 'danger',
			'message' => __('Something went wrong!','qmean')
		] );
	}
}

QMeanAjax::get_instance();
