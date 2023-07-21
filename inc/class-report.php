<?php
// Class QMean - This class creates the option page and add the web app script
class QMeanReport
{

	// QMean constructor - The main plugin actions registered for WordPress
	public function __construct()
	{

	}

	// cleans the query from wierd characters and spaces plus make them lower case
	private function clean($query)
	{
	$query = trim($query," ");
	$query = sanitize_text_field($query);
	$query = mb_strtolower($query);

	return $query;
	}

	private function add($args)
	{
		global $wpdb;
		$table = $wpdb->prefix . "qmean_keyword";
		$wpdb->insert($table,$args);
	}

	private function update($query,$args)
	{
		global $wpdb;
		$table = $wpdb->prefix . "qmean_keyword";
		$wpdb->update($table,$args,array('keyword' => $query));
	}

	private function get($query)
	{
		global $wpdb;
		$table = $wpdb->prefix . "qmean_keyword";
		$sql = $wpdb->prepare("SELECT * FROM $table WHERE keyword = %s",array($query));
		$row = $wpdb->get_row($sql);
		return $row;
	}

    public function save($query,$found_posts = 0)
	{
		$query 	 = $this->clean($query);
		// get if already existed
		$keyword = $this->get($query);
		$args 	 = [];
		$args['keyword'] = $query;
		if($keyword){
				$this->update($query,[
					'hit' 				=> $keyword->hit + 1,
					'found_posts' => $found_posts
				]);

				return true;
		} else {
				$args['hit'] = 1;
				$args['created'] = time();
		}

		$args['found_posts'] = $found_posts;

		$this->add($args);
  	}

	// gets all keywords for dashboard report
	public function get_keywords($args)
	{
		global $wpdb;
		$table = $wpdb->prefix . "qmean_keyword";
		$page = $args['page'];
		$number = $args['number'];
		$from = $page == 1 ? 0 : ($page - 1) * $number;
		$sort = $args['sort'];
		$orderby = $args['orderby'] == 'asc' ? 'ASC' : 'DESC';
		if ($args['search']) {
			$search = $args['search'];
			$sql = $wpdb->prepare("SELECT * FROM $table WHERE keyword LIKE '%%%s%%' ORDER BY $sort $orderby LIMIT %d,%d",[$search, $from, $number]);
		} else {
			$sql = $wpdb->prepare("SELECT * FROM $table ORDER BY $sort $orderby LIMIT %d,%d",[$from, $number]);
		}
		$results = $wpdb->get_results($sql);
		
		return $results;
	}

	public function remove_keyword($id)
	{
		global $wpdb;
		$table = $wpdb->prefix . "qmean_keyword";
		$result = $wpdb->delete($table,['id' => $id]);
		return $result;
	}

	// gets total keywords recorded for dashboard and pagination
	public function get_keywords_total($args = [])
	{
		global $wpdb;
		$table = $wpdb->prefix . "qmean_keyword";
		if ($args['search']) {
			$sql = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE keyword LIKE '%%%s%%'", [$args['search']]);

		} else {
			$sql = "SELECT COUNT(*) FROM $table";
		}
		$total = $wpdb->get_var($sql);
		return $total;
	}
	public function get_hits_total()
	{
		global $wpdb;
		$table = $wpdb->prefix . "qmean_keyword";
		$sql = "SELECT SUM(hit) FROM $table";
		$total = $wpdb->get_var($sql);
		return $total;
	}
	public function get_no_result_total()
	{
		global $wpdb;
		$table = $wpdb->prefix . "qmean_keyword";
		$sql = "SELECT COUNT(*) FROM $table WHERE found_posts = 0";
		$total = $wpdb->get_var($sql);
		return $total;
	}
}
