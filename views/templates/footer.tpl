<script type="text/javascript">
require(['jquery', 'layout/section', 'ui/feedback'], function($, section, feedback){
        section.on('show', function() {
            if(section.id === "manage_delivery_assembly"){
                $("form[name=form_1]").empty();
            }
        });

        <?php if(has_data('message')):?>
            feedback().info(<?=json_encode(get_data('message'))?>);
        <?php endif?>

});
</script>
