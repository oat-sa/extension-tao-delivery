<?include('header.tpl')?>

<div class="main-container">
	<div class="ext-home-container ui-state-highlight">
	
		<h1><img src="<?=BASE_WWW?>img/delivery_compilation.png" /><?=__('Compilation of the Process')?> <?=get_data('processLabel')?></h1>
		<br/>
		
		<?if(get_data('isCompiled')):?>
			<?=__('The delivery was last compiled on')?> <span id="compiledDate"><?=get_data('compiledDate')?></span>.<br/><br/>
		<?endif;?>
		
		<a href="#" id="initCompilation" onclick="initCompilation('<?=get_data('processUri')?>','<?=get_data('deliveryClass')?>')">
			<?if(get_data('isCompiled')):?>
				<?=__('Recompile')?>
			<?else:?>
				<?=__('Compile')?>
			<?endif;?>
		</a>
		<br/>
		<br/>
		<br/>
		
		<script type="text/javascript" src="/taoDelivery/views/js/deliveryCompiling.js"></script>
		
		<div id="progressbar"></div>
		<br/>
		<br/>
		<div id="testsContainer"></div>
	</div>
</div>

<?include('footer.tpl')?>
