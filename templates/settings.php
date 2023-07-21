<?php
  $settings = get_option('qmean_options');
  $post_types = empty($settings['post_types']) ? [] : $settings['post_types'];
  $search_areas = empty($settings['search_area']) ? [] : $settings['search_area'];
  $custom_hook = isset($settings['custom_hook']) ? $settings['custom_hook'] : '';
?>
<div class="wrap">
  <div id="qmean-settings">
    <form method="post" id="qmean-admin-form">
      <input type="hidden" name="action" id="qmean-action" value="qmean_store_admin_data" />
      <input type="hidden" name="_wpnonce" id="qmean-security" value="" />
      <input type="hidden" name="qmean_saved_by_user" id="qmean_saved_by_user" value="1" />
        <div class="inside qmean-settings">
            <div class="postbox">
              <img class="qmean-settings-logo" width="200" src="<?php echo QMEAN_URL;?>assets/images/qmean-logo.svg"/>
              <h1><?php _e('Settings','qmean');?></h1>
              <hr/>
              <div class="qmean-settings-notification"></div>
              
              <h2><?php _e('Essential Setup','qmean');?></h2>
                <table class="form-table">
                  <tr valign="top">
                      <th scope="row"><?php _e('Suggest Engine','qmean');?>
                          <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_suggest_engine"></i>
                      </th>
                      <td>
                      <select name="qmean_suggest_engine" id="qmean_suggest_engine">
                        <option<?php echo ($settings['suggest_engine'] == 'google') ? ' selected="selected"' : '';?> value="google"><?php _e('Google','qmean');?></option>
                        <option<?php echo ($settings['suggest_engine'] == 'qmean') ? ' selected="selected"' : '';?> value="qmean"><?php _e('QMean','qmean');?></option>
                      </select>
                        <div id="qmean_suggest_engine_help" class="qmean-settings-help">
                          <h3><?php _e('Search Engine','qmean');?></h3>
                          <p><?php _e('You can set your suggest engine. Google is super fast, but might have irrelevant suggestions. QMean might be slower with more relevant suggestions. You can use <b>search mode</b> option to get the most out of every engine.','qmean');?>
                        </p>
                        </div>
                          
                      </td>
                    </tr>
                    <tr valign="top">
                    <th scope="row">
                        <?php _e('Block Enabled','qmean');?>
                        <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_block_enabled"></i>
                    </th>
                    <td>
                      <p id="qmean_block_enabled"><?php _e('Since version 1.9, QMean supports blocks. You can use WordPress core block, <b>search</b>, to enable suggestions. Additionally, we added <b>Did You Mean</b> block to use it as a placeholder anywhere.','qmean');?></p>
                      <p id="qmean_block_enabled"><?php _e('You can still use any CSS selector and QMean auto recognizer. These options are now available in <b>Advance Setup</b> tab.','qmean');?></p>
                      <div id="qmean_block_enabled_help" class="qmean-settings-help">
                        <h3><?php _e('QMean Blocks','qmean');?></h3>
                        <p>
                          <?php _e('We added <b>Did You Mean</b> block and customized WP\'s search core block.','qmean');?>
                        </p>
                        <p><?php _e('Did You Mean block is a placeholder of the suggestions when no result found. You can add it anywhere like a normal block and style it as you want.','qmean');?>
                        </p>
                        <p>
                          <?php _e('Since version 1.9, <b>WP search</b> block has <b>Qmean Settings</b> when adding search block. You can enable it and config it as you see fit.','qmean');?>
                        </p>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <?php _e('Search Mode','qmean');?>
                        <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_search_mode"></i>
                      </th>
                    <td>
                      <select name="qmean_search_mode" id="qmean_search_mode">
                        <option<?php echo ($settings['search_mode'] == 'word_by_word') ? ' selected="selected"' : '';?> value="word_by_word"><?php _e('Word By Word','qmean');?></option>
                        <option<?php echo ($settings['search_mode'] == 'phrase') ? ' selected="selected"' : '';?> value="phrase"><?php _e('Phrase','qmean');?></option>
                      </select>
                      <div id="qmean_search_mode_help" class="qmean-settings-help">
                        <h3><?php _e('Search Mode','qmean');?></h3>
                        <p><?php _e('Word by word will compelete the search query on every word, individually, but phrase mode will get the phrase containing the word. Word by word may produce keyword combinations that won\'t match to any result but can help the SEO by reproducing them','qmean');?></p>
                      </div>
                      
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <?php _e('RTL Support','qmean');?>
                      <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_rtl_support"></i>
                    </th>
                    <td>
                      <select name="qmean_rtl_support" id="qmean_rtl_support">
                        <option<?php echo ($settings['rtl_support'] == 'no') ? ' selected="selected"' : '';?> value="no"><?php _e('No','qmean');?></option>
                        <option<?php echo ($settings['rtl_support'] == 'yes') ? ' selected="selected"' : '';?> value="yes"><?php _e('Yes','qmean');?></option>
                      </select>
                      <div id="qmean_rtl_support_help" class="qmean-settings-help">
                        <h3><?php _e('RTL Support','qmean');?></h3>
                        <p><?php _e('Instead of using left for positioning it will use right instead','qmean');?></p>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <?php _e('Submit After Click','qmean');?>
                      <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_submit_after_click"></i>
                    </th>
                    <td>
                      <select name="qmean_submit_after_click" id="qmean_submit_after_click">
                        <option<?php echo ($settings['submit_after_click'] == 'no') ? ' selected="selected"' : '';?> value="no"><?php _e('No','qmean');?></option>
                        <option<?php echo ($settings['submit_after_click'] == 'yes') ? ' selected="selected"' : '';?> value="yes"><?php _e('Yes','qmean');?></option>
                      </select>
                      <div id="qmean_submit_after_click_help" class="qmean-settings-help">
                        <h3><?php _e('RTL Support','qmean');?></h3>
                        <p><?php _e('Submit the form after clicking on suggestion item','qmean');?></p>
                      </div>
                      
                    </td>
                  </tr>
                </table>
                <hr/>
                <h2 class="qmean-hint-toggler"><?php _e('Suggestion Box Styles','qmean');?></h2>
                <table class="form-table qmean-hint-toggle-wrapper">
                  
                <tr valign="top">
                    <th scope="row">
                      <?php _e('Auto Set Parent Position','qmean');?>
                        <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_parent_position"></i>
                      </th>
                    <td>
                      <select name="qmean_parent_position" id="qmean_parent_position">
                        <option value=""><?php _e('Do Nothing!');?></option>
                        <option<?php echo ($settings['parent_position'] == 'relative') ? ' selected="selected"' : '';?> value="relative"><?php _e('Relative','qmean');?></option>
                        <option<?php echo ($settings['parent_position'] == 'absolute') ? ' selected="selected"' : '';?> value="absolute"><?php _e('Absolute','qmean');?></option>
                        <option<?php echo ($settings['parent_position'] == 'fixed') ? ' selected="selected"' : '';?> value="fixed"><?php _e('Fixed','qmean');?></option>
                      </select>
                      <div id="qmean_parent_position_help" class="qmean-settings-help">
                        <h3><?php _e('Auto Set Parent Position','qmean');?></h3>
                      <p><?php _e('<strong>IMPORTANT!</strong> Input parent\'s postion needs to be set for suggestion wrapper to appear correctly. If the parent doesn\'t have any position this can automatically add it. Relative is common and won\'t harm','qmean');?></p>
                    </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <?php _e('Suggestions Positioning','qmean');?>
                      <i id="qmean_positioning" class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_positioning"></i>
                    </th>
                    <td class="qmean-positioning">
                      <div class="qmean-positioning-fields">
                        <div>
                          <label for="qmean_suggestion_zindex"><?php _e('CSS z-index','qmean');?></label>
                          <input type="text" id="qmean_suggestion_zindex" name="qmean_suggestion_zindex" value="<?php echo esc_attr( $settings['suggestion_zindex'] ); ?>"/>
                        </div>
                        <div>
                          <label for="qmean_suggestion_posx"><?php _e('CSS left','qmean');?></label>
                          <input type="text" id="qmean_suggestion_posx" name="qmean_suggestion_posx" value="<?php echo esc_attr( $settings['suggestion_posx'] ); ?>"/>
                        </div>
                        <div>
                          <label for="qmean_suggestion_posy"><?php _e('CSS Top','qmean');?></label>
                          <input type="text" id="qmean_suggestion_posy" name="qmean_suggestion_posy" value="<?php echo esc_attr( $settings['suggestion_posy'] ); ?>"/>
                        </div>
                      </div>
                      <div class="qmean-positioning-fields">
                        <div>
                          <label for="qmean_suggestion_width"><?php _e('Width','qmean');?></label>
                          <input type="text" id="qmean_suggestion_width" name="qmean_suggestion_width" value="<?php echo esc_attr( $settings['suggestion_width'] ); ?>"/>
                        </div>
                        <div>
                          <label for="qmean_suggestion_height"><?php _e('Height','qmean');?></label>
                          <input type="text" id="qmean_suggestion_height" name="qmean_suggestion_height" value="<?php echo esc_attr( $settings['suggestion_height'] ); ?>"/>
                        </div>
                      </div>
                      <div id="qmean_positioning_help" class="qmean-settings-help">
                        <h3><?php _e('Suggestion Result Positioning','qmean');?></h3>
                        <p><?php _e('Use units too like 50px, 5%, 5rem or any CSS standard unit; Please make sure that the parent of the input has a position of relative or absolute. Use - (dash) to automate each value','qmean');?></p>
                        <p><?php _e('If RTL support is enabled, the <b>left</b> value will act as <b>right</b> attribute.','qmean');?></p>
                        <p class="qmean-info"><?php _e('You can also use <code>#qmean-suggestion-results</code> selector for suggestion wrapper and <code>.qmean-suggestion-item</code> for suggestion item in your CSS file for better styling. Use <code>.qmean-typo-suggestion</code> for DidYouMean box after the search.','qmean');?></p>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <?php _e('Suggestions Styles','qmean');?>
                        <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_styling"></i>
                      </th>
                    <td class="qmean-styling" id="qmean_styling">
                    <div class="qmean-styling-fields">
                      <div>
                        <label for="qmean_wrapper_background"><?php _e('Background Color','qmean');?></label>
                        <input type="text" id="qmean_wrapper_background" name="qmean_wrapper_background" value="<?php echo esc_attr( $settings['wrapper_background'] ); ?>"/>
                      </div>
                      <div>
                        <label for="qmean_wrapper_border_radius"><?php _e('Border Radius','qmean');?></label>
                        <input type="text" id="qmean_wrapper_border_radius" name="qmean_wrapper_border_radius" value="<?php echo esc_attr( $settings['wrapper_border_radius'] ); ?>"/>
                      </div>
                      <div>
                        <label for="qmean_wrapper_padding"><?php _e('Padding','qmean');?></label>
                        <input type="text" id="qmean_wrapper_padding" name="qmean_wrapper_padding" value="<?php echo esc_attr( $settings['wrapper_padding'] ); ?>"/>
                      </div>
                    </div>
                      <div id="qmean_styling_help" class="qmean-settings-help">
                        <h3><?php _e('Suggestion Wrapper Styling','qmean');?></h3>
                        <p><?php _e('Enter standard CSS values like color #ffffff, border radius 0px 0px 0px 0px, padding 0px 0px 0px 0px. Any standard CSS unit is allowed like em, rem, % and  ... . order of values is: top right bottom left. Seperate with space.','qmean');?></p>
                      </div>
                    </td>
                  </tr>
                </table>
                <hr/>
                <h2 class="qmean-hint-toggler"><?php _e('Shortcodes','qmean');?></h2>
                <table class="form-table qmean-hint-toggle-wrapper">
                  <tr valign="top">
                    <th scope="row"><?php _e('QMean Shortcode','qmean');?></th>
                    <td>
                      <code>
                        [qmean icon="yes" placeholder="Type to search" button_bg="#1a1a1a" button_height="40px" button_width="40px" form_class="" input_class="" button_class=""  form_style="" input_style="" button_style=""]
                      </code>
                      <p><?php _e('Instead of selector (above solution) you can use the shortcode.','qmean');?></p>
                      <div class="qmean-info">
                        <h3 class="qmean-hint-toggler"><?php _e('How to use, what are the options? and best practices','qmean');?></h3>
                        <div class="qmean-hint-toggle-wrapper">
                          <ul class="">
                            <li><?php _e('Use just <code>[qmean]</code> anywhere you want; Post/Page contents, Widgets and ...','qmean');?></li>
                            <li><?php _e('You can use these options to customize:','qmean');?></li>
                            <li>
                              <pre>
          areas: comma separated areas: posts_title, posts_excerpt, posts_content, terms, posts_metas
          post_types: comma separated post_types
          icon: yes, no
          placeholder: any text you want
          button_bg: CSS color like #1a1a1a
          button_height: number with unit like 40px, 3rem, 20% or ...
          button_width: number with unit like 40px, 3rem, 20% or ...
          button_text: text of the button
          form_class: any class, sperate with spaces like "form-wrapper form-contact"
          input_class: any class, sperate with spaces like "form-control form-control-sm"
          button_class: any class, sperate with spaces like "btn btn-sm"
          form_style: CSS stlye like "padding: 0 0 15px 0; background-color:#1a1a1a"
          input_style: CSS stlye like "padding: 0 0 15px 0; background-color:#1a1a1a"
          button_style: CSS stlye like "padding: 0 0 15px 0; background-color:#1a1a1a"
                              </pre>
                            </li>
                            <li><h4><?php _e('Best practices:','qmean');?></h4></li>
                            <li><?php _e('Use specific named attribute if you don\'t know how to use CSS; like button_height, button_bg and ...','qmean');?></li>
                            <li><?php _e('Use in set! Either use style attributes for all of them or use class attribute for all of them; like form_class, input_class, button_class OR form_style, input_style and button_style!','qmean');?></li>
                            <li><?php _e('* You can use all of them but it won\'t make sense in general and it might get confusing :)','qmean');?></li>
                          </ul>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row"><?php _e('Did You Mean Shortcode','qmean');?></th>
                    <td>
                      <code>
                        [qmean-dym wrapper_class=""]
                      </code>
                      <p><?php _e('Use this shortcode in the search page if your theme does not support <code>get_search_form</code>. <code>wrapper_class</code> is optional','qmean');?></p>
                    </td>
                  </tr>
                </table>
                <hr/>
                <h2 class="qmean-hint-toggler"><?php _e('Advance Setup','qmean');?></h2>
                <table class="form-table qmean-hint-toggle-wrapper">
                <tr valign="top">
                    <th scope="row"><?php _e('Search Input Selector','qmean');?>
                        <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_input_selector"></i>
                    </th>
                    <td>
                      <input type="text" name="qmean_input_selector" id="qmean_input_selector" value="<?php echo stripslashes(esc_attr( $settings['input_selector'] )); ?>" />
                      or <a href="<?php echo get_home_url();?>?qmean_field_recognizer" class="button button-primary"><?php _e('Use QMean\'s auto recognizer on front-end','qmean');?></a>
                      <div id="qmean_input_selector_help" class="qmean-settings-help">
                        <h3><?php _e('Search Input Selector','qmean');?></h3>
                        <p>
                          <?php _e('This is the field which will show the suggestions by typing 3 letters. Use CSS selector in full. e.g. <code>.selector-class</code> OR <code>#selector-id</code>. This is <strong>THE BEST & EASIEST WAY</strong> to hook QMean to any field you already have on your theme!','qmean');?>
                        </p>
                        <div class="qmean-info">
                            <h4 class="qmean-hint-toggler"><?php _e('How to find the selector fast?','qmean');?></h4>
                            <div class="qmean-hint-toggle-wrapper">
                              <p><?php _e('To find the selector, on Chrome or Firefox, right click on your search input field, then click Inspect Element, then you will see the class or the id value for the field. If id exists choose it, if not you need to use CSS. ','qmean');?></p>
                              <p><?php _e('The field might have multiple classes, sperated with spaces; One is enough then add a dot <code>.</code> at the beginning. It should be like <code>.oneOfThem</code> or you can concatenate them by dot to be sure that is unique like <code>.first.second.third.fourth</code>.','qmean');?></p>
                              <p><?php _e('Sometimes the parent element has unique id or class like &lt;form&gt; or &lt;div&gt; then you can use it like <code>#formId input</code> or <code>#firmId .inputClass</code>.','qmean');?></p>
                            </div>
                          </div>
                      </div>
                      <p><?php printf(__('<strong>NOTE: </strong>Just add <code>?qmean_field_recognizer</code> at the end of any URL to activate recognizer and if the URL already has <code>?</code> then add <code>&qmean_field_recognizer</code> e.g. %s/filter?qmean_field_recognizer <strong>or</strong> %s/?s=something&qmean_field_recognizer ','qmean'), get_home_url(), get_home_url());?></p>
                        
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <?php _e('Remove stop words','qmean');?>
                      <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_remove_stop_words"></i>
                      </th>
                    <td>
                      <select name="qmean_remove_stop_words" id="qmean_remove_stop_words">
                        <option<?php echo ($settings['remove_stop_words'] == 'basic') ? ' selected="selected"' : '';?> value="basic"><?php _e('Basic','qmean');?></option>
                        <option<?php echo ($settings['remove_stop_words'] == 'strict') ? ' selected="selected"' : '';?> value="strict"><?php _e('Strict','qmean');?></option>
                        <option<?php echo ($settings['remove_stop_words'] == 'no') ? ' selected="selected"' : '';?> value="no"><?php _e('No','qmean');?></option>
                      </select>
                      <div id="qmean_remove_stop_words_help" class="qmean-settings-help">
                        <h3><?php _e('Remove stop words','qmean');?></h3>
                        
                        <p><?php _e('The words which are generally filtered out before processing a natural language. e.g. a, the, and, or ...','qmean');?></p>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <?php _e('Merge Searched Queries','qmean');?>
                      <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_merge_previous_searched"></i>
                      </th>
                    <td>
                      <select name="qmean_merge_previous_searched" id="qmean_merge_previous_searched">
                        <option<?php echo ($settings['merge_previous_searched'] == 'yes') ? ' selected="selected"' : '';?> value="yes"><?php _e('Yes','qmean');?></option>
                        <option<?php echo ($settings['merge_previous_searched'] == 'no') ? ' selected="selected"' : '';?> value="no"><?php _e('No','qmean');?></option>
                      </select>
                      <div id="qmean_merge_previous_searched_help" class="qmean-settings-help">
                        <h3><?php _e('Merge Searched Queries','qmean');?></h3>
                        
                        <p><?php _e('If set to yes, previous queries searched by other users which have more than 0 results will rank higher based on hit and found_posts','qmean');?></p>
                      </div>
                      
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <?php _e('Keyword Efficiency','qmean');?>
                      <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_keyword_efficiency"></i>
                      </th>
                    <td>
                      <select name="qmean_keyword_efficiency" id="qmean_keyword_efficiency">
                        <option<?php echo ($settings['keyword_efficiency'] == 'on') ? ' selected="selected"' : '';?> value="on"><?php _e('On','qmean');?></option>
                        <option<?php echo ($settings['keyword_efficiency'] == 'off') ? ' selected="selected"' : '';?> value="off"><?php _e('Off','qmean');?></option>
                      </select>
                      <div id="qmean_keyword_efficiency_help" class="qmean-settings-help">
                        <h3><?php _e('Keyword Efficiency','qmean');?></h3>
                        
                        <p><?php _e('If on it will count ration of matched characters and total phrase length. Turn it off if your content is optimized with keywords in a row.','qmean');?></p>
                      </div>
                      
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <?php _e('Ignore Shortcodes','qmean');?>
                      <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_ignore_shortcodes"></i>
                      </th>
                    <td>
                      <select name="qmean_ignore_shortcodes" id="qmean_ignore_shortcodes">
                        <option<?php echo ($settings['ignore_shortcodes'] == 'no') ? ' selected="selected"' : '';?> value="no"><?php _e('No','qmean');?></option>
                        <option<?php echo ($settings['ignore_shortcodes'] == 'yes') ? ' selected="selected"' : '';?> value="yes"><?php _e('Yes','qmean');?></option>
                      </select>
                      <div id="qmean_ignore_shortcodes_help" class="qmean-settings-help">
                        <h3><?php _e('Ignore Shortcodes','qmean');?></h3>
                        <p><?php _e('If yes, shorcodes and their content will be ignored','qmean');?></p>
                      </div>
                      
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <?php _e('Limit Results','qmean');?>
                      <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_limit_results"></i>
                      </th>
                    <td>
                      <input type="text" name="qmean_limit_results" id="qmean_limit_results" value="<?php echo esc_attr( $settings['limit_results'] ); ?>" />
                      <div id="qmean_limit_results_help" class="qmean-settings-help">
                        <h3><?php _e('Limit Results','qmean');?></h3>
                        <p><?php _e('Number of results to show. Your search may have a lot of matched keywords. This number will limit them.','qmean');?></p>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top" id="qmean-word-count-wrapper"<?php echo $settings['search_mode'] == 'word_by_word' ? ' style="display:none"' : '';?>>
                    <th scope="row">
                      <?php _e('Found phrase word count','qmean');?>
                      <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_word_count"></i>
                      </th>
                    <td>
                      <input type="text" name="qmean_word_count" id="qmean_word_count" value="<?php echo esc_attr( $settings['word_count'] ); ?>" />
                      <div id="qmean_word_count_help" class="qmean-settings-help">
                        <h3><?php _e('Found phrase word count','qmean');?></h3>
                        <p><?php _e('Trim the phrase to by words to this number','qmean');?></p>
                      </div>                      
                    </td>
                  </tr>
                    <tr valign="top">
                    <th scope="row">
                      <?php _e('Search in','qmean');?>
                      <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_search_area"></i>
                      </th>
                    <td>
                      <div class="form-group block" id="qmean_search_area">
                        <label for="qmean_search_area_title">
                          <input id="qmean_search_area_title" type="checkbox"<?php echo in_array('posts_title',$search_areas) ? ' checked="checked" ' : '';?> value="posts_title" name="qmean_search_area[]" />
                          <?php _e('Posts titles','qmean');?>
                        </label>
                        <label for="qmean_search_area_excerpt">
                          <input id="qmean_search_area_excerpt" type="checkbox"<?php echo in_array('posts_excerpt',$search_areas) ? ' checked="checked" ' : '';?> value="posts_excerpt" name="qmean_search_area[]" />
                          <?php _e('Posts excerpt','qmean');?>
                        </label>
                        <label for="qmean_search_area_content">
                          <input id="qmean_search_area_content" type="checkbox"<?php echo in_array('posts_content',$search_areas) ? ' checked="checked" ' : '';?> value="posts_content" name="qmean_search_area[]" />
                          <?php _e('Posts content','qmean');?>
                        </label>
                        <label for="qmean_search_area_terms">
                          <input id="qmean_search_area_terms" type="checkbox"<?php echo in_array('terms',$search_areas) ? ' checked="checked" ' : '';?> value="terms" name="qmean_search_area[]" />
                          <?php _e('Term Taxonomies','qmean');?>
                        </label>
                        <label for="qmean_search_area_metas">
                          <input id="qmean_search_area_metas" type="checkbox"<?php echo in_array('posts_metas',$search_areas) ? ' checked="checked" ' : '';?> value="posts_metas" name="qmean_search_area[]" />
                          <?php _e('Posts Metas','qmean');?>
                        </label>
                      </div>
                      <div id="qmean_search_area_help" class="qmean-settings-help">
                        <h3><?php _e('Search Areas','qmean');?></h3>
                        <p><?php _e('Hidden keys (internal values like order metas and such) which start with underscore are not included. On large databases PostMetas may slow down the suggestions. Be sure or check your load time first.','qmean');?></p>
                      </div> 
                      
                    </td>
                    </tr>
                    <tr valign="top">
                    <th scope="row">
                      <?php _e('Include Post Types','qmean');?>
                        <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_post_types"></i>
                      </th>
                    <td>
                      <div class="form-group block" id="qmean_post_types">
                        <?php
                        $pts = get_post_types();
                        if(!empty($pts)) {
                          foreach ($pts as $key => $pt) {
                        ?>
                              <label for="qmean_post_types-<?php echo $key;?>">
                                <input id="qmean_post_types-<?php echo $key;?>" type="checkbox"<?php echo in_array($pt,$post_types) ? ' checked="checked" ' : '';?> value="<?php echo $pt;?>" name="qmean_post_types[]" />
                                <?php echo $pt;?>
                              </label>
                        <?php
                          }
                        }
                        ?>
                        <div id="qmean_post_types_help" class="qmean-settings-help">
                          <h3><?php _e('Include Post Types','qmean');?></h3>
                          <p><?php _e('Uncheck uneccessary post types for better performance','qmean');?></p>
                        </div> 
                        
                      </div>
                    </td>
                    </tr>



                    <tr valign="top">
                      <th scope="row">
                        <?php _e('Custom Action Hook','qmean');?>
                        <i class="dashicons dashicons-editor-help qmean-tooltip" data-target="qmean_custom_hook"></i>
                        </th>
                      <td>
                        <input type="text" name="qmean_custom_hook" id="qmean_custom_hook" value="<?php echo esc_attr( $custom_hook ); ?>" />
                        <div id="qmean_custom_hook_help" class="qmean-settings-help">
                          <h3><?php _e('Custom Action Hook','qmean');?></h3>
                          <p><?php _e('If you need to use different action hook instead of <code>get_search_form</code>, enter the action name here. It will be usefull if you are using a custom theme. You can also add the code below anywhere you want it to be shown','qmean');?></p>
                        <pre><code><?php echo esc_html("<?php do_action('qmean_suggestion');?>");?></code></pre>
                        </div> 
                        
                      </td>
                    </tr>

                </table>

            <hr/>


            <div class="qmean-settings-notification"></div>
            <button class="qmean-settings-save button button-hero button-primary" id="qmean-admin-save" type="submit"><?php _e( 'Save Settings', 'qmean' ); ?></button>
            <a class="qmean-settings-back button button-hero button-default" href="<?php echo admin_url('admin.php?page=qmean');?>"><?php _e('Back to Dashboard','qmean');?></a>
          </div>
        </div>




    </form>
  </div>
  <div class="qmean-notice between update-nag notice notice-info inline">
      <div>
      <span><?php _e('Need more support? please visit:','qmean');?></span>
      <a href="https://wordpress.org/support/plugin/qmean/" target="_blank"> <?php _e('WordPress Support','qmean');?></a>
        or
       <a href="https://github.com/arashsafaridev/qmean/" target="_blank"><?php _e('GitHub','qmean');?></a>
      </div>
      <div class="qmean-d-flex between">
       <span><?php _e('Make QMean better','qmean');?></span>
       <a href="https://wordpress.org/plugins/qmean/#reviews" class="button button-primary qmean-button-review" target="_blank"><?php _e('Write a review','qmean');?></a>
       <a href="https://forms.gle/mgESz8C5n2zvEyWt9" class="button button-secondary" target="_blank"><?php _e('Take the survey ','qmean');?></a>
      </div>
    </div>
</div>