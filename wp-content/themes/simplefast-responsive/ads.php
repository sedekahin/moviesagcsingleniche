<div class="ads336"><?php $ads_act = get_theme_option('ads_act2'); if(($ads_act == '') || ($ads_act == 'No')) { ?><?php } else { ?>
<div class="tags">Advertisement</div>
<?php } ?>
<?php $header_ads_act = get_theme_option('home_ads_act2'); if(($header_ads_act == '') || ($header_ads_act == 'No')) { ?>
<?php } else { ?><?php echo get_theme_option('home_ads2'); ?><?php } ?>
</div>