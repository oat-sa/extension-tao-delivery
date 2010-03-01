<div>
	
		<h1><img src="<?=BASE_WWW?>img/taoDelivery.png" /><?=__('Compilation of the Process')?> <?=get_data('processLabel')?></h1>
		<br/>
		
		<?if(get_data('isCompiled')):?>
			<?=__('The delivery was last compiled on')?> <span id="compiledDate"><?=get_data('compiledDate')?></span>.<br/><br/>
		<?endif;?>
		
		<a href="#" id="initCompilation" onclick="initCompilation('<?=get_data('processUri')?>')">
			<?if(get_data('isCompiled')):?>
				<?=__('Recompile')?>
			<?else:?>
				<?=__('Compile')?>
			<?endif;?>
		</a>
		<br/>
		<br/>
		<br/>
		
		<script type="text/javascript" src="/taoDelivery/views/js/compiling.js"></script>
		<div id="progressbar"></div>
		<br/>
		<br/>
		<div id="testsContainer"></div>
	
</div>

