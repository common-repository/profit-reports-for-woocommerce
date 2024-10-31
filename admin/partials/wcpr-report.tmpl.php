<?php echo do_shortcode("[codup_ads_top]");?>

<div id="codup_pr-report-page" class="wrap">
<h2>Profit Reporting</h2>
    <div class="divider"></div>
    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <form id="codup_pr_date_filter" method="GET">
                Search ID :
                <input id="search_id" name="search_id" type="number" placeholder="Order ID" >
                <input id="codup_pr_paged" name="paged" type="hidden" value="1">
                <input id="codup_pr_page" name="page" type="hidden" value="codup_pr-profit">
                <label for="codup_pr_from_date">From: </label>
                <input id="codup_pr_from_date" name="codup_pr_from_date" type="date">
                <label for="codup_pr_to_date">To: </label>
                <input id="codup_pr_to_date" name="codup_pr_to_date" type="date"> 
               <div class="select-opt"> 
                <?php 
                    $arguments = array(
                        'uid' => 'status_id',
                        'type' => 'multiselect',
                        'options' => wc_get_order_statuses(),
                        'default' => array(),
                    );
                    
                    if( !empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                      $attributes = '';
                      $options_markup = '';
                      foreach( $arguments['options'] as $key => $label ){
                         $options_markup .= sprintf( '<option  value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, (array)$value, true ) ], $key, false ), $label );
                       }
                      if( $arguments['type'] === 'multiselect' ){
                         $attributes = ' multiple="multiple" ';
                      }
                      printf( '<select style="width:160px;" name="%1$s" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup );
                    }
              
                ?>
                <input id="apply" type="submit" class="button action" value="Apply"></div>
            </form>
        </div>
    </div>
    <table id="wcpr-reports" class="wp-list-table widefat fixed striped posts">
        <thead>
        <tr>
            <th class="manage-column">Order Id</th>
            <th class="manage-column">Order Date</th>
            <th class="manage-column">Order Total</th>
            <th class="manage-column">Order Cost</th>
            <th class="manage-column">Overhead</th>
            <th class="manage-column">Order Tax</th>
            <th class="manage-column">Order Shipment</th>
            <th class="manage-column">Order Profit</th>
            <th class="manage-column">Order Status</th>
        </tr>
        </thead>
        <tbody>

        <?php while ($posts_query->have_posts()) : $posts_query->the_post();?>
        <?php
        $orderid = get_the_ID();
        $order = wc_get_order( $orderid );
        $order_status  = $order->get_status();
        $order_obj = new WC_Order($orderid);
        $order_date = get_the_date('d,M,Y',$orderid);
        $order_profit = get_post_meta($orderid,"_codup_pr_profit",true);
        $order_cost = get_post_meta($orderid,'_codup_pr_order_cost',true);
        $order_ohead = get_post_meta($orderid,'_codup_pr_order_ohead',true);
        $order_ohead = $order_ohead ? $order_ohead : 0 ;
        $order_cost = floatval($order_cost )- floatval ($order_ohead);
        $order_total = $order_obj->get_total();
        $order_url = site_url('wp-admin/post.php?post='.$orderid.'&action=edit');
        $order_tax = $order_obj->get_total_tax();
        $order_ship = $order_obj->calculate_shipping();
        if($order_status == 'completed'){
          $order_profit = get_post_meta($orderid,"_codup_pr_profit",true);
          $order_gtotal = $order_gtotal + $order_total;
          $order_gcost = $order_gcost + $order_cost;
          $order_gohead = $order_gohead + $order_ohead;
          $order_gtax = $order_gtax + $order_tax;
          $order_gship = $order_gship + $order_ship;
          $order_gprofit = $order_gprofit + $order_profit;
        }
        else{
          $order_profit = 0;
        }
        if($order_status == 'refunded' && $order_status == 'completed'){
            $order_profit = get_post_meta($orderid,"_codup_pr_profit",true);
            $order_gprofit = $order_gprofit - $order_profit;
        }
            ?>
            <tr>
             <td><a href='<?php echo $order_url; ?>'>#<?php echo $orderid; ?></a></td>
                <td><?php echo $order_date; ?></td>
                <td><?php echo $currency_symbol.$order_total; ?></td>
                <td><?php echo $currency_symbol.$order_cost; ?></td>
                <td><?php echo $currency_symbol.$order_ohead; ?></td>
                <td><?php echo $currency_symbol.$order_tax; ?></td>
                <td><?php echo $currency_symbol.$order_ship; ?></td>
              <?php if($order_profit < 0) : ?>
                  <td class="text-red"><?php echo $currency_symbol.$order_profit; ?></td>
                <?php else : ?>
                  <td><?php echo $currency_symbol.$order_profit; ?></td>

                <?php endif; ?>
                
                <td class="table-onhold"><?php echo  $order_status; ?></td>
                
            </tr>
            
        <?php endwhile; wp_reset_postdata();?>
        </tbody>
        <tfoot>
            <tr>
              <!-- Total Rows Displaying -->
              <td>Total</td>
              <td></td>
                  <td><?php echo $currency_symbol.$order_gtotal; ?></td>
                  <td><?php echo $currency_symbol.$order_gcost; ?></td>
                  <td><?php echo $currency_symbol.$order_gohead; ?></td>
                  <td><?php echo $currency_symbol.$order_gtax; ?></td>
                  <td><?php echo $currency_symbol.$order_gship; ?></td>
              <?php if($order_gprofit < 0) : ?>
                <td class="text-red"><?php echo $currency_symbol.$order_gprofit; ?></td>
              <?php else : ?>
                <td ><?php echo $currency_symbol.$order_gprofit; ?></td>
              <?php endif; ?>
            </tr>
        </tfoot>
    </table>
    <?php $this->codup_pr_custom_pagination($posts_query->max_num_pages,$posts_query->max_num_pages,$paged,$from_date,$to_date);?>

</div>
