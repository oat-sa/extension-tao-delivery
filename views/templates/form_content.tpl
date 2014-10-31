<div class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select delivery mode')?>
	</div>
	<div class="ui-widget ui-widget-content ui-state-highlight container-content">
	   <ul class="contentList">
	       <?php foreach (get_data('models') as $uri => $label) :?>
	           <li class="contentButton" data-uri="<?=$uri?>"><?=$label?></li>
	       <?php endforeach;?>
	   </ul>
		<?=get_data('formContent')?>
	</div>
	<div class="emptyContentFooter ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
	</div>	
</div>
<script type="text/javascript">
require(['jquery', 'i18n', 'ui/feedback', 'generis.tree.select'], function($, __, feedback) {
    $('.contentButton').click(function(){
                $.ajax({
            url: "<?=get_data('saveUrl')?>",
            type: "POST",
            data: {'model': $(this).data('uri')},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    feedback().info(__('Content driver selected'));
                }
                $('.clicked').trigger("click");
            }
                });			
    });
});
</script>
