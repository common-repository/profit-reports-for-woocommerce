(function($) {
    
    
    $.ajax({
        url: "https://codup.io/wpads.codup.io/",
        
        data: {
            pluginConfig: PluginConfig
        },
        type: 'GET',
        headers: {
            'X-Codup-Ads' : 'ae237uiew4222ghq'
        },
        success: appendAds
    });
    
    function appendAds(data) {

        data = JSON.parse(data);
        $('#codup-topad').html(data.topad);
        $('#codup-rightad').html(data.rightad);

    }
    
})(jQuery);