<?php
  $options = get_option('qmean_options');
  $orderby = $orderby == 'asc' ? 'desc' : 'asc';
  $saved_by_user = isset($options['saved_by_user']) ? $options['saved_by_user'] : 0;
?>
<div class="wrap qmean-dashboard">
  <h1><?php _e('QMean Dashboard','qmean');?><img class="qmean-settings-logo" width="100" src="<?php echo QMEAN_URL;?>assets/images/qmean-logo.svg"/></h1>
  <hr />
  <p><?php _e('You can see all user\'s searched queries here. <strong>Found posts</strong> is more helpful if you are using <strong>word by word</strong> mode.','qmean');?></p>
  <?php if($saved_by_user != 1){ ?>
    <div class="qmean-notice update-nag notice notice-error inline">
      <?php _e('You need to define your search field selector first, so for better search suggestion please visit settings page','qmean');?>
      <a class="button button-primary" href="<?php echo admin_url('admin.php?page=qmean-settings');?>"><?php _e('Go to Settings page','qmean');?></a>
    </div>
  <?php } ?>
  <div class="qmean-dashboard-report">
    <div class="qmean-dashboard-totals postbox">
      <div class="qmean-total-group">
        <strong><?php echo $total_hits;?></strong>
        <small><?php _e('Total Hits','qmean');?></small>
      </div>
      <div class="qmean-total-group">
        <strong><?php echo $total_keywords;?></strong>
        <small><?php _e('Keywords','qmean');?></small>
      </div>
      <div class="qmean-total-group">
        <strong><?php echo $total_no_results;?></strong>
        <small><?php _e('No Results','qmean');?></small>
      </div>
    </div>
    <form method="GET" action="<?php echo admin_url('admin.php?page=qmean');?>" class="qmean-dashboard-search postbox">
      <input name="page" value="qmean" type="hidden" />
      <input class="qmean-search-keywords-input" name="qmsearch" value="<?php echo $search;?>" placeholder="<?php _e('Search ...','qmean');?>"/>
    </form>
    <table class="wp-list-table widefat fixed striped table-view-list posts">
      <thead>
        <tr>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=kw&qmorder='.$orderby);?>"><?php _e('Keyword','qmean');?></a></td>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=ht&qmorder='.$orderby);?>"><?php _e('Hit','qmean');?></a></td>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=fp&qmorder='.$orderby);?>"><?php _e('Found Posts','qmean');?></a></td>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=time&qmorder='.$orderby);?>"><?php _e('Last seached','qmean');?></a></td>
          <td><?php _e('Actions','qmean');?></td>
        </tr>
      </thead>
      <?php if($keywords){?>
        <?php foreach ($keywords as $key => $keyword) { ?>
          <tr>
            <td><a href="#<?php echo $keyword->keyword;?>" class="qmean-open-modal minimal" data-type="user-eye" data-keyword="<?php echo $keyword->keyword;?>"><?php echo $keyword->keyword;?></a></td>
            <td><?php echo $keyword->hit;?></td>
            <td><?php echo $keyword->found_posts;?></td>
            <td><?php echo human_time_diff(time(),$keyword->created);?> <?php _e('ago', 'qmean');?></td>
            <td class="dash-table-actions">
              <a href="#<?php echo $keyword->keyword;?>" class="qmean-open-modal minimal" data-type="user-eye" data-keyword="<?php echo $keyword->keyword;?>"><?php _e('User Eye','qmean');?></a>
              <a href="#" class="qmean-remove-keyword" data-id="<?php echo $keyword->id;?>"><?php _e('Remove','qmean');?></a>
            </td>
          </tr>
        <?php } ?>
      <?php } else { ?>
        <tr>
          <td colspan="4"><?php _e('Nothing is recorded yet!','qmean');?></td>
        </tr>
      <?php } ?>
    </table>
    <?php
      echo paginate_links( array(
          'base'       => admin_url('admin.php?page=qmean').'&qmp=%#%',
          'format'     => 'qmp=%#%',
          'current'    => max( 1, $page ),
          'total'      => (int)(($number + $total_keywords)/$number),
          'mid_size'   => 1,
          'prev_text'  => __('«'),
          'next_text'  => __('»'),
          'type'       => 'list'
      ) );
    ?>
    
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
