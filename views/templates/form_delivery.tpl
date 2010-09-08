<?include('header.tpl')?>

<style type="text/css">
	div.data-container{
		width:48%;
	}
	
	#delivery-left-container{
		float:left;
		position:absolute;
		width:46%;
	}
	
	#delivery-main-container{
		margin-left:46%;
	}
	
	#test-container, #group-container, #campaign-container{
		margin-right:6px;
		margin-bottom:6px;
	}
	
	#form-compile, #form-container{
		margin-bottom:6px;
	}
	
	#form-compile{
		font-size:1em;
	}
	
	div.xhtml_form div .form_desc{
		width:50%;
	}
	div.xhtml_form div input, div.xhtml_form div select, div.xhtml_form div textarea, div.xhtml_form div.wysiwyg, div.xhtml_form ul.form-elt-list, div.xhtml_form div .form-elt-container{
		margin-left:50%;
	}
	#compileLink {
		border-color:#CCCCCC #AAAAAA #AAAAAA #CCCCCC;
		border-style:solid;
		border-width:1px;
		margin-left:0;
		margin-right:5px;
		padding:3px 6px;
		text-decoration:none;
	}
	
	#compileLink:hover{
		color: #D00239;
	}
	
	
</style>
   
<div id="delivery-left-container">
		<?if(get_data('authoringMode') == 'simple'):?>
		
		<?include('delivery_tests.tpl');?>
		<div class="breaker"></div>
		<?endif?>
		
		<?include('groups.tpl')?>
		<?include('subjects.tpl')?>
		<div class="breaker"></div>
		
		<?include('delivery_campaign.tpl')?>
		<div class="breaker"></div>
		
</div>

<div class="main-container" id="delivery-main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
	<!-- complie box not available in standalone mode-->
	<?if(!tao_helpers_Context::check('STANDALONE_MODE')):?>
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:0.5%;">
		<?=__("Compilation")?>
	</div>
	<div id="form-compile" class="ui-widget-content ui-corner-bottom">
		<div class="ext-home-container ui-state-highlight">
		<p>
		<?if(get_data('isCompiled')):?>
			<?=__('The delivery was last compiled on')?> <?=get_data('compiledDate')?>.
		<?else:?>
			<?=__('The delivery is not compiled yet')?>
		<?endif;?>
		</p>
		
		<span>
			<a id='compileLink' class='nav' href="<?=BASE_URL.'/Delivery/CompileView?uri='.tao_helpers_Uri::encode(get_data('uri')).'&classUri='.tao_helpers_Uri::encode(get_data('classUri'))?>">
				<img id='compileLinkImg' src="<?=BASE_WWW?>img/compile_small.png"/>
				<?if(get_data('isCompiled')):?>
					<?=__('Recompile')?> 
				<?else:?>
					<?=__('Compile')?>
				<?endif;?>
				
			</a>
		</span>
		
		<br/>
		
		</div>
	</div>
	<?endif;?>
	
	<?include('delivery_history.tpl');?>
	
</div>


<script type="text/javascript">
$(function(){
	//correct the display of the long label:

	// $("#compileLink,#compileLinkImg,.compileLink").click(function(){
		// $('a.nav').each(function(){
			// var href = $(this).attr('href');
			// if(href.indexOf('compileView')>0){
				// $(this).click();
				// return false;
			// }
		// });
		// return false;
	// });
});
</script>

<?include('footer.tpl');?>