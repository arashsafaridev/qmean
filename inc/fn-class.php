<?php

// Class QMeanFN - QMean main functions
class QMeanFN
{


	// The security nonce
	private $option_name = 'qmean_options';

	public function __construct(){}


	// Creates regex pattern for both sql & php
	// $match defines number of characters in order
	public function create_regex_pattern($query = '', $match = 1, $mode = 'phrase') {
		// clean spaces
		$query = mb_strtolower(trim($query," "));
		if($query){
				$sql_regex_pattern = '[[:<:]](';
				$php_regex_pattern = '(?i)';
				$len = mb_strlen($query, 'UTF-8');
				$str_splitted = [];
				for ($i = 0; $i < $len; $i = $i + $match) {
					$str_splitted[] = mb_substr($query, $i, $match, 'UTF-8');
				}
				foreach ($str_splitted as $key => $sp) {
						$php_regex_pattern .= '(?=[\w ]*'.$sp.')';
						$sql_regex_pattern .= $sp.'.*';
				}
				$php_regex_pattern .="\w+";
				$sql_regex_pattern .= ")";
				return array('sql' => $sql_regex_pattern, 'php' => $php_regex_pattern);
		}
		return array();
	}

	private function clean($query, $type='trim') {
		if($type == 'trim') {
			$query = trim($query," ");
		} else if($type == 'all') {
			$query = trim(preg_replace('~[\r\n]+~', '', mb_strtolower($query)));
		}
		return $query;
	}
	// main query method
	private function query($query, $override_mode = '') {
		$query = $this->clean($query,'trim');
		$query_len = mb_strlen($query, 'UTF-8');
		$settings = get_option($this->option_name,[]);
		$limit_results = empty($settings['limit_results']) ? 10 : $settings['limit_results'];
		$sensitivity = empty($settings['sensitivity']) ? 3 : $settings['sensitivity'];
		$cut_phrase_limit =	empty($settings['cut_phrase_limit']) ? 50 : $settings['cut_phrase_limit'];
		$merge_previous_searched =	empty($settings['merge_previous_searched']) ? 'yes' : $settings['merge_previous_searched'];
		$ignore_shortcodes =	empty($settings['ignore_shortcodes']) ? 'no' : $settings['ignore_shortcodes'];
		$word_count =	empty($settings['word_count']) ? 3 : $settings['word_count'];
		$keyword_efficiency =	empty($settings['keyword_efficiency']) ? 'on' : $settings['keyword_efficiency'];
		$suggestions = [];
		if(empty($override_mode)){
			$mode = empty($settings['search_mode']) ? 'word_by_word' : $settings['search_mode'];
		} else {
			$mode = 'word_by_word';
		}
		$areas = $settings['search_area'];
		$post_types = $settings['post_types'];

		$post_types_q = '';
		if($post_types){
			$post_types_q = " AND post_type IN ('". implode("','",$post_types) ."')";
		}

		global $wpdb;
		$matches = [];

		// if count analytics results too
		if($merge_previous_searched == 'yes'){
			$analytics_suggestions = [];
			for ($char_count = $sensitivity; $char_count >= 1; $char_count--) {
				$patterns = $this->create_regex_pattern($query,$char_count,$mode);
				if($patterns['sql'] == '[[:<:]]()'){
					return [];
				}
				$qmean_table = $wpdb->prefix.'qmean_keyword';
				$sql = "SELECT keyword FROM $qmean_table WHERE found_posts > 0 AND LOWER(keyword) REGEXP %s ORDER BY hit DESC, found_posts DESC";
				$results = $wpdb->get_results(
					$wpdb->prepare($sql,$patterns['sql'])
				);
				if($results){
					foreach ($results as $k => $result) {
						if($mode == 'word_by_word'){
							preg_match('/'.$patterns['php'].'/u',$result->keyword,$matches);
							if($matches){
								foreach ($matches as $key => $match) {
									$analytics_suggestions[] = $this->clean($match,'all');
								}
							}
						} else {
							$analytics_suggestions[] = $this->clean($result->keyword,'all');
						}
					}
					$matches = [];
				}
				$analytics_suggestions = array_unique($analytics_suggestions,SORT_REGULAR);
				if(count($analytics_suggestions) >= $limit_results) break;
			}
		}

		// print_r($suggestions);
		for ($char_count = $sensitivity; $char_count >= 1; $char_count--) {
			$patterns = $this->create_regex_pattern($query,$char_count,$mode);
			if($patterns['sql'] == '[[:<:]]()'){
				return [];
			}
			if($areas){
				foreach ($areas as $area_key => $area) {
					if('posts_title' == $area){
						$table = $wpdb->prefix.'posts';
						$sql = "SELECT post_title FROM $table WHERE post_status = 'publish' AND LOWER(post_title) REGEXP %s".$post_types_q;
						$results = $wpdb->get_results(
							$wpdb->prepare($sql,$patterns['sql'])
						);
						if($results){
							foreach ($results as $k => $result) {
								if($mode == 'word_by_word'){
									preg_match_all('/'.$patterns['php'].'/u',$result->post_title,$matches);
									if($matches){
										foreach ($matches[0] as $key => $match) {
											$suggestions[] = $this->clean($match,'all');
										}
									}
								} else {
									$suggestions[] = $this->clean($result->post_title,'all');
								}
							}
							$matches = [];
						}
					} else if('terms' == $area){
						$table = $wpdb->prefix.'terms';
						$sql = "SELECT name FROM $table WHERE LOWER(name) REGEXP %s";
						$results = $wpdb->get_results(
							$wpdb->prepare($sql,$patterns['sql'])
						);
						if($results){
							foreach ($results as $k => $result) {
								if($mode == 'word_by_word'){
									preg_match_all('/'.$patterns['php'].'/u',$result->name,$matches);
									if($matches){
										foreach ($matches[0] as $key => $match) {
											$suggestions[] = $this->clean($match,'all');
										}
									}
								} else {
									$suggestions[] = $this->clean($result->name,'all');
								}
							}
							$matches = [];
						}
					} else if('posts_excerpt' == $area){
						$table = $wpdb->prefix.'posts';
						$sql = "SELECT post_excerpt FROM $table WHERE post_status = 'publish' AND LOWER(post_excerpt) REGEXP %s".$post_types_q;
						$results = $wpdb->get_results(
							$wpdb->prepare($sql,$patterns['sql'])
						);
						if($results){
							foreach ($results as $k => $result) {
								if($mode == 'word_by_word'){
									$excerpt = strip_tags($result->post_excerpt);
									$excerpt = $ignore_shortcodes == 'yes' ? strip_shortcodes($excerpt) : $excerpt;
									preg_match_all('/'.$patterns['php'].'/u',$excerpt,$matches);
									if($matches){
										foreach ($matches[0] as $key => $match) {
											$suggestions[] = $this->clean($match,'all');
										}
									}
								} else {
									$suggestions[] = $this->clean($result->post_excerpt,'all');
								}
							}
							$matches = [];
						}
					} else if('posts_content' == $area){
							$table = $wpdb->prefix.'posts';
							$sql = "SELECT post_content FROM $table WHERE post_status = 'publish' AND LOWER(post_content) REGEXP %s".$post_types_q;
							$results = $wpdb->get_results(
								$wpdb->prepare($sql,$patterns['sql'])
							);
							if($results){
								foreach ($results as $k => $result) {
									$content = strip_tags($result->post_content);
									$content = $ignore_shortcodes == 'yes' ? strip_shortcodes($content) : $content;
									if($mode == 'word_by_word'){
										preg_match_all('/'.$patterns['php'].'/u',$content,$matches);
										if($matches){
											foreach ($matches[0] as $key => $match) {
												$suggestions[] = $this->clean($match,'all');
											}
										}
									} else {
										$phrase = $this->find_the_phrase($content,$patterns['php'],$word_count);
										if($phrase) $suggestions[] = $this->clean($phrase,'all');
									}
								}
								$matches = [];
							}
						} else if('posts_metas' == $area){
								$table = $wpdb->prefix.'postmeta';
								// hides hidden metas which has _ at the begining
								$sql = "SELECT meta_value FROM $table WHERE substring(meta_key, 1, 1) != '_' AND meta_value REGEXP %s";
								$results = $wpdb->get_results(
									$wpdb->prepare($sql,$patterns['sql'])
								);
								if($results){
									foreach ($results as $k => $result) {
										if($mode == 'word_by_word'){
											preg_match_all('/'.$patterns['php'].'/u',$result->meta_value,$matches);
											if($matches){
												foreach ($matches[0] as $key => $match) {
													$suggestions[] = $this->clean($match,'all');
												}
											}
										} else {
											if($result->meta_value) $suggestions[] = $this->clean($result->meta_value,'all');
										}
									}
									$matches = [];
								}
							}
					}
				}
				$suggestions = array_unique($suggestions,SORT_REGULAR);
				if(count($suggestions) >= $limit_results) break;
		}
			$suggestion_rated = [];
			// merged is set to yes
			if($merge_previous_searched == 'yes'){
				$db_suggestions = array_diff($suggestions,$analytics_suggestions);
			} else {
				$db_suggestions = $suggestions;
			}

			$suggestions_arr = array();
			if($keyword_efficiency == 'on'){

				// find efficiency
				foreach ($db_suggestions as $key => $suggestion) {
					$suggestion_len = mb_strlen($suggestion, 'UTF-8');
					$efficiency = (int)(100 * ($query_len / $suggestion_len));
					$suggestion_rated[] = array('q' => $suggestion, 'e' => $efficiency, 'l' => $suggestion_len);
				}

				// sort by efficiency
				if($mode != 'word_by_word'){
					usort($suggestion_rated, function ($a, $b) { return $b['e'] - $a['e']; });
				}

				if($merge_previous_searched == 'yes'){
					foreach (array_reverse($analytics_suggestions) as $key => $suggestion) {
						$suggestion_len = mb_strlen($suggestion, 'UTF-8');
						$efficiency = 100;
						array_unshift($suggestion_rated, array('q' => $suggestion, 'e' => $efficiency, 'l' => $suggestion_len));
					}
				}

				// make sure of sending array instead of an object
				// cut the phrase if it's longer than settings limit
				foreach ($suggestion_rated as $key => $suggestion) {
					$phrased_suggestion = $this->find_the_phrase($suggestion['q'],$patterns['php'],$word_count);
					if($phrased_suggestion){
						$suggestions_arr[] = $phrased_suggestion;
					}
				}
			} else {
				if($merge_previous_searched == 'yes'){
					foreach (array_reverse($analytics_suggestions) as $key => $suggestion) {
						array_unshift($db_suggestions, $suggestion);
					}
				}
				// make sure of sending array instead of an object
				// cut the phrase if it's longer than settings limit
				foreach ($db_suggestions as $key => $suggestion) {
					$phrased_suggestion = $this->find_the_phrase($suggestion,$patterns['php'],$word_count);
					if($phrased_suggestion){
						$suggestions_arr[] = $phrased_suggestion;
					}
				}

			}

			// limit the results
			$suggestions_arr = array_unique($suggestions_arr,SORT_REGULAR);
			$limited_suggestions = array_slice($suggestions_arr,0,$settings['limit_results']);
			return $limited_suggestions;
	}

	// main query method - for public use on ajax calls
	public function query_suggestions($query, $override_mode = '') {
		return $this->query($query, $override_mode);
	}

	// find the matched word in a long content
	private function find_the_word($content,$query) {
		$clean_content = mb_strtolower($content);
		// get the word
		preg_match('/\b\w*'.$query.'\w*\b/u',$clean_content,$matches);
		if($matches){
			$the_word = $matches[0];
		}

		return $the_word;
	}

	// find the phrase in a long content
	private function find_the_phrase($content,$pattern,$num = 3) {
		$clean_content = mb_strtolower($content);
		// get the word and words after
		preg_match('/(('.$pattern.'))(\b\s\w{2,}+\b){0,'.$num.'}/u',$clean_content,$matches);
		return $matches[0];
	}

	// Query suggestions for typos specifically - used in did you mean box after search result fails
	public function find_typos($query) {
		$suggestions = $this->query($query,'word_by_word');
		return $suggestions;
	}

	// if mode is WBW it will get keyword tree by individual word - Dashboard Report
	public function suggestion_tree($query) {
		$words = explode(" ",$query);
		$suggestions = array();
		$suggestions['words'] = $words;
		if(count($words) > 0){
			foreach ($words as $key => $word) {
				$suggestions['suggestions'][$key] = $this->query($word,'word_by_word');
			}
		}
		return $suggestions;
	}

}
