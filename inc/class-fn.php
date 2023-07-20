<?php


/**
 * QMeanFN Class
 * QMean main functions
 */
class QMeanFN
{
	/**
	 * QMean option name 
	 */
	private $option_name = 'qmean_options';

	/**
	 * Create regex pattern for both sql & php
	 * 
	 * @param string 	$query 				the query to make pattern of
	 * @param integer 	$match 				defines number of characters in order
	 * @param string 	$mode 				which mode
	 * @param string 	$sql_patterner 		compatible base pattern for sql 
	 */
	public function create_regex_pattern($query = '', $match = 1, $mode = 'phrase', $sql_patterner = '\b(%s)\b')
	{
		// clean spaces
		$query = mb_strtolower($this->clean($query,"trim"));

		if ($query) {
			$sql_regex_pattern = '';
			$php_regex_pattern = '(?i)';

			$len = mb_strlen($query, 'UTF-8');

			$str_splitted = [];
			/**
			 * Create chunks of character based on $match
			 */
			for ($i = 0; $i < $len; $i = $i + $match) {
				$splitted = mb_substr($query, $i, $match, 'UTF-8');
				if(mb_strlen($splitted) < $match) {
					$str_splitted[count($str_splitted) - 1] = $str_splitted[count($str_splitted) - 1] . $splitted;
				} else {
					$str_splitted[] = $splitted;
				}
			}

			/**
			 * Create pattern part
			 */
			foreach ($str_splitted as $key => $sp) {
					$php_regex_pattern .= '(?=[\w ]*'.$sp.')';
					$sql_regex_pattern .= $sp.'.*';
			}
		

			$php_regex_pattern .="\w+";
			$sql_regex_pattern = sprintf(stripslashes($sql_patterner),$sql_regex_pattern);

			return [
				'sql' => $sql_regex_pattern,
				'php' => $php_regex_pattern
			];
		}

		return [];
	}

	/**
	 * Clean the query string
	 * 
	 * @param string 	$query 			the searched query 
	 * @param string 	$type 			how to clean: trim, all  
	 * @param boolean 	$stop_words 	remove stop_words:no, basic, strict
	 */
	private function clean($query, $type = 'trim', $stop_words = false)
	{
		if ($type == 'trim') {
			$query = trim($query," ");
		} else if ($type == 'all') {
			$query = trim(preg_replace('~[\r\n]+~', '', mb_strtolower($query)));
		}

		if ($stop_words != 'no') {
			$query = $this->remove_stopwords($query, $stop_words);
		}

		return $query;
	}

	/**
	 * Remove stop words 
	 * uses QMeanStopWords class
	 * 
	 * @param string 	$text 		text to clean 
	 * @param string 	$mode 		basic or strict  
	 */
	private function remove_stopwords($text, $mode)
	{
		$stop_words_obj = new QMeanStopWords();
		$words = $stop_words_obj->get_default($mode == 'strict' ? true : false);
		
		$filter = new QMeanFilter(null, true);
		$filter->set_words($words);

		return $filter->clean_text($text);
	}

	/**
	 * Main query method
	 * 
	 * @param string 	$query 				the searched query
	 * @param string 	$override_mode  	if set overrides settings mode, used by find_typos method 
	 * 
	 * @since 1.8
	 * @param array 	$custom_areas   	which areas to look for suggestion
	 * @param array 	$custom_post_types  which post types to look for suggestion
	 */
	private function query($query, $override_mode = '', $custom_areas = [], $custom_post_types = [])
	{
		$query 	   				 = $this->clean($query,'all','strict');
		if (empty($query)) return;
		
		$query_len 				 = mb_strlen($query, 'UTF-8');
		$settings  				 = get_option($this->option_name,[]);
		$limit_results 			 = empty($settings['limit_results']) ? 10 : $settings['limit_results'];
		$cut_phrase_limit 		 = empty($settings['cut_phrase_limit']) ? 50 : $settings['cut_phrase_limit'];
		$merge_previous_searched = empty($settings['merge_previous_searched']) ? 'yes' : $settings['merge_previous_searched'];
		$ignore_shortcodes 		 = empty($settings['ignore_shortcodes']) ? 'no' : $settings['ignore_shortcodes'];
		$word_count 			 = empty($settings['word_count']) ? 3 : $settings['word_count'];
		$keyword_efficiency 	 = empty($settings['keyword_efficiency']) ? 'on' : $settings['keyword_efficiency'];
		$remove_stop_words 	 	 = empty($settings['remove_stop_words']) ? 'no' : $settings['remove_stop_words'];
		$sensitivity 			 = mb_strlen($query);
		// echo $regex_exceed;
		$suggestions 			 = [];
		$sql_patterner 			 = $settings['sql_patterner'];

		if (empty($override_mode)) {
			$mode = empty($settings['search_mode']) ? 'word_by_word' : $settings['search_mode'];
		} else {
			$mode = 'word_by_word';
		}

		$regex_exceed 			 = 1; 

		$areas 		= isset($custom_areas) && !empty($custom_areas) ? $custom_areas : $settings['search_area'];
		$post_types = isset($custom_post_types) && !empty($custom_post_types) ? $custom_post_types : $settings['post_types'];

		$post_types_q = '';
		if($post_types){
			$post_types_q = " AND post_type IN ('". implode("','",$post_types) ."')";
		}

		global $wpdb;

		$matches = [];

		/**
		 * If is merge_previous_searched option is on
		 * rank previous searched keywords higher
		 */
		if ($merge_previous_searched == 'yes') {
			$analytics_suggestions = [];

			for ($char_count = $sensitivity; $char_count >= $regex_exceed; $char_count--) {
				$patterns = $this->create_regex_pattern($query, $char_count, $mode, $sql_patterner);

				if($patterns['sql'] == $sql_patterner){
					return [];
				}

				$qmean_table = $wpdb->prefix.'qmean_keyword';
				$sql 		 = "SELECT keyword FROM $qmean_table WHERE found_posts > 0 AND LOWER(keyword) REGEXP %s ORDER BY hit DESC, found_posts DESC";
				$results 	 = $wpdb->get_results(
					$wpdb->prepare($sql,$patterns['sql'])
				);

				if ($results) {
					foreach ($results as $k => $result) {
						if ($mode == 'word_by_word') {
							preg_match('/'.$patterns['php'].'/u',$result->keyword,$matches);
							if ($matches) {
								foreach ($matches as $key => $match) {
									$analytics_suggestions[] = $this->clean($match,'all');
								}
							}
						} else {
							$analytics_suggestions[] = $this->clean($result->keyword, 'all', $remove_stop_words);
						}
					}

					$matches = [];
				}

				$analytics_suggestions = array_unique($analytics_suggestions,SORT_REGULAR);

				if(count($analytics_suggestions) >= $limit_results) break;
			}
		}

		/**
		 * Loop through from high sesivity to low
		 * If the result is enough and reached the limit
		 * It will break to optimize the speed
		 */
		for ($char_count = $sensitivity; $char_count >= $regex_exceed; $char_count--) {
			$patterns = $this->create_regex_pattern($query, $char_count, $mode, $sql_patterner);

			if($patterns['sql'] == $sql_patterner){
				return [];
			}

			if ($areas) {
				foreach ($areas as $area_key => $area) {
					if ('posts_title' == $area) {

						$table = $wpdb->prefix.'posts';
						$sql = "SELECT post_title FROM $table WHERE post_status = 'publish' AND LOWER(post_title) REGEXP %s".$post_types_q;
						$results = $wpdb->get_results(
							$wpdb->prepare($sql,$patterns['sql'])
						);

						if ($results) {
							foreach ($results as $k => $result) {
								if($mode == 'word_by_word'){
									preg_match_all('/'.$patterns['php'].'/u',$result->post_title,$matches);
									if($matches){
										foreach ($matches[0] as $key => $match) {
											$suggestions[] = $this->clean($match,'all');
										}
									}
								} else {
									$suggestions[] = $this->clean($result->post_title, 'all', $remove_stop_words);
								}
							}

							$matches = [];
						}
					} else if('terms' == $area) {
						$table = $wpdb->prefix.'terms';
						$sql = "SELECT name FROM $table WHERE LOWER(name) REGEXP %s";
						$results = $wpdb->get_results(
							$wpdb->prepare($sql,$patterns['sql'])
						);

						if ($results) {
							foreach ($results as $k => $result) {
								if ($mode == 'word_by_word') {
									preg_match_all('/'.$patterns['php'].'/u',$result->name,$matches);
									if ($matches) {
										foreach ($matches[0] as $key => $match) {
											$suggestions[] = $this->clean($match, 'all');
										}
									}
								} else {
									$suggestions[] = $this->clean($result->name, 'all', $remove_stop_words);
								}
							}
							$matches = [];
						}
					} else if ('posts_excerpt' == $area) {
						$table = $wpdb->prefix.'posts';
						$sql = "SELECT post_excerpt FROM $table WHERE post_status = 'publish' AND LOWER(post_excerpt) REGEXP %s".$post_types_q;
						$results = $wpdb->get_results(
							$wpdb->prepare($sql,$patterns['sql'])
						);

						if ($results) {
							foreach ($results as $k => $result) {
								if ($mode == 'word_by_word') {
									$excerpt = strip_tags($result->post_excerpt);
									$excerpt = $ignore_shortcodes == 'yes' ? strip_shortcodes($excerpt) : $excerpt;
									preg_match_all('/'.$patterns['php'].'/u',$excerpt,$matches);
									if($matches){
										foreach ($matches[0] as $key => $match) {
											$suggestions[] = $this->clean($match,'all');
										}
									}
								} else {
									$suggestions[] = $this->clean($result->post_excerpt, 'all', $remove_stop_words);
								}
							}

							$matches = [];
						}
					} else if('posts_content' == $area && $char_count > 1) {
						
						$table = $wpdb->prefix.'posts';
						$sql = "SELECT post_content FROM $table WHERE post_status = 'publish' AND LOWER(post_content) REGEXP %s".$post_types_q;
						
						// get current error reporting level.
						$error_level = error_reporting();
						// diable warnings for huge content.
						// to avoid warning: Timeout exceeded in regulur expression.
						error_reporting(E_ALL ^ E_WARNING);
						// don't log WPDB errors. just for this query
						$wpdb->suppress_errors(true);
						$results = $wpdb->get_results(
							$wpdb->prepare($sql,$patterns['sql'])
						);
						// restore WPDB error logging.
						$wpdb->suppress_errors(false);
						// restore error reporting level.
						error_reporting($error_level);
						
						if ($results) {
							foreach ($results as $k => $result) {
								$content = strip_tags($result->post_content);
								$content = $ignore_shortcodes == 'yes' ? strip_shortcodes($content) : $content;
								if($mode == 'word_by_word'){
									preg_match_all('/'.$patterns['php'].'/u',$content,$matches);
									if($matches){
										foreach ($matches[0] as $key => $match) {
											$suggestions[] = $this->clean($match, 'all');
										}
									}
								} else {
									$phrase = $this->find_the_phrase($content,$patterns['php'],$word_count);
									if($phrase) $suggestions[] = $this->clean($phrase, 'all', $remove_stop_words);
								}
							}
							$matches = [];
						}
					} else if ('posts_metas' == $area) {
						$table = $wpdb->prefix.'postmeta';
						// hides hidden metas which has _ at the begining
						$sql = "SELECT meta_value FROM $table WHERE substring(meta_key, 1, 1) != '_' AND meta_value REGEXP %s";
						$results = $wpdb->get_results(
							$wpdb->prepare($sql,$patterns['sql'])
						);

						if ($results) {
							foreach ($results as $k => $result) {
								if($mode == 'word_by_word'){
									preg_match_all('/'.$patterns['php'].'/u',$result->meta_value,$matches);
									if($matches){
										foreach ($matches[0] as $key => $match) {
											$suggestions[] = $this->clean($match, 'all');
										}
									}
								} else {
									if($result->meta_value) $suggestions[] = $this->clean($result->meta_value, 'all', $remove_stop_words);
								}
							}

							$matches = [];
						}
					}

					$suggestions = array_unique($suggestions,SORT_REGULAR);

					if(count($suggestions) >= $limit_results) break;
				}
			}
		}

		$suggestion_rated = [];

		// if merged is set to yes
		if($merge_previous_searched == 'yes'){
			$db_suggestions = array_diff($suggestions,$analytics_suggestions);
		} else {
			$db_suggestions = $suggestions;
		}

		$suggestions_arr = [];

		if($keyword_efficiency == 'on'){

			// find efficiency
			foreach (array_filter($db_suggestions) as $key => $suggestion) {
				$suggestion_len = mb_strlen($suggestion, 'UTF-8');
				$efficiency = (int)(100 * ($query_len / $suggestion_len));
				$suggestion_rated[] = [
					'q' => $suggestion,
					'e' => $efficiency,
					'l' => $suggestion_len
				];
			}

			// sort by efficiency
			if ($mode != 'word_by_word') {
				usort($suggestion_rated, function ($a, $b) {
					 return $b['e'] - $a['e'];
				});
			}

			if ($merge_previous_searched == 'yes') {
				foreach (array_reverse($analytics_suggestions) as $key => $suggestion) {
					$suggestion_len = mb_strlen($suggestion, 'UTF-8');
					$efficiency = 100;
					array_unshift($suggestion_rated, [
						'q' => $suggestion,
						'e' => $efficiency,
						'l' => $suggestion_len
					]);
				}
			}

			// make sure of sending array instead of an object
			// cut the phrase if it's longer than settings limit
			foreach ($suggestion_rated as $key => $suggestion) {
				$phrased_suggestion = $this->find_the_phrase($suggestion['q'],$patterns['php'], $word_count);
				if ($phrased_suggestion) {
					$suggestions_arr[] = $mode == 'phrase' ? $phrased_suggestion.'&hellip;' : $phrased_suggestion;
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
				if ($phrased_suggestion) {
					$suggestions_arr[] = $mode == 'phrase' ? $phrased_suggestion.'&hellip;' : $phrased_suggestion;
				}
			}

		}

		// limit the results
		$suggestions_arr 	 = array_unique($suggestions_arr, SORT_REGULAR);
		$limited_suggestions = array_slice($suggestions_arr,0, $settings['limit_results']);

		return $limited_suggestions;
	}

	/**
	 * Find the phrase via Google suggest
	 *
	 * @param string $query The query to search for
	 * @return array $suggestions The suggestions
	 */
	public function google_suggest($query) {
		
		$url = 'https://clients1.google.com/complete/search?output=toolbar&hl=en&q='.$query;

		$google_suggestions = [];

		$body = wp_remote_get($url, [
			'headers' => ['User-Agent' => 'PostmanRuntime/7.29.3'],
			'timeout' => 10
		]);

		if ( is_wp_error( $body ) ) {
			return $google_suggestions;
		}

		$code = wp_remote_retrieve_response_code( $body );

		// read xml
		if ( isset( $body['body'] ) && 200 === $code ) {
			$xml = simplexml_load_string( $body['body'] );
			if ( $xml ) {
				$suggestions = $xml->CompleteSuggestion;
				if ( $suggestions ) {
					foreach ( $suggestions as $suggestion ) {
						$google_suggestions[] = (string) $suggestion->suggestion['data'];
					}
				}
				
			}
		}

		return $google_suggestions;
	}

	/**
	 * Public interface of query() method
	 * ajax calls use it
	 */
	public function query_suggestions($query, $override_mode = '', $areas = [], $post_types = []) {
		return $this->query($query, $override_mode, $areas, $post_types);
	}

	/**
	 * find the matched word in a long content
	 * 
	 * @param string $content 		the content to search for the word
	 * @param string $query 		the word
	 */
	private function find_the_word($content,$query) {
		$clean_content = mb_strtolower($content);
		// get the word
		preg_match('/\b\w*'.$query.'\w*\b/u',$clean_content,$matches);

		if ($matches) {
			$the_word = $matches[0];
		}

		return $the_word;
	}

	/**
	 * Find the phrase in a long content
	 * 
	 * @param string $content 		the content to search for the phrase
	 * @param string $pattern 		the pattern to look
	 * @param string $num 			how many words makes a phrase
	 * 
	 * @return string 				the phrase found
	 */
	private function find_the_phrase($content,$pattern,$num = 3) {
		$clean_content = mb_strtolower($content);
		$clean_content = preg_replace("#[[:punct:]]#", "", $clean_content);
		$clean_content = str_replace(["’"],[""], $clean_content);
		// get the word and words after
		preg_match('/(('.$pattern.'))(\b\s\w{2,}+\b){0,'.$num.'}/u',$clean_content,$matches);
		return isset($matches[0]) ? $matches[0] : [];
	}

	/**
	 * Query suggestions for typos specifically
	 * used in did you mean box after search result fails
	 * 
	 * @param string 	$query 		the query searched
	 */
	public function find_typos($query) {
		$settings = get_option( $this->option_name, [] );

		if ( $settings['suggest_engine'] === 'google' ) {
			$suggestions = $this->google_suggest($query);
		} else {
			$suggestions = $this->query($query,'word_by_word');
		}

		return $suggestions;
	}

	/**
	 * if mode is WBW it will get keyword tree by individual word
	 * Dashboard Report
	 * 
	 * @param string $query 		the query to build the tree
	 * @return array $suggestions 	the suggestions found
	 */
	public function suggestion_tree($query) {
		$words = explode(" ",$query);
		$suggestions = array();
		$suggestions['words'] = $words;
		if (count($words) > 0) {
			foreach ($words as $key => $word) {
				$suggestions['suggestions'][$key] = $this->query($word,'word_by_word');
			}
		}
		
		return $suggestions;
	}

}
