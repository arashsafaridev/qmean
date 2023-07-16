<div class="qmean-modal-wrapper">
  <div class="qmean-modal-keyword-top">
    <h2><?php _e('User is seeing these suggestions for','qmean');?>: <strong><?php echo $keyword;?></strong></h2>
    <div class="qmean-keyword-wrapper">
      <?php if($suggestions) { ?>
        <table class="qmean-keywords-list wp-list-table widefat striped">
          <thead>
            <tr>
              <td><?php _e('Keyword','qmean');?></td>
            </tr>
          </thead>
          <?php if ($mode == 'word_by_word') { ?>
            <?php foreach ($suggestions['suggestions'] as $index => $suggestion_list) { ?>
              <tr>
                <td class="qmean-top-keyword"><strong><?php echo $suggestions['words'][$index];?></strong></td>
              </tr>
              <?php if ( $suggestion_list ) { ?>
              <?php foreach ($suggestion_list as $key => $suggestion) { ?>
                <tr>
                  <td><strong><?php echo implode(" ",array_slice($suggestions['words'],0,$index));?></strong> <?php echo $suggestion;?></td>
                </tr>
              <?php } ?>
            <?php } ?>
            <?php } ?>
          <?php } else if ($mode == 'phrase') { ?>
              <?php foreach ($suggestions as $key => $suggestion) { ?>
                <tr>
                  <td><?php echo $suggestion;?></td>
                </tr>
              <?php } ?>
            <?php } ?>
        </table>
      <?php } else { ?>
        <p><?php _e('No keywords found!','qmean');?></p>
      <?php } ?>
    </div>
  </div>
</div>
