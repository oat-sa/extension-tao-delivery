<div>
	
	<h1><img src="<?=BASE_WWW?>img/compile.png" />&nbsp;&nbsp;<?=__('Compilation of the delivery')?> <?=get_data('processLabel')?></h1>
	
	<div style="margin:20px 10px;">
		<?=__('A delivery must to be compiled before being executed. The compilation can be done here.')?>
		<br/>
		<?=__('A recompilation is also required after modification on the tests composing a delivery.')?>
	</div>
	
	<div style="margin:0px 10px;">
		<img src="<?=BASE_WWW?>img/dialog-warning.png"/>&nbsp;<?=__('Note: please make sure that all tests that make up the delivery are well defined and set to "active" in the test extension before compiling the delivery.')?>
	</div>
	
	<?if(get_data('isCompiled')):?>
		<div style="margin:30px 10px;"><img src="<?=BASE_WWW?>img/info.png"/>&nbsp;<?=__('The delivery was last compiled on')?> <span id="compiledDate"><?=get_data('compiledDate')?></span>.</div>
	<?endif;?>
	
	<div style="margin:30px 10px;">
		<a href="#" id="initCompilation" onclick="initCompilation('<?=get_data('processUri')?>','<?=get_data('deliveryClass')?>')">
			<?if(get_data('isCompiled')):?>
				<?=__('Recompile')?> 
			<?else:?>
				<?=__('Compile')?>
			<?endif;?>
		</a>
	</div>
	
	<script type="text/javascript" src="<?=BASE_WWW?>js/deliveryCompiling.js"></script>
	
	<div id="progressbar" style="margin:20px 10px;"></div>
	
	<div id="testsContainer" style="margin:20px 10px;"></div>
	
	<div id="generatingProcess" style="margin:20px 10px;">
		<div id="generatingProcess_info" style="margin-bottom:10px;"><img style="position:relative;top:10px;margin-right:10px;" src="<?=BASE_WWW?>img/process-ajax-loader.gif"/><?=__('Generating the delivery process, please wait')?></div>
		<div id="generatingProcess_feedback"/>
	</div>
	
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#generatingProcess_info').hide();
});
</script>