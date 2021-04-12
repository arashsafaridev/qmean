<?php
  $options = get_option('qmean_options');
?>
<div class="wrap">
  <h1><?php _e('Welcome to QMean Dashboard','qmean');?></h1>
  <?php if($options['saved_by_user'] != 1){ ?>
    <div class="update-nag notice notice-error inline">
      <?php _e('You need to define your search field selector first, so for better search suggestion please visit settings page','qmean');?>
      <a href="<?php echo admin_url('admin.php?page=qmean-settings');?>"><?php _e('Go to Settings page','qmean');?></a>
    </div>
    <a class="button button-primary button-hero" href="<?php echo admin_url('admin.php?page=qmean-settings');?>"><?php _e('Go to Settings page','qmean');?></a>
  <?php } ?>
  <p><?php _e('Query Analytics will be here soon.','qmean');?></p>
  <p><?php _e('If you have any issues, please let me know, don\'t keep me blind to them! I will fix them if I knew them though :)','qmean');?></p>
  <div class="update-nag notice notice-info inline">
    <?php _e('Need more support? please visit:','qmean');?>
    <br />
    <a href="https://wordpress.org/support/plugin/qmean/" target="_blank"><?php _e('WordPress Support','qmean');?></a>
     Or
    <a href="https://github.com/arashsafaridev/qmean/issues" target="_blank"><?php _e('GitHub Issues','qmean');?></a>
  </div>
</div>
