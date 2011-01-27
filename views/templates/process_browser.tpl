<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
		
		<script type="text/javascript" src="<?echo BASE_WWW; ?>js/jquery.js"></script>
		<script type="text/javascript" src="<?echo BASE_WWW; ?>js/jquery.ui.js"></script>
		<script type="text/javascript" src="<?echo BASE_WWW; ?>js/jquery.json.js"></script>	
		<script type="text/javascript" src="<?echo BASE_WWW; ?>js/jquery.ui.taoqualDialog.js"></script>
		<script type="text/javascript" src="<?echo BASE_WWW; ?>js/wfEngine.js"></script>
		<script type="text/javascript" src="<?echo BASE_WWW; ?>js/process_browser.js"></script>
			
		<script type="text/javascript">
			window.processUri = '<?php echo urlencode($processUri); ?>';
			window.activityUri = '<?php echo urlencode($activity->uri); ?>';
			window.activeResources = <?php echo $browserViewData['active_Resource']; ?>;
			
			function goToPage(page_str){
				$("#loader").css('display', 'block');
				$("#tools").empty();
				window.location.href = page_str;
			 }
		
		    $(document).ready(function (){

		    	$("#loader").css('display', 'none');
		    	
		       // Back and next function bindings for the ProcessBrowser.
		       $("#back").click(function(){
				goToPage('<?php echo BASE_URL;?>/ProcessBrowser/back?processUri=<?php echo urlencode($processUri); ?>');
		       });
		       
		       	
			   $("#next").click(function(){
			       	goToPage('<?php echo BASE_URL;?>/ProcessBrowser/next?processUri=<?php echo urlencode($processUri); ?>&activityExecutionUri=<?php echo urlencode($browserViewData['activityExecutionUri']);?>');
			   	});
			   	

			   <?if(get_data('debugWidget')):?>

				$("#debug").click(function(){
					$("#debugWindow").toggle('slow');
				});
				
				<?endif?>  
		    });
		    
			
		</script>
		
		<style media="screen">
			@import url(<?echo BASE_WWW; ?>css/process_browser.css);
		</style>

	</head>
	
	<body>
		<div id="loader"><img src="<?=BASE_WWW?>img/ajax-loader.gif" /> <?=__('Loading next item...')?></div>
		<div id="process_view"></div>
		<ul id="control">
			
			
        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User name:"); ?> <span id="username"><?php echo $userViewData['username']; ?></span></span> <span class="separator" />
        	</li>
         	

         	<li>
         		<a id="pause" class="action icon" href="<?php echo BASE_URL;?>/processBrowser/pause?processUri=<?php echo urlencode($browserViewData['processUri']); ?>"><?php echo __("Pause"); ?></a> <span class="separator" />
         	</li>

         	<?if(get_data('debugWidget')):?>
			<li>
				<a id="debug" class="action icon" href="#">Debug</a> <span class="separator" />
			</li>
        	<?endif?>
			
         	<li>
         		<a id="logout" class="action icon" href="<?php echo BASE_URL;?>/DeliveryServerAuthentification/logout"><?php echo __("Logout"); ?></a>
         	</li>

		</ul>
		
		<?if(get_data('debugWidget')):?>
				<div id="debugWindow" style="display:none;">
					<?foreach(get_data('debugData') as $debugSection => $debugObj):?>
					<fieldset>
						<legend><?=$debugSection?></legend>
						<pre>
							<?print_r($debugObj);?>
						</pre>
					</fieldset>
					<?endforeach?>
				</div>
		  <?endif?>

		<div id="content">
			<div id="business">
		
				<div id="navigation">
					<?if(USE_PREVIOUS): ?>
						<?if($browserViewData['controls']['backward']):?>
							<input type="button" id="back" value="<?= __("Back")?>"/>
						<?else:?>
							<input type="button" id="back" value="" style="display:none;"/>
						<?endif?>
					<?endif?>
					
					<?if ($browserViewData['controls']['forward']): ?>				
						<input type="button" id="next" value="<?= __("Forward")?>"/>
					<?else:?>
						<input type="button" id="next" value="" style="display:none;"/>
					<?endif?>
				</div>
				
				
			
				<div id="tools">
					<?php foreach($services as $service): ?>
					<iframe frameborder="0" style="<?php echo $service->getStyle();?>" src="<?php echo $service->getCallUrl($variablesViewData);?>"></iframe>
					<?php endforeach;?>
				</div>

			</div>
			
			<br class="clear" />
  		</div>

		<div id="footer">
			TAO<sup>&reg;</sup> - <?=date('Y')?> - A joint initiative of CRP Henri Tudor and the University of Luxembourg
		</div>
	</body>

</html>