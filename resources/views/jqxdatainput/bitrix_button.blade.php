@if(config('medinfo.show_bitrix_button'))
    <script data-skip-moving="true">
        (function(w,d,u){
            var s=d.createElement('script');s.async=1;s.src=u+'?'+(Date.now()/60000|0);
            var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
        })(window,document,'https://cdn.bitrix24.ru/b2164961/crm/site_button/loader_9_o6dqy0.js');
    </script>
@endif