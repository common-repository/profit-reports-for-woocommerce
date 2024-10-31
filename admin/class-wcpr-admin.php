<?php

/**
 * Implementation of admin specific functionality.
 *
 *@since 1.1.1.0.
 *@package productor
 *@subpackage admin/productor
 *
 */


/**
 * Implementation of admin specific functionality for WC_PR Admin.
 *
 * Defines plugin name, version, post types, taxonomies, tags & filters.
 *
 * @since 1.1.1.0.
 * @author sami@codup
 *
*/

class WC_PR_Admin  {

    /**
     * The ID of this plugin.
     *
     * @since    1.1.1.0
     * @access   private
     * @var      string
     */
    private $plugin_name;

    /**
     * The Version of this plugin.
     *
     * @since    1.1.1.0
     * @access   private
     * @var      string
     */
    private $version;


    /**
     * Initialize the WC_PR Admin Class & set it's properties.
     *
     * @since 1.1.1.0.
     * @param string $plugin_name Name of plugin.
     * @param string $version Version of plugin.
    */
    // woocommerce_reports_charts
    public function __construct($plugin_name,$version) {
        $this->plugin_name = $plugin_name;
        $this->version= $version;
        
    }
  
    /**
     * Add Cost of Price Field to Product & Variation
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_add_cost_input() {

        woocommerce_wp_text_input(
            array(
                'id'          => '_codup_pr_cost_price',
                'label'       => __( 'Cost of Product', 'codup-wc-profit-reporting' ),
                'placeholder' => 'Using default from settings.',
                'desc_tip'    => 'true',
                'description' => __( 'Enter the purchasing price of product here.', 'codup-wc-profit-reporting' )
            )
        );

    }


    /**
     * Add Cost of Price Input to Variations of Variable Products
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_add_variable_cost($loop, $variation_data, $variation) {

        woocommerce_wp_text_input(
            array(
                'id'          => '_codup_pr_cost_price['.$variation->ID.']',
                'label'       => __( 'Cost of Product', 'codup-wc-profit-reporting' ),
                'placeholder' => 'Using default from settings.',
                'desc_tip'    => 'true',
                'description' => __( 'Enter the COGS/purchasing price of product here.', 'codup-wc-profit-reporting' ),
                'value'       => get_post_meta( $variation->ID, '_codup_pr_cost_price', true )
            )
        );

    }


    /**
     * Save Cost Price of Product
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_save_cost_price($post_id) {

        if(isset($_POST['_codup_pr_cost_price'])) {

            update_post_meta($post_id,'_codup_pr_cost_price',esc_attr($_POST['_codup_pr_cost_price']));

        }

    }


    /**
     * Save Cost Price of Product Variation
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_save_variable_cost($post_id) {

        if(isset($_POST['_codup_pr_cost_price'][$post_id])) {

            update_post_meta($post_id,'_codup_pr_cost_price',esc_attr($_POST['_codup_pr_cost_price'][$post_id]));

        }

    }


    /**
     * Adds a custom options page to WP Settings Tab
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_add_options_page() {

        add_submenu_page('woocommerce',"Profit Reports Settings","Profit Reports Settings","manage_options","codup-pr-settings", array($this,'codup_pr_populate_options'));

    }


    /**
     * Adds Custom Options Fields to WC-PR Settings Page
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_populate_options() {


        settings_fields('codup_pr_options');
        include plugin_dir_path(dirname( __FILE__ ) ).'admin/partials/wcpr-settings.tmpl.php';
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/main.css', array(), $this->version, 'all' );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/script.js', array( 'jquery' ), $this->version, false );
        
    }


    /**
     * Creates New Settings Feild Using WP Options API
     *
     * @sine 1.1.1.0
     * @access public
    */

    public function codup_pr_create_options() {

        register_setting('codup_pr_options','codup_pr_options',array($this,'codup_pr_sanitize_options'));
        
        add_settings_section('codup_pr_options','Profit Settings',array($this,'codup_pr_profit_details'),'codup_pr_profit_settings');
        add_settings_section('codup_pr_options','Overhead Settings',array($this,'codup_pr_overhead_details'),'codup_pr_overhead_settings');
        add_settings_field('codup_pr_profit_enabled','Enable Profit Reporting',array($this,'codup_pr_enable_profit_field'),'codup_pr_profit_settings','codup_pr_options' );
        add_settings_field('codup_pr_cost_type','Cost Type',array($this,'codup_pr_default_cost_field_type'),'codup_pr_profit_settings','codup_pr_options' );
        add_settings_field('codup_pr_profit_default_cost','Default Cost of Products',array($this,'codup_pr_default_profit_field'),'codup_pr_profit_settings','codup_pr_options' );
        add_settings_field('codup_pr_overhead_enabled','Enable Overhead Cost',array($this,'codup_pr_enable_overhead_field'),'codup_pr_overhead_settings','codup_pr_options' );
        add_settings_field('codup_pr_overhead_on','Overhead Calculated On',array($this,'codup_pr_default_overhead_field_on'),'codup_pr_overhead_settings','codup_pr_options' );
        add_settings_field('codup_pr_overhead_type','Overhead Type',array($this,'codup_pr_default_overhead_field_type'),'codup_pr_overhead_settings','codup_pr_options' );
        add_settings_field('codup_pr_profit_default_overhead','Overhead Cost',array($this,'codup_pr_default_overhead_field'),'codup_pr_overhead_settings','codup_pr_options' );
        
      
    }


    /**
     * Sanitize / Validates Custom ACVP Options
     *
     * @since 1.1.1.0
     * @access public
     * @params $options Array An Array of WP Options
    */

    public function codup_pr_sanitize_options($options) {

        if("" == $options['codup_pr_default_cost_profit'] || !isset($options['codup_pr_default_cost_profit'])) {

            //$options['codup_pr_enable_profit'] = 0;

        }

        if("" == $options['codup_pr_default_overhead'] || !isset($options['codup_pr_default_overhead'])) {

            //$options['codup_pr_enable_overhead'] = 0;

        }

        if(!is_numeric($options['codup_pr_default_overhead'])) {

            $options['codup_pr_default_overhead'] = 0;

        }

        if(!is_numeric($options['codup_pr_default_cost_profit'])) {

            $options['codup_pr_default_cost_profit'] = 0;

        }

       

        return $options;

    }


    /**
     * Echoes Settings Details
     *
     * @since 1.1.1.0
     * @access public
    */
  
    public function codup_pr_overhead_details() {

        echo "<p>Overhead Cost is deducted form the revenue to calculate product, just like cost. Overhead is set globally for all products.</p>";

    }


    /**
     * Echoes Settings Details
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_profit_details() {

        echo "<p>Cost Price is required to calculate profit. You can set cost of single product from 'Product > Edit Product'  Page.<br/>Default cost is used when product does not have a valid cost price.</p>";

    }


    /**
     * Echoes Option Fielf to Profit Section of Options Page
     *
     * @since 1.1.1.4
     * @access public
    */

    public function codup_pr_default_cost_field_type() {

        $options = get_option('codup_pr_options');

        switch($options['codup_pr_cost_type']) {

          case '1221':
                $opts = "<option selected value='1221'>Percentage (%)</option>
                        <option value='2112'>Fixed Value</option>";

                break;

          case '2112':
                $opts = "<option value='1221'>Percentage (%)</option>
                        <option selected value='2112'>Fixed Value</option>";

                break;

          default:
                $opts = "<option value='1221'>Percentage (%)</option>
                        <option selected value='2112'>Fixed Value</option>";

                break;

        }

        echo "<select name='codup_pr_options[codup_pr_cost_type] id='codup_pr_cost_type'>".$opts."</select>";

    }


    /**
     * Echoes Option Fielf to Profit Section of Options Page
     *
     * @since 1.1.1.4
     * @access public
    */

    public function codup_pr_default_overhead_field_type() {

        $options = get_option('codup_pr_options');

        switch($options['codup_pr_overhead_type']) {

          case '1221':
                $opts = "<option selected value='1221'>Percentage (%)</option>
                        <option value='2112'>Fixed Value</option>";

                break;

          case '2112':
                $opts = "<option value='1221'>Percentage (%)</option>
                        <option selected value='2112'>Fixed Value</option>";

                break;

          default:
                $opts = "<option value='1221'>Percentage (%)</option>
                        <option selected value='2112'>Fixed Value</option>";

                break;

        }

        echo "<select name='codup_pr_options[codup_pr_overhead_type]' id='codup_pr_overhead_type'>".$opts."</select>";

    }


    /**
     * Echoes Option Fielf to Profit Section of Options Page
     *
     * @since 1.1.1.4
     * @access public
    */

    public function codup_pr_default_overhead_field_on() {

        $options = get_option('codup_pr_options');

        switch($options['codup_pr_overhead_on']) {

          case '1331':
                $opts = "<option selected value='1331'>Cost of Products</option>
                        <option value='3113'>Order Value</option>";

                break;

          case '3113':
                $opts = "<option value='1331'>Cost of Products</option>
                        <option selected value='3113'>Order Value</option>";

                break;

          default:
                $opts = "<option value='1331'>Cost of Products</option>
                        <option selected value='3113'>Order Value</option>";

                break;

        }

        echo "<select name='codup_pr_options[codup_pr_overhead_on]' id='codup_pr_overhead_on'>".$opts."</select>";
        
    }


    /**
     * Echoes Option Fielf to Profit Section of Options Page
     *
     * @since 1.1.1.4
     * @access public
    */
    

    public function codup_pr_default_overhead_field() {

        $options = get_option('codup_pr_options');
        echo "<input type='text' id='codup_pr_profit_default_overhead' name='codup_pr_options[codup_pr_default_overhead]' value='{$options['codup_pr_default_overhead']}' />";

    }


    /**
     * Echoes Option Field to Profit Section of Options Page
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_enable_overhead_field() {

        $options = get_option('codup_pr_options');
        $checked = checked(1 == $options['codup_pr_enable_overhead'],true,false);
        echo "<input type='checkbox' id='codup_pr_overhead_enabled' name='codup_pr_options[codup_pr_enable_overhead]' value='1' $checked/>";

    }


    /**
     * Echoes Option Field to Profit Section of Options Page
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_enable_profit_field() {

        $options = get_option('codup_pr_options');
        $checked = checked(1 == $options['codup_pr_enable_profit'],true,false);
        echo "<input type='checkbox' id='codup_pr_profit_enabled' name='codup_pr_options[codup_pr_enable_profit]' value='1' $checked/>";

    }


    /**
     * Echoes Option Field to Profit Section of Options Page
     *
     * @since 1.1.1.0
     * @access public
     */

    public function codup_pr_default_profit_field() {

        $options = get_option('codup_pr_options');
        echo "<input type='text' id='codup_pr_profit_default_cost' name='codup_pr_options[codup_pr_default_cost_profit]' value='{$options['codup_pr_default_cost_profit']}' />";

    }


    /**
     * Adds Profit Reporting Page
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_profit_report_page() {

        add_menu_page("Profit Reports","Profit Reports","manage_options","codup_pr-profit",array($this,'codup_pr_render_profit_page'),'dashicons-analytics',3);
       

    }
      

    /**
     * Renders Profit Reporting Page
     *
     * @since 1.1.1.0
     * @access public
    */
 
    public function codup_pr_render_profit_page() {
       
     
        global $paged, $from_date, $to_date;
        
        $from_date = date('Y-m-d',strtotime("30 days ago"));
        
        $to_date = date('Y-m-d',strtotime("today"));
        
        $paged = ( $_GET["paged"] ) ? $_GET["paged"] : 1;
        
        if((isset($_GET["codup_pr_from_date"]) && "" != $_GET["codup_pr_from_date"] )|| ( isset($_GET["codup_pr_to_date"]) && "" != $_GET["codup_pr_to_date"])) {

            $from_date = explode("-",$_GET["codup_pr_from_date"]);

            $to_date = explode("-",$_GET["codup_pr_to_date"]);

        }
        else {

            $from_date = explode("-",date('Y-m-d',strtotime("30 days ago")));

            $to_date = explode("-",date('Y-m-d',strtotime("today")));

        }
        
        if(isset($_GET['search_id'])){
            $orderid = (int)$_GET['search_id'];
        }

        if(isset($_GET['status_id'])){
            $order_statuses = $_GET['status_id'] ;
        }  
        else{
            $order_statuses = array(       
                'wc-pending'    ,
                'wc-processing',
                'wc-on-hold'   ,
                'wc-completed' ,
                'wc-cancelled' ,
                'wc-refunded'  ,
                'wc-failed'    ,
            );
        }      
            $args = array(
                'post_type' => 'shop_order',
                'posts_per_page' => 15,
                'paged' => $paged,
                'post_status' => $order_statuses ,
                'p' => $orderid,
                'date_query' => array(
                    'relation' => 'AND',
                    array(
                        'after' => array(
                            'year' => $from_date[0],
                            'month' => $from_date[1],
                            'day' => $from_date[2]
                        ),
                        'inclusive' => true
                    ),
                    array(
                        'before' => array(
                            'year' => $to_date[0],
                            'month' => $to_date[1],
                            'day' => $to_date[2]
                        ),
                        'inclusive' => true
                    )
                )
            );
            
            $posts_query = new WP_Query($args);
            $currency_symbol = get_woocommerce_currency_symbol().' ';
            include plugin_dir_path(dirname( __FILE__ ) ).'admin/partials/wcpr-report.tmpl.php';
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/main.css', array(), $this->version, 'all' );

    }


    /**
     * Custom Pagination Function
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_custom_pagination($numpages = '', $pagerange = '', $paged='', $from_date , $to_date) {

        if (empty($pagerange)) {
            $pagerange = 2;
        }

        if (empty($paged)) {
            $paged = 1;
        }
        if ($numpages == '') {
            global $wp_query;
            $numpages = $wp_query->max_num_pages;
            if(!$numpages) {
                $numpages = 1;
            }
        }
        
        $pagination_args = array(
            'base'            =>  '%_%',
            'format'          => '?paged=%#%',
            'total'           => $numpages,
            'current'         => $paged,
            'show_all'        => false,
            'end_size'        => 1,
            'mid_size'        => $pagerange,
            'prev_next'       => false,
            'prev_text'       => __('&laquo;'),
            'next_text'       => __('&raquo;'),
            'type'            => 'plain',
            'add_args'        => false,
            'add_fragment'    => '',
            'before_page_number' => '',
	        'after_page_number'  => ''
        );
      
        $paginate_links = paginate_links($pagination_args);
        if ($paginate_links) {
            echo "<nav class='custom-pagination'>";
            echo "<span class='page-num'>Page " . $paged . " of " . $numpages . "</span> ";
            echo $paginate_links;
            echo "</nav>";
        }
          

    }


    /**
     * Gets Completed Orders
     *
     * @since 1.1.1.0
     * @access private
     * @return $orders Array List of Completed Orders
     */
    
    public function codup_pr_get_completed_orders($returnsum) {
        
        global $woocommerce;
        $total_profit = 0;

        $orders = array();

        $income_sum = 0;

        $args = array(
            'post_type' => 'shop_order',
            'post_status' => 'wc-completed',
            'posts_per_page' => -1,

        );

        foreach(get_posts($args) as $order){

            $order_obj = new WC_Order($order->ID);
            $order_date = get_the_date('d,M,Y',$order->ID);
            $order_profit = get_post_meta($order->ID,"_codup_pr_profit",true);
            
            $order_total = $order_obj->get_total();
            $order_url = $order_obj->get_view_order_url();
            $income_sum += $order_total;
            $total_profit += $order_profit; 

            array_push(
                $orders,
                array(
                    "ID" => $order->ID,
                    "date_completed" => $order_date,
                    "profit" => $order_profit,
                    "total" => $order_total,
                    "view_url" => $order_url
                )
            );
        }
        array_push($orders,$total_profit);
        
        return $returnsum ? $income_sum : $orders;

    }


    /**
     * Calculate Order Profit
     *
     * @since 1.1.1.0
     * @access public
     * @params int $orderid WooCommerce Order ID
    */

    public function codup_pr_calculate_profit($orderid) {

        global $woocommerce;

        $order = new WC_Order($orderid);

        $order_profit = $this->codup_pr_calculate_order_profit($order);
        $order_cost = $this->codup_pr_calculate_order_cost($order);
        update_post_meta($orderid,'_codup_pr_profit',number_format((float)$order_profit,2,'.',''));
        update_post_meta($orderid,'_codup_pr_order_cost',number_format((float)$order_cost,2,'.',''));

    }


    /**
     * Calculate cost of items in a order
     *
     * @since 1.1.1.0
     * @access private
     * @params array $orderitems A list of order items
     * @return int $order_cost Sum of Cost of All Items of a orders
    */

    private function codup_pr_calculate_order_cost($order) {

        global $woocommerce;

        $orderid = str_replace('#', '', $order->get_order_number());

        $orderitems = $order->get_items();

        $order_total = $order->get_total();

        $order_cost = 0;

        $options = get_option('codup_pr_options');

        $default_cost = $options['codup_pr_default_cost_profit'];

        $cost_type = $options['codup_pr_cost_type'];

        $overhead_enabled = $options['codup_pr_enable_overhead'];

        $overhead_type = $options['codup_pr_overhead_type']; 

        $overhead_value = $options['codup_pr_default_overhead']; // 0.2

        $overhead_on = $options['codup_pr_overhead_on']; //cost of pro 200

        $overhead_total = 0;

        if("2112" == $cost_type) {

          foreach($orderitems as $item) {


              $_prod_attrs = $item["variation_id"] ? get_post_meta($item["variation_id"]) : get_post_meta($item["product_id"]);
                  $_prod_cost_price = empty($_prod_attrs["_codup_pr_cost_price"][0]) ? $default_cost : $_prod_attrs["_codup_pr_cost_price"][0] ;
    
                  if(1 == $overhead_enabled) {
                    $overhead_total = $overhead_total + $this->codup_pr_calculate_overhead($overhead_on,$overhead_value,$overhead_type,$_prod_cost_price); // 10/100
                    $_prod_cost_price = $_prod_cost_price + $this->codup_pr_calculate_overhead($overhead_on,$overhead_value,$overhead_type,$_prod_cost_price);
                  }
                  $order_cost = $order_cost + ($_prod_cost_price * $item->get_quantity()); //$item["item_meta"]["_qty"][0]

          }
         
        }
      

        else if("1221" == $cost_type) {

          foreach($orderitems as $item) {


              $_prod_attrs = $item["variation_id"] ? get_post_meta($item["variation_id"]) : get_post_meta($item["product_id"]);
               $_prod_cost_price = empty($_prod_attrs["_codup_pr_cost_price"][0]) ? $this->codup_pr_get_amount_by_perc($item["total"],$default_cost) : $_prod_attrs["_codup_pr_cost_price"][0] ;
               
               if(1 == $overhead_enabled) {
                    $overhead_total = $overhead_total + $this->codup_pr_calculate_overhead($overhead_on,$overhead_value,$overhead_type,$_prod_cost_price);
                    $_prod_cost_price = $_prod_cost_price + $this->codup_pr_calculate_overhead($overhead_on,$overhead_value,$overhead_type,$_prod_cost_price);
                }
                $order_cost = $order_cost + ($_prod_cost_price); //$item["item_meta"]["_qty"][0]
            }
        }

        if(1 == $overhead_enabled && "1221" == $overhead_type && "3113" == $overhead_on) {
          $overhead_total = $this->codup_pr_get_amount_by_perc($order_total,$overhead_value);
          $order_cost = $order_cost + $this->codup_pr_get_amount_by_perc($order_total,$overhead_value);
         

        }
        else if(1 == $overhead_enabled && "2112" == $overhead_type ) {
          $overhead_total = $overhead_value;
          $order_cost = $order_cost + $overhead_value;
         
        }
       
        update_post_meta($orderid,'_codup_pr_order_ohead',number_format((float)$overhead_total,2,'.',''));
       
        
        return $order_cost;

    }


    private function codup_pr_calculate_overhead($oon,$oval,$otype,$total) {

      if("1221" == $otype && "1331" == $oon) {
          return $this->codup_pr_get_amount_by_perc($total,$oval);
      }
      else {
          return 0;
      }

    }

    private function codup_pr_get_amount_by_perc($percof,$perc) {

      return ($percof / 100) * $perc;

    }


    /**
     * Calculate Order Shipment & Taxes
     *
     * @since 1.1.1.0
     * @access private
     * @params stdObject $order Instance of WC_Order Class
     * @return int $shiptax Sum of Order Taxes and Shipments
    */

    private function codup_pr_calculate_shipment_tax($order) {

        $shiptax = 0;

        $shiptax += $order->get_total_tax();

        $shiptax += $order->calculate_shipping();

        return $shiptax;

    }


    /**
     * Calculate Order Profit
     *
     * @since 1.1.1.0
     * @access private
     * @params stdObject $order Instance of WC_Order Class
     * @return int $order_profit Profir of order
     * @uses codup_pr_calculate_shipment_tax, codup_pr_calculate_order_cost
    */

    private function codup_pr_calculate_order_profit($order) {

        $order_profit = 0;

        $order_total = $order->get_total();

        $order_profit = $order_total - $this->codup_pr_calculate_shipment_tax($order) - $this->codup_pr_calculate_order_cost($order);

        return $order_profit;

    }


    /**
     * Get number of users who have placed atleast one order.
     *
     * @since 1.1.1.4
     * @access private
     * @return int $user_count Total Users Who Place Atleast 1 Order
    */

    private function codup_pr_total_users_has_orders() {

        $count = 0;
        $users = get_users();
        foreach($users as $user) {
            $wc_user = new WC_Customer($user);
            if(wc_get_customer_order_count( $user->ID ) > 0) {
                $count = $count + 1;
            }
        }
        return $count;
    }


    /**
     * Calculate Total Users
     *
     * @since 1.1.1.0
     * @access private
     * @return int $users_count Total Number of Users
    */

    private function codup_pr_total_users() {

        $count = count_users();
        $users_count = $count["total_users"];

        return $users_count;

    }


    /**
     * Calculate Gross Income
     *
     * @since 1.1.1.0
     * @access private
     * @return int $gross_income Gross Income (Sum of Amount of All Orders)
    */

    private function codup_pr_gross_income() {

        global $woocommerce;

        $orders_income = $this->codup_pr_get_completed_orders(true);

        return $orders_income;


    }


    /**
     * Add Average Customer Value Meta Box
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_metabox() {

        wp_add_dashboard_widget(
            'codup_pr_dash_widget',
            'Average Customer Value',
            array($this,'codup_pr_populate_widget')
        );

    }
  


    /**
     * Calculates & Displays Average Customer Value
     *
     * @since 1.1.1.0
     * @access public
    */

    public function codup_pr_populate_widget() {
        $codup_avg_profit[] = $this->codup_pr_get_completed_orders(false);

        $codup_pr_income = $this->codup_pr_gross_income();

        $codup_pr_users_has_orders = $this->codup_pr_total_users_has_orders();

        $codup_pr_users = $this->codup_pr_total_users();

        $codup_pr_orders = count($this->codup_pr_get_completed_orders(false))-1;

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/main.css', array(), $this->version, 'all' );

        include plugin_dir_path(dirname( __FILE__ ) ).'admin/partials/wcpr-widget.tmpl.php';

    }

    // //Select orders Statuses
    // function wc_get_order_statuses() {
    //             $order_statuses = array(
    //                     'wc-pending'    => _x( 'Pending payment', 'Order status', 'woocommerce' ),
    //                     'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
    //                 'wc-on-hold'    => _x( 'On hold', 'Order status', 'woocommerce' ),
    //                     'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
    //                 'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
    //                     'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
    //                 'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
    //             );
    //             return apply_filters( 'wc_order_statuses', $order_statuses );
    //     }
        
    //     /**
    //      * See if a string is an order status.
    //  *
    //      * @param  string $maybe_status Status, including any wc- prefix.
    //      * @return bool
    //      */
    //     function wc_is_order_status( $maybe_status ) {
    //             $order_statuses = wc_get_order_statuses();
    //             return isset( $order_statuses[ $maybe_status ] );
    //     }
    
    
    
    // function wc_get_order_status_name( $status ) {
    //             $statuses = wc_get_order_statuses();
    //             $status   = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
    //             $status   = isset( $statuses[ 'wc-' . $status ] ) ? $statuses[ 'wc-' . $status ] : $status;
    //             return $status;
    //     }
    
    
    
    
    
    
    // function wc_processing_order_count() {
    //         return wc_orders_count( 'processing' );
    //     }
        
    //     /**
    //      * Return the orders count of a specific order status.
    //      *
    //      * @param string $status Status.
    //      * @return int
    //      */
    //     function wc_orders_count( $status ) {
    //             $count          = 0;
    //             $status         = 'wc-' . $status;
    //             $order_statuses = array_keys( wc_get_order_statuses() );
        
    //             if ( ! in_array( $status, $order_statuses, true ) ) {
    //                     return 0;
    //             }
        
    //             $cache_key    = WC_Cache_Helper::get_cache_prefix( 'orders' ) . $status;
    //             $cached_count = wp_cache_get( $cache_key, 'counts' );
        
    //             if ( false !== $cached_count ) {
    //                     return $cached_count;
    //             }
        
    //             foreach ( wc_get_order_types( 'order-count' ) as $type ) {
    //                     $data_store = WC_Data_Store::load( 'shop_order' === $type ? 'order' : $type );
    //                     if ( $data_store ) {
    //                             $count += $data_store->get_order_count( $status );
    //                     }
    //             }
        
    //             wp_cache_set( $cache_key, $count, 'counts' );
        
    //             return $count;
    //     }
    
               
               


}
