<?php

// Class QMeanFN - QMean main functions
class QMeanFN
{


	// The security nonce
	private $option_name = 'qmean_options';

	public function __construct(){}


	// Creates regex pattern for both sql & php
	// $match defines number of characters in order
	public function create_regex_pattern($query = '', $match = 1) {
		// clean spaces
		$query = trim($query," ");
		$keywords = explode(" ",$query);
		if($keywords){
			foreach ($keywords as $index => $keyword) {
				// $sql_regex_pattern = '^('; // begins from each line
				$sql_regex_pattern = '[[:<:]](';
				$php_regex_pattern = '(?i)';
				$len = mb_strlen($keyword, 'UTF-8');
				$str_splitted = [];
				for ($i = 0; $i < $len; $i = $i + $match) {
					$str_splitted[] = mb_substr($keyword, $i, $match, 'UTF-8');
				}
				// $str_splitted = str_split($keyword); // won't work on UTF8
				foreach ($str_splitted as $key => $sp) {
					$php_regex_pattern .= '(?=\w*'.mb_strtolower($sp).')';
					$sql_regex_pattern .= mb_strtolower($sp).'.*';
				}
				$php_regex_pattern .="\w+";
				$sql_regex_pattern = rtrim($sql_regex_pattern,"|").")";
			}
			return array('sql' => $sql_regex_pattern, 'php' => $php_regex_pattern);
		}

		return [];
	}

	// main query method
	private function query($query, $override_mode = '') {

		$query = trim($query," ");
		$query_len = mb_strlen($query, 'UTF-8');
		$settings = get_option($this->option_name,[]);
		$limit_results = empty($settings['limit_results']) ? 10 : $settings['limit_results'];
		$sensitivity = empty($settings['sensitivity']) ? 3 : $settings['sensitivity'];
		$cut_phrase_limit =	empty($settings['cut_phrase_limit']) ? 50 : $settings['cut_phrase_limit'];
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

		for ($char_count = $sensitivity; $char_count >= 1; $char_count--) {
			$patterns = $this->create_regex_pattern($query,$char_count);
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
									preg_match('/'.$patterns['php'].'/u',$result->post_title,$matches);
									if($matches){
										foreach ($matches as $key => $match) {
											$suggestions[] = mb_strtolower($match);
										}
									}
								} else {
									$suggestions[] = mb_strtolower($result->post_title);
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
									preg_match('/'.$patterns['php'].'/u',$result->name,$matches);
									if($matches){
										foreach ($matches as $key => $match) {
											$suggestions[] = mb_strtolower($match);
										}
									}
								} else {
									$suggestions[] = mb_strtolower($result->name);
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
									preg_match('/'.$patterns['php'].'/u',$result->post_excerpt,$matches);
									if($matches){
										foreach ($matches as $key => $match) {
											$suggestions[] = mb_strtolower($match);
										}
									}
								} else {
									$suggestions[] = mb_strtolower($result->post_excerpt);
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
									if($mode == 'word_by_word'){
										preg_match('/'.$patterns['php'].'/u',$result->post_content,$matches);
										if($matches){
											foreach ($matches as $key => $match) {
												$suggestions[] = mb_strtolower($match);
											}
										}
									} else {
										$phrase = $this->find_the_phrase($result->post_content,$query);
										if($phrase) $suggestions[] = mb_strtolower($phrase);
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
											preg_match('/'.$patterns['php'].'/u',$result->meta_value,$matches);
											if($matches){
												foreach ($matches as $key => $match) {
													$suggestions[] = mb_strtolower($match);
												}
											}
										} else {
											if($result->meta_value) $suggestions[] = mb_strtolower($result->meta_value);
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

			// find efficiency
			$suggestion_rated = [];
			foreach ($suggestions as $key => $suggestion) {
				$suggestion_len = mb_strlen($suggestion, 'UTF-8');
				$efficiency = (int)(100 * ($query_len / $suggestion_len));
				$suggestion_rated[] = array('q' => $suggestion, 'e' => $efficiency, 'l' => $suggestion_len);
			}

			// sort by efficiency
			if($mode != 'word_by_word'){
				usort($suggestion_rated, function ($a, $b) { return $b['e'] - $a['e']; });
			}

			// make sure of sending array instead of an object
			// cut the phrase if it's longer than settings limit
			$suggestions_arr = [];
			foreach ($suggestion_rated as $key => $suggestion) {
				if($suggestion['l'] < $cut_phrase_limit){
					$suggestions_arr[] = $suggestion['q'];
				} else {
					$phrase = $this->find_the_phrase($suggestion['q'],$query);
					if($phrase) $suggestions_arr[] = $phrase;
				}
			}

			// limit the results
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
	private function find_the_phrase($content,$query) {
		$clean_content = mb_strtolower($content);
		$phrase = '';

		// get the first word
		preg_match('/\b\w*'.$query.'\w*\b/u',$clean_content,$matches);
		if($matches){
			$the_word = $matches[0];
			// get the second word
			preg_match('/(?<=('.$the_word.'))(\s\w*)/u',$clean_content,$next_matches);
			$next_word = trim($next_matches[0]," ");
			$phrase = $the_word.' '.$next_word;
			// get the third word
			preg_match('/(?<=('.$phrase.'))(\s\w*)/u',$clean_content,$second_next_matches);
			$second_next_word = trim($second_next_matches[0]," ");
			$final_phrase = $phrase.' '.$second_next_word;
		}

		return $final_phrase;
	}

	// Query suggestions for typos specifically - used in did you mean box after search result fails
	public function find_typos($query) {
		$suggestions = $this->query($query,'word_by_word');
		return $suggestions;
	}

}
