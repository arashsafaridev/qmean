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
                  <p><?php _e('More sensitive means more relevent result','qmean');?></p>
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
                  <p><?php _e('If phrase mode is selected, this will shortten phrases with length bigger than this number. Default is 50 characters. It will show 3 words starting with the query instead ','qmean');?></p>
                </td>
              </tr>
                <tr valign="top">
                <th scope="row"><?php _e('Search in','qmean');?></th>
                <td>
                  <div class="form-group">
                    <label for="qmean_search_area_title">
                      <?php _e('Posts titles','qmean');?>
                      <input id="qmean_search_area_title" type="checkbox"<?php echo in_array('posts_title',$search_areas) ? ' checked="checked" ' : '';?> value="posts_title" name="qmean_search_area[]" />
                    </label>
                    <label for="qmean_search_area_excerpt">
                      <?php _e('Posts excerpt','qmean');?>
                      <input id="qmean_search_area_excerpt" type="checkbox"<?php echo in_array('posts_excerpt',$search_areas) ? ' checked="checked" ' : '';?> value="posts_excerpt" name="qmean_search_area[]" />
                    </label>
                    <label for="qmean_search_area_content">
                      <?php _e('Posts content','qmean');?>
                      <input id="qmean_search_area_content" type="checkbox"<?php echo in_array('posts_content',$search_areas) ? ' checked="checked" ' : '';?> value="posts_content" name="qmean_search_area[]" />
                    </label>
                    <label for="qmean_search_area_metas">
                      <?php _e('Posts Metas','qmean');?>
                      <input id="qmean_search_area_metas" type="checkbox"<?php echo in_array('posts_metas',$search_areas) ? ' checked="checked" ' : '';?> value="posts_metas" name="qmean_search_area[]" />
                    </label>
                  </div>
                  <p><?php _e('Hidden keys (internal values like order metas and such) which start with underscore are not included. On large databases PostMetas may slow down the suggestions. Be sure or check your load time first.','qmean');?></p>
                </td>
                </tr>
                <tr valign="top">
                <th scope="row"><?php _e('Include post types','qmean');?></th>
                <td>
                  <div class="form-group">
                    <?php $pts = get_post_types();?>
                    <div class="form-group">

                      <?php if(!empty($pts)) {
                        foreach ($pts as $key => $pt) {
                          ?>
                          <label for="qmean_post_types-<?php echo $key;?>">
                            <?php echo $pt;?>
                            <input id="qmean_post_types-<?php echo $key;?>" type="checkbox"<?php echo in_array($pt,$post_types) ? ' checked="checked" ' : '';?> value="<?php echo $pt;?>" name="qmean_post_types[]" />
                          </label>
                      <?php }
                      } ?>
                      <p><?php _e('Uncheck uneccessary post types for better performance','qmean');?></p>
                    </div>
                  </div>
                </td>
                </tr>

                <tr valign="top">
                  <th scope="row"><?php _e('Search input selector','qmean');?></th>
                  <td>
                    <input type="text" name="qmean_input_selector" value="<?php echo esc_attr( $settings['input_selector'] ); ?>" />
                    <p><?php _e('Use CSS selector in full. If it is a class it needs . if its an id it needs # and make sure you have only one element with this id! unless you have a corrupted HTML code in general :)','qmean');?></p>
                    <p class="info"><?php _e('To find the selector, on Chrome or Firefox, right click on your search input field, then click Inspect Element, then you will see the class or the id value for the field.','qmean');?></p>
                    <p class="info"><?php _e('The field might have multiple classes, one is enough. You can use it like input.ONE-OF-THE-CLASSES.','qmean');?></p>
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
                    <label for="qmean_suggestion_posx"><?php _e('CSS left','qmean');?></label>
                    <input type="text" id="qmean_suggestion_posx" name="qmean_suggestion_posx" value="<?php echo esc_attr( $settings['suggestion_posx'] ); ?>"/>
                    <label for="qmean_suggestion_posy"><?php _e('CSS Top','qmean');?></label>
                    <input type="text" id="qmean_suggestion_posy" name="qmean_suggestion_posy" value="<?php echo esc_attr( $settings['suggestion_posy'] ); ?>"/>
                    <label for="qmean_suggestion_width"><?php _e('Width','qmean');?></label>
                    <input type="text" id="qmean_suggestion_width" name="qmean_suggestion_width" value="<?php echo esc_attr( $settings['suggestion_width'] ); ?>"/>
                    <label for="qmean_suggestion_height"><?php _e('Height','qmean');?></label>
                    <input type="text" id="qmean_suggestion_height" name="qmean_suggestion_height" value="<?php echo esc_attr( $settings['suggestion_height'] ); ?>"/>
                    <p><?php _e('Use units too like 50px, 5%, 5rem or any CSS standard unit; Please make sure that the parent of the input has a position of relative or absolute. Use - (dash) to automate each value','qmean');?></p>
                    <p class="info"><?php _e('You can also use #qmean-suggestion-results selector for suggestion wrapper and .qmean-suggestion-item for suggestion item in your CSS file for better styling. Use .qmean-typo-suggestion for DidYouMean box after the search.','qmean');?></p>
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
                    <p><?php _e('If you need to use different action hook instead of get_search_form, enter the action name here. It will be usefull if you are using a custom theme. You can also add the code below anywhere you want it to be shown','qmean');?></p>
                    <pre><?php echo wp_specialchars("<?php do_action('qmean_suggestion');?>");?></pre>
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
