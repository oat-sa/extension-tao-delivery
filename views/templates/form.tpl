<?php
use oat\tao\helpers\Template;

Template::inc('form.tpl', 'tao');
?>

<script>
    require(['jquery', 'uiBootstrap', 'helpers'], function($, uiBootstrap, helpers){
        uiBootstrap.tabs.bind('tabsshow', function(event, ui) {
            if(ui.index>0){
                $("form[name=form_1]").html('');
            }
        });

        <?if(has_data('message')):?>
        helpers.createMessage(<?=json_encode(get_data('message'))?>);
        <?endif?>

    });
</script>