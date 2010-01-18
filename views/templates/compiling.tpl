<?include('header.tpl')?>

<div class="main-container">
	<div class="ext-home-container ui-state-highlight">
		<h1><img src="<?=BASE_WWW?>img/taoDelivery.png" />Compilation of the Delivery <?=get_data('deliveryLabel')?></h1>
		<br/>
		
		<?if(get_data('isCompiled')):?>
			The delivery has been compiled on <?=get_data('compiledDate')?>.<br/><br/>
		<?endif;?>
		
		<a href="#" id="initCompilation" onclick="initCompilation('<?=get_data('deliveryUri')?>')">
			<?if(get_data('isCompiled')):?>
				Recompile
			<?else:?>
				Compile
			<?endif;?>
		</a>
		<br/>
		<br/>
		<br/>
		
		<script type="text/javascript" src="/taoDelivery/views/js/compiling.js"></script>
		
		<div id="tests"></div>
	</div>
</div>

<?include('footer.tpl')?>