<?php

/**
 * Implementation of core functionality.
 *
 *@since 1.1.1.0.
 *@package productor
 *@subpackage admin/productor
 *
 */

/**
 * Implementation of core functionality for WC_PR Admin.
 *
 * This is the core plugin class.
 *
 * @since 1.1.1.0.
 * @author sami@codup
 *
 */

class WC_PR {

    /**
     * The Loader to manage and register hooks, actions & filters.
     *
     * @since    1.1.1.0
     * @access   private
     * @var      string
     */
    private $loader;

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
     * Initialize the WC_PR Plugin Class & set it's properties.
     *
     * @since 1.1.1.0.
     * @param string $plugin_name Name of plugin.
     * @param string $version Version of plugin.
     */

    public function __construct($plugin_name,$version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->codup_pr_load_dependencies();
        $this->codup_pr_define_admin_hooks();
        $this->codup_pr_define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - WC_PR_Loader. Orchestrates the hooks of the plugin.
     * - WC_PR_Admin. Defines all hooks for the admin area.
     * - WC_PR_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.1.1.0
     * @access   private
     */

    private function codup_pr_load_dependencies() {

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcpr-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'lib/codupads/codupads.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wcpr-admin.php';

        $this->loader = new Prodcuctor_Loader($this->plugin_name,$this->version);
    }



    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.1.1.0
     * @access   private
     */

    private function codup_pr_define_admin_hooks() {

        $pr_admin = new WC_PR_Admin($this->plugin_name,$this->version);
       
        //add options page
        $this->loader->add_action('admin_menu',$pr_admin,'codup_pr_add_options_page');
        $this->loader->add_action('admin_init',$pr_admin,'codup_pr_create_options');
        

        $this->loader->add_action( 'wp_dashboard_setup',$pr_admin,'codup_pr_metabox');
        $options = get_option('codup_pr_options');
        
        if(1 == $options['codup_pr_enable_profit']){
           
          
            //add cost price input field
            $this->loader->add_action('woocommerce_product_options_pricing', $pr_admin, 'codup_pr_add_cost_input');
            $this->loader->add_action('woocommerce_product_after_variable_attributes', $pr_admin, 'codup_pr_add_variable_cost', 10, 3);
            
            //save cost price
            $this->loader->add_action('woocommerce_process_product_meta', $pr_admin, 'codup_pr_save_cost_price');
            $this->loader->add_action('woocommerce_save_product_variation', $pr_admin, 'codup_pr_save_variable_cost', 10, 2);
            
            //add profit reporting page
            $this->loader->add_action('admin_menu', $pr_admin, 'codup_pr_profit_report_page');
            
            //calculate profit on order processing
            $this->loader->add_action('woocommerce_order_status_completed', $pr_admin, 'codup_pr_calculate_profit');
            add_action('admin_enqueue_scripts', array($this, 'admin_load_scripts'));
        }
         
    }

    function admin_load_scripts(){
        wp_enqueue_style( 'select-styling',  plugin_dir_url( __FILE__ ). 'css/select2.min.css' );
        wp_enqueue_script( 'select-script',  plugin_dir_url( __FILE__ ). "js/select2.min.js", array( 'jquery' ), false, true );
        wp_enqueue_script( 'main-script',  plugin_dir_url( __FILE__ ). "js/script.js", array( 'jquery' ), false, true );
    }

    /**
     * Register all of the hooks related to the frontend functionality
     * of the plugin.
     *
     * @since    1.1.1.0
     * @access   private
     */

    private function codup_pr_define_public_hooks() {

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.1.1.0
     */

    public function codup_pr_run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.1.1.0
     * @return    string    The name of the plugin.
     */

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.1.1.0
     * @return    WC_PR_Loader    Orchestrates the hooks of the plugin.
     */

    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.1.1.0
     * @return    string    The version number of the plugin.
     */

    public function get_version() {
        return $this->version;
    }


}
