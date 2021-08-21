<?php
// Class QMeanAjax
class QMeanAjax
{

	private $_nonce = 'qmean_nonce';
	private $option_name = 'qmean_options';

		public function check_nonce(){
			if (wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), $this->_nonce ) === false)
			{
				wp_send_json( array( 'status'=> 'not_found','suggestions' => [] ) );
			}
		}

		public function search(){
			// Checks if the request is valid
			$this->check_nonce();

			$query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
			if(empty($query)){
				wp_send_json( array( 'status'=> 'not_found','suggestions' => [] ) );
			}
			$dyn_fn = new QMeanFN();
			$suggestions = $dyn_fn->query_suggestions($query);
			wp_send_json( array('status'=> empty($suggestions) ? 'not_found' : 'success','suggestions' => $suggestions ) );
		}

		public function save_from_recognizer(){
			// Checks if the request is valid
			$this->check_nonce();
			$selector = isset($_POST['selector']) ? sanitize_text_field($_POST['selector']) : '';
			if(empty($selector)){
				wp_send_json( array( 'status'=> 'danger','message' => __('Please select an element','qmean') ) );
			}
			$settings = get_option('qmean_options');
			$settings['input_selector'] = $selector;
			update_option('qmean_options',$settings);
			wp_send_json( array('status'=> 'success','message' => __('Saved successfully! Please refresh the page to check the selector by typing three characters which you know it will lead to results','qmean') ) );
		}

		public function remove_keyword(){
			// Checks if the request is valid
			$this->check_nonce();

			$id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : 0;
			if(empty($id)){
				wp_send_json( array( 'status'=> 'danger','message' => __('Please select an element','qmean') ) );
			}
			$report = new QMeanReport();
			$result = $report->remove_keyword($id);
			wp_send_json( array('status'=> (int)$result > 0 ? 'success' : 'danger','message' => __('Something went wrong!','qmean') ) );
		}
}

$ajax = new QMeanAjax();

add_action( 'wp_ajax_qmean_search', array( $ajax, 'search' ) );
add_action( 'wp_ajax_nopriv_qmean_search', array( $ajax, 'search' ) );
add_action( 'wp_ajax_qmean_save_from_recognizer', array( $ajax, 'save_from_recognizer' ) );
add_action( 'wp_ajax_qmean_remove_keyword', array( $ajax, 'remove_keyword' ) );
