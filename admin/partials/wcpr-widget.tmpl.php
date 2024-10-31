<?php echo do_shortcode("[codup_ads_top]"); ?>
<ul class="codup_pr-widget">
  <li>
    <p><small>Revenue</small></p>
    <h3><?php echo get_woocommerce_currency_symbol().' '.$codup_pr_income; ?></h3>
  </li>
  <li>
    <p><small>Total Buyers</small></p>
    <h3><?php echo $codup_pr_users; ?></h3>
  </li>
  <li>
    <p><small>Buyers with Orders</small></p>
    <h3><?php echo $codup_pr_users_has_orders; ?></h3>
  </li>
  <li>
    <p><small>Total Orders</small></p>
    <h3><?php echo $codup_pr_orders; ?></h3>
  </li>
<?php if($codup_pr_users_has_orders) :  ?>
  <li>
    <p><small>Average Customer Value</small></p>
    <h3><?php echo number_format($codup_pr_income / $codup_pr_users_has_orders ,2,'.',''); ?></h3>
  </li>
<?php else : ?>
  <li>
    <p><small>Average Customer Value</small></p>
    <p><small>No Registred Users Have Placed Orders</small></p>
  </li>
<?php endif; ?>
<?php if($codup_pr_orders) :  ?>
  <li>
    <p><small>Average Order Value</small></p>
    <h3><?php echo number_format($codup_pr_income / $codup_pr_orders ,2,'.',''); ?></h3>
  </li>
<?php else : ?>
  <li>
    <p><small>Average Order Value</small></p>
    <p><small>No Completed Orders Found</small></p>
  </li>
<?php endif; ?>

<!-- Average profit -->
<?php if($codup_pr_orders) :  
?>
  <li>
    <p><small>Average Profit</small></p>
    <h3><?php echo number_format(end($codup_avg_profit[0]) / $codup_pr_orders ,2,'.','');   ?></h3>
  </li>
<?php else : ?>
  <li>
    <p><small>Average Profit</small></p>
    <p><small>No Completed Orders Found</small></p>
  </li>
<?php endif; ?>
<!-- end profit -->

</ul>
<ul class="codup_pr-action">
  <li><a href='<?php menu_page_url("codup_pr-profit"); ?>'><span class="dashicons dashicons-arrow-right-alt"></span>&nbsp;&nbsp;View Profit Report</a></li>
</ul>
