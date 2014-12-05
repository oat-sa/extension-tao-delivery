<section>
	<header>
        <h1><?=__('Select delivery mode')?></h1>
	</header>
	<div>
	   <ul class="contentList">
	       <?php foreach (get_data('models') as $uri => $label) :?>
	           <li class="contentButton" data-uri="<?=$uri?>"><?=$label?></li>
	       <?php endforeach;?>
	   </ul>
		<?=get_data('formContent')?>
	</div>
</section>
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
