<?php
// Class QMeanAjax
class QMeanAjax
{

	private $_nonce = 'qmean_nonce';
	private $option_name = 'qmean_options';

	public function __construct(){

	}

	public function qmean_search(){

		// Checks if the request is valid
		if (wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), $this->_nonce ) === false)
		{
			wp_send_json( array( 'status'=> 'not_found','suggestions' => [] ) );
		}

		$query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
		if(empty($query)){
			wp_send_json( array( 'status'=> 'not_found','suggestions' => [] ) );
		}
		$dyn_fn = new QMeanFN();
		$suggestions = $dyn_fn->query_suggestions($query);
		wp_send_json( array('status'=> empty($suggestions) ? 'not_found' : 'success','suggestions' => $suggestions ) );
		}
}

$ajax = new QMeanAjax();

add_action( 'wp_ajax_qmean_search', array( $ajax, 'qmean_search' ) );
add_action( 'wp_ajax_nopriv_qmean_search', array( $ajax, 'qmean_search' ) );
