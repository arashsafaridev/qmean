<?php
  $settings = get_option('qmean_options');
  $post_types = empty($settings['post_types']) ? [] : $settings['post_types'];
  $search_areas = empty($settings['search_area']) ? [] : $settings['search_area'];
?>
<div class="wrap">
  <div id="qmean-settings">
    <form method="post" id="qmean-admin-form">
      <input type="hidden" name="action" id="qmean-action" value="qmean_store_admin_data" />
      <input type="hidden" name="_wpnonce" id="qmean-security" value="" />
      <input type="hidden" name="qmean_saved_by_user" id="qmean_saved_by_user" value="1" />
        <div class="inside qmean-settings">
            <div class="postbox">
              <img class="qmean-settings-logo" width="200" src="<?php echo QMean_URL;?>assets/images/qmean-logo.svg"/>
              <h1><?php _e('Settings','qmean');?></h1>
              <hr/>
            <table class="form-table">
              <tr valign="top">
                <th scope="row"><?php _e('Search input selector','qmean');?></th>
                <td>
                  <input type="text" name="qmean_input_selector" value="<?php echo esc_attr( $settings['input_selector'] ); ?>" />
                  <p><?php _e('This is the field which will show the suggestions by typing 3 letters. Use CSS selector in full. Like <code>.selector-class</code> OR <code>#selector-id</code>. This is <strong>THE BEST & EASIEST WAY</strong> to hook QMean to any field you already have on your theme! :)','qmean');?></p>
                    <div class="info">
                      <h3 class="qmean-hint-toggler"><?php _e('How to find the selector fast?','qmean');?></h3>
                      <div class="qmean-hint-toggle-wrapper">
                        <p><?php _e('To find the selector, on Chrome or Firefox, right click on your search input field, then click Inspect Element, then you will see the class or the id value for the field. If id exists choose it, if not you need to use CSS. ','qmean');?></p>
                        <p><?php _e('The field might have multiple classes, sperated with spaces; One is enough then add a dot <code>.</code> at the beginning. It should be like <code>.oneOfThem</code> or you can concatenate them by dot to be sure that is unique like <code>.first.second.third.fourth</code>.','qmean');?></p>
                        <p><?php _e('Sometimes the parent element has unique id or class like &lt;form&gt; or &lt;div&gt; then you can use it like <code>#formId input</code> or <code>#firmId .inputClass</code>.','qmean');?></p>
                      </div>
                    </div>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><?php _e('QMean shortcode','qmean');?></th>
                <td>
                  <code>
                    [qmean icon="yes" placeholder="Type to search" button_bg="#1a1a1a" button_height="40px" button_width="40px" form_class="" input_class="" button_class=""  form_style="" input_style="" button_style=""]
                  </code>
                  <p><?php _e('Instead of selector (above solution) you can use the shortcode.','qmean');?></p>
                  <div class="info">
                    <h3 class="qmean-hint-toggler"><?php _e('How to use, what are the options? and best practices','qmean');?></h3>
                    <div class="qmean-hint-toggle-wrapper">
                      <ul class="">
                        <li><?php _e('Use just <code>[qmean]</code> anywhere you want; Post/Page contents, Widgets and ...','qmean');?></li>
                        <li><?php _e('You can use these options to customize:','qmean');?></li>
                        <li>
                          <pre>
      icon: yes, no
      placeholder: any text you want
      button_bg: CSS color like #1a1a1a
      button_height: number with unit like 40px, 3rem, 20% or ...
      button_width: number with unit like 40px, 3rem, 20% or ...
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
                <th scope="row"><?php _e('Search Mode','qmean');?></th>
                <td>
                  <select name="qmean_search_mode">
                    <option<?php echo ($settings['search_mode'] == 'word_by_word') ? ' selected="selected"' : '';?> value="word_by_word"><?php _e('Word By Word','qmean');?></option>
                    <option<?php echo ($settings['search_mode'] == 'phrase') ? ' selected="selected"' : '';?> value="phrase"><?php _e('Phrase','qmean');?></option>
                  </select>
                  <p><?php _e('Word by word will compelete the search query on every word, individually, but phrase mode will get the phrase containing the word. Word by word may produce keyword combinations that won\'t match to any result but can help the SEO by reproducing them','qmean');?></p>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><?php _e('Sensitivity','qmean');?></th>
                <td>
                  <select name="qmean_sensitivity">
                    <option<?php echo ($settings['sensitivity'] == 1) ? ' selected="selected"' : '';?> value="1"><?php _e('Lowest','qmean');?></option>
                    <option<?php echo ($settings['sensitivity'] == 2) ? ' selected="selected"' : '';?> value="2"><?php _e('Low','qmean');?></option>
                    <option<?php echo ($settings['sensitivity'] == 3) ? ' selected="selected"' : '';?> value="3"><?php _e('Medium','qmean');?></option>
                    <option<?php echo ($settings['sensitivity'] == 4) ? ' selected="selected"' : '';?> value="4"><?php _e('High','qmean');?></option>
                    <option<?php echo ($settings['sensitivity'] == 5) ? ' selected="selected"' : '';?> value="5"><?php _e('Extrem','qmean');?></option>
                  </select>
                  <p><?php _e('More sensitive means more relevent result. In fact the combination of characters in a row will define sensativity!','qmean');?></p>
                  <div class="info">
                    <h3 class="qmean-hint-toggler"><?php _e('Learn more by example','qmean');?></h3>
                    <div class="qmean-hint-toggle-wrapper">
                      <p><?php _e('Relative to your sensivity option, lowest in this case, if the user searches for "wor", QMean will get all the words containing these characters no matter of their order and rank them higher like: <code>world, otherwise,flower, worst, WordPress, words</code>','qmean');?></p>
                      <p><?php _e('Relative to your sensivity option, high in this case, if the user searches for "wor", QMean will get all the words containing these characters in a row and rank them higher like: <code>world, worst, WordPress, words, otherwise,flower, wrong</code>','qmean');?></p>
                      <p><?php _e('<strong>In general sensitivity can manipulate number of results suggested! You can test to choose which one suits you best.</strong>','qmean');?></p>
                    </div>
                  </div>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><?php _e('Limit results','qmean');?></th>
                <td>
                  <input type="text" name="qmean_limit_results" value="<?php echo esc_attr( $settings['limit_results'] ); ?>" />
                  <p><?php _e('Number of results to show. Your search may have a lot of matched keywords. This number will limit them.','qmean');?></p>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><?php _e('Shortten phrase limit','qmean');?></th>
                <td>
                  <input type="text" name="qmean_cut_phrase_limit" value="<?php echo esc_attr( $settings['cut_phrase_limit'] ); ?>" />
                  <p><?php _e('Rank higher and match the phrases lower than this number for length. Default is 50 characters. Then it will show 3 words starting with the query','qmean');?></p>
                </td>
              </tr>
                <tr valign="top">
                <th scope="row"><?php _e('Search in','qmean');?></th>
                <td>
                  <div class="form-group block">
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
                  <p><?php _e('Hidden keys (internal values like order metas and such) which start with underscore are not included. On large databases PostMetas may slow down the suggestions. Be sure or check your load time first.','qmean');?></p>
                </td>
                </tr>
                <tr valign="top">
                <th scope="row"><?php _e('Include post types','qmean');?></th>
                <td>
                  <div class="form-group block">
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
                    <p><?php _e('Uncheck uneccessary post types for better performance','qmean');?></p>
                  </div>
                </td>
                </tr>

                <tr valign="top">
                  <th scope="row"><?php _e('Auto Set Parent Position','qmean');?></th>
                  <td>
                    <select name="qmean_parent_position">
                      <option value=""><?php _e('Do Nothing!');?></option>
                      <option<?php echo ($settings['parent_position'] == 'relative') ? ' selected="selected"' : '';?> value="relative"><?php _e('Relative','qmean');?></option>
                      <option<?php echo ($settings['parent_position'] == 'absolute') ? ' selected="selected"' : '';?> value="absolute"><?php _e('Absolute','qmean');?></option>
                      <option<?php echo ($settings['parent_position'] == 'fixed') ? ' selected="selected"' : '';?> value="fixed"><?php _e('Fixed','qmean');?></option>
                    </select>
                    <p><?php _e('Input parent\'s postion needs to be set for suggestion wrapper to appear correctly. If the parent doesn\'t have any position this can automatically add it. Relative is common and won\'t harm','qmean');?></p>
                  </td>
                </tr>

                <tr valign="top">
                  <th scope="row"><?php _e('Suggestion Result Positioning','qmean');?></th>
                  <td class="qmean-positioning">
                    <label for="qmean_suggestion_zindex"><?php _e('CSS z-index','qmean');?></label>
                    <input type="text" id="qmean_suggestion_zindex" name="qmean_suggestion_zindex" value="<?php echo esc_attr( $settings['suggestion_zindex'] ); ?>"/>
                    <label for="qmean_suggestion_posx"><?php _e('CSS left','qmean');?></label>
                    <input type="text" id="qmean_suggestion_posx" name="qmean_suggestion_posx" value="<?php echo esc_attr( $settings['suggestion_posx'] ); ?>"/>
                    <label for="qmean_suggestion_posy"><?php _e('CSS Top','qmean');?></label>
                    <input type="text" id="qmean_suggestion_posy" name="qmean_suggestion_posy" value="<?php echo esc_attr( $settings['suggestion_posy'] ); ?>"/>
                    <label for="qmean_suggestion_width"><?php _e('Width','qmean');?></label>
                    <input type="text" id="qmean_suggestion_width" name="qmean_suggestion_width" value="<?php echo esc_attr( $settings['suggestion_width'] ); ?>"/>
                    <label for="qmean_suggestion_height"><?php _e('Height','qmean');?></label>
                    <input type="text" id="qmean_suggestion_height" name="qmean_suggestion_height" value="<?php echo esc_attr( $settings['suggestion_height'] ); ?>"/>
                    <p><?php _e('Use units too like 50px, 5%, 5rem or any CSS standard unit; Please make sure that the parent of the input has a position of relative or absolute. Use - (dash) to automate each value','qmean');?></p>
                    <p class="info"><?php _e('You can also use <code>#qmean-suggestion-results</code> selector for suggestion wrapper and <code>.qmean-suggestion-item</code> for suggestion item in your CSS file for better styling. Use <code>.qmean-typo-suggestion</code> for DidYouMean box after the search.','qmean');?></p>
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row"><?php _e('Suggestion Wrapper Styling','qmean');?></th>
                  <td class="qmean-styling">
                    <label for="qmean_wrapper_background"><?php _e('Background Color','qmean');?></label>
                    <input type="text" id="qmean_wrapper_background" name="qmean_wrapper_background" value="<?php echo esc_attr( $settings['wrapper_background'] ); ?>"/>
                    <label for="qmean_wrapper_border_radius"><?php _e('Border Radius','qmean');?></label>
                    <input type="text" id="qmean_wrapper_border_radius" name="qmean_wrapper_border_radius" value="<?php echo esc_attr( $settings['wrapper_border_radius'] ); ?>"/>
                    <label for="qmean_wrapper_padding"><?php _e('Padding','qmean');?></label>
                    <input type="text" id="qmean_wrapper_padding" name="qmean_wrapper_padding" value="<?php echo esc_attr( $settings['wrapper_padding'] ); ?>"/>
                    <p><?php _e('Enter standard CSS values like color #ffffff, border radius 0px 0px 0px 0px, padding 0px 0px 0px 0px. Any standard CSS unit is allowed like em, rem, % and  ... . order of values is: top right bottom left','qmean');?></p>
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row"><?php _e('RTL Support','qmean');?></th>
                  <td>
                    <select name="qmean_rtl_support">
                      <option<?php echo ($settings['rtl_support'] == 'no') ? ' selected="selected"' : '';?> value="no"><?php _e('No','qmean');?></option>
                      <option<?php echo ($settings['rtl_support'] == 'yes') ? ' selected="selected"' : '';?> value="yes"><?php _e('Yes','qmean');?></option>
                    </select>
                    <p><?php _e('Instead of using left for positioning it will use right instead','qmean');?></p>
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row"><?php _e('Custom Action Hook','qmean');?></th>
                  <td>
                    <input type="text" name="qmean_custom_hook" value="<?php echo esc_attr( $settings['custom_hook'] ); ?>" />
                    <p><?php _e('If you need to use different action hook instead of <code>get_search_form</code>, enter the action name here. It will be usefull if you are using a custom theme. You can also add the code below anywhere you want it to be shown','qmean');?></p>
                    <pre><code><?php echo wp_specialchars("<?php do_action('qmean_suggestion');?>");?></code></pre>
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
