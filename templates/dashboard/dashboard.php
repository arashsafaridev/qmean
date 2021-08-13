<?php
  $options = get_option('qmean_options');
  $orderby = $orderby == 'asc' ? 'desc' : 'asc';
?>
<div class="wrap qmean-dashboard">
  <h1><?php _e('Welcome to QMean Dashboard','qmean');?></h1>
  <div class="qmean-notice update-nag notice notice-info inline">
    <?php _e('Need more support? please visit:','qmean');?>
    <a href="https://wordpress.org/support/plugin/qmean/" target="_blank"> <?php _e('WordPress Support','qmean');?></a>
      Or
     <a href="https://github.com/arashsafaridev/qmean/issues" target="_blank"><?php _e('GitHub Issues','qmean');?></a>
  </div>
  <?php if($options['saved_by_user'] != 1){ ?>
    <div class="qmean-notice update-nag notice notice-error inline">
      <?php _e('You need to define your search field selector first, so for better search suggestion please visit settings page','qmean');?>
      <a class="button button-primary" href="<?php echo admin_url('admin.php?page=qmean-settings');?>"><?php _e('Go to Settings page','qmean');?></a>
    </div>
  <?php } ?>
  <h2><?php _e('Global Reports','qmean');?></h2>
  <p><?php _e('You can see all the searched queries here. <strong>Found posts</strong> is helpful if you are using <strong>word by word</strong> mode.','qmean');?></p>
  <div class="qmean-dashboard-report">
    <table class="wp-list-table widefat fixed striped table-view-list posts">
      <thead>
        <tr>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=kw&qmorder='.$orderby);?>"><?php _e('Keyword','qmean');?></a></td>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=ht&qmorder='.$orderby);?>"><?php _e('Hit','qmean');?></a></td>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=fp&qmorder='.$orderby);?>"><?php _e('Found Posts','qmean');?></a></td>
          <td><a href="<?php echo admin_url('admin.php?page=qmean&qmsort=time&qmorder='.$orderby);?>"><?php _e('Time','qmean');?></a></td>
        </tr>
      </thead>
      <?php if($keywords){?>
        <?php foreach ($keywords as $key => $keyword) { ?>
          <tr>
            <td><?php echo $keyword->keyword;?></td>
            <td><?php echo $keyword->hit;?></td>
            <td><?php echo $keyword->found_posts;?></td>
            <td><?php echo human_time_diff(time(),$keyword->created);?></td>
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
          'base'       => admin_url('admin.php?page=qmean&qmsort=kw&qmorder='.$orderby).'&qmp=%#%',
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
