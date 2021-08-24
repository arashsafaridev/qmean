<?php
  $options = get_option('qmean_options');
  $orderby = $orderby == 'asc' ? 'desc' : 'asc';
  $saved_by_user = isset($options['saved_by_user']) ? $options['saved_by_user'] : 0;
?>
<div class="wrap qmean-dashboard">
  <h1><?php _e('QMean Dashboard','qmean');?><img class="qmean-settings-logo" width="100" src="<?php echo QMean_URL;?>assets/images/qmean-logo.svg"/></h1>
  <hr />
  <p><?php _e('You can see all user\'s searched queries here. <strong>Found posts</strong> is more helpful if you are using <strong>word by word</strong> mode.','qmean');?></p>
  <div class="qmean-notice between update-nag notice notice-info inline">
    <div>
    <span><?php _e('Need more support? please visit:','qmean');?></span>
    <a href="https://wordpress.org/support/plugin/qmean/" target="_blank"> <?php _e('WordPress Support','qmean');?></a>
      or
     <a href="https://github.com/arashsafaridev/qmean/issues" target="_blank"><?php _e('GitHub Issues','qmean');?></a>
   </div>
   <div class="qmean-d-flex between">
     <span><?php _e('Liked the plugin?','qmean');?></span>
     <a href="https://wordpress.org/plugins/qmean/#reviews" class="button button-primary" target="_blank"><?php _e('Write a review','qmean');?></a>
   </div>
  </div>
  <?php if($saved_by_user != 1){ ?>
    <div class="qmean-notice update-nag notice notice-error inline">
      <?php _e('You need to define your search field selector first, so for better search suggestion please visit settings page','qmean');?>
      <a class="button button-primary" href="<?php echo admin_url('admin.php?page=qmean-settings');?>"><?php _e('Go to Settings page','qmean');?></a>
    </div>
  <?php } ?>
  <div class="qmean-dashboard-report">
    <table class="wp-list-table widefat fixed striped table-view-list posts">
      <thead>
        <tr>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=kw&qmorder='.$orderby);?>"><?php _e('Keyword','qmean');?></a></td>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=ht&qmorder='.$orderby);?>"><?php _e('Hit','qmean');?></a></td>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=fp&qmorder='.$orderby);?>"><?php _e('Found Posts','qmean');?></a></td>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=time&qmorder='.$orderby);?>"><?php _e('Time','qmean');?></a></td>
          <td><?php _e('Actions','qmean');?></td>
        </tr>
      </thead>
      <?php if($keywords){?>
        <?php foreach ($keywords as $key => $keyword) { ?>
          <tr>
            <td><a href="#<?php echo $keyword->keyword;?>" class="qmean-open-modal minimal" data-type="user-eye" data-keyword="<?php echo $keyword->keyword;?>"><?php echo $keyword->keyword;?></a></td>
            <td><?php echo $keyword->hit;?></td>
            <td><?php echo $keyword->found_posts;?></td>
            <td><?php echo human_time_diff(time(),$keyword->created);?></td>
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
          'total'      => (int)(($number + $total)/$number),
          'mid_size'   => 1,
          'prev_text'  => __('«'),
          'next_text'  => __('»'),
          'type'       => 'list'
      ) );
    ?>
  </div>
</div>
