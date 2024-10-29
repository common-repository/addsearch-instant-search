(function($, config){
    $(document).ready(function(){
        init();
    });

    function init() {
        if($('#customer_key').val() !== ''){
            $('table.addsearch-settings').addClass('show');

            toggleV2Config($('#installation_method').val());
            
            $('#installation_method').on('change', function(e){
                toggleV2Config($(this).val());
            });
        }else{
            $('table.addsearch-instructions').addClass('show');
        }
    }

    function toggleV2Config(val){
        if(val.indexOf('v2') !== -1){
            $('.v2config').hide();
            $('.' + val + 'config').show('slow');
        }else{
            $('.v2config').hide('show');
        }
    }
})(jQuery, addsearch_config);