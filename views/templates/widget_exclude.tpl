<?php if (get_data('groupcount') > 0) : ?>
<section>
	<header>
		<h1><?=__('Test-takers')?>
	</header>
	<div>
	   <div >
    	   <?php if (get_data('ttassigned') > 0) : ?>
    	       <?=__('Delivery is assigned to %s test-takers.', get_data('ttassigned')); ?>
	       <?php else: ?>
        	   <div class="feedback-info small">
            	   <span class="icon-info"></span>
        	       <?=__('Delivery is not assigned to any test-taker.'); ?>
    	       </div>
    	   <?php endif; ?>
	   </div>
	   <?php if (get_data('ttexcluded') > 0) : ?>
    	   <div class="feedback-info small">
    	       <span class="icon-info"></span>
    	       <?=__('%s test-taker(s) are excluded.', get_data('ttexcluded')); ?>
    	   </div>
	   <?php endif; ?>
	</div>
	<footer>
		<button id="exclude-btn" class="btn-info small" type="button"><?=__('Excluded test-takers')?></button>
	</footer>
</section>
<div id="modal-container" class="tao-scope">
    <div id="testtaker-form" class="modal"></div>	
</div>
<script type="text/javascript">
$('#exclude-btn').click(function() {
	jQuery('#testtaker-form').load('<?=_url('excludeTesttaker', 'Delivery')?>', {'uri' : '<?= get_data('assemblyUri')?>'}, function() {
		$('body').prepend($('#modal-container'));
		$('#testtaker-form').modal();
	});
})
</script>
<?php endif; ?>
