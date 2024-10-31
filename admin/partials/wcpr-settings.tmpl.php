<?php echo do_shortcode("[codup_ads_top]"); ?>
<div id="co-adapi" class="wrap codup-settings-page">
    <h2>Profit Reports - Settings</h2>
    <div class="divider"></div>
    <form action="options.php" method="post">

        <?php settings_fields('codup_pr_options'); ?>
        <?php do_settings_sections('codup_pr_profit_settings'); ?>

        <div class="divider"></div>

        <?php do_settings_sections('codup_pr_overhead_settings'); ?>
        <br/><br/>

        <input name="Submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
</div>

