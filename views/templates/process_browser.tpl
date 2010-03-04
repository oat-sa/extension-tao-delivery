<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $GLOBALS['lang']; ?>" lang="<?php echo $GLOBALS['lang']; ?>">
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
			window.uiLanguage = '<?php echo $browserViewData['uiLanguage']; ?>';
			
			
			function goToPage(page_str){
				window.location.href = page_str;
		    }
		
		    $(document).ready(function (){

		
		
				
				
		    	
		       // Back and next function bindings for the ProcessBrowser.
		       $("#back").click(function(){
		      
			       			
				goToPage('<?php echo BASE_URL;?>/processBrowser/back?processUri=<?php echo urlencode($processUri); ?>');
		       					
			       			
		       			
		       		
		       	});
		       
		       if ($('#back_floating').length)
		       {
		       		$('#back_floating').click(function(){
			
			       					goToPage('<?php echo BASE_URL;?>/processBrowser/back?processUri=<?php echo urlencode($processUri); ?>');
		       					
			       	});
		       }	
		       	
			   $("#next").click(function(){
			   		
			       				goToPage('<?php echo BASE_URL;?>/processBrowser/next?processUri=<?php echo urlencode($processUri); ?>');
			   					
			   	});
			   	
			   if ($('#next_floating').length)
		       {
		       		$('#next_floating').click(function(){
			       		
			       					goToPage('<?php echo BASE_URL;?>/processBrowser/next?processUri=<?php echo urlencode($processUri); ?>');
			   				
			       	});
		       }	
		
		


			   window.addEventListener('click', mouseclickHandler, true);	  
		    });
		    
			
			$(window).load(function(){
			   adjustFloatingButtons();
			
			   <?php if (!$consistencyViewData['isConsistent']): 
			   // Consistency checking.
			   ?>
			   openConsistencyDialog('<?php echo $consistencyViewData['processExecutionUri']; ?>', 
			   						 '<?php echo $consistencyViewData['activityUri']; ?>', 
			   						 <?php echo GUIHelper::buildActivitiesList($consistencyViewData['involvedActivities']); ?>, 
			   						 '<?php echo addslashes($consistencyViewData['notification']); ?>',  
			   						 <?php echo ($consistencyViewData['suppressable']) ? 'true' : 'false'; ?>);
			   <?php endif; ?>
			});
			
		</script>
		
		<style media="screen">
			@import url(<?echo BASE_WWW; ?>css/process_browser.css);
		</style>

	</head>
	
	<body>
		<div id="process_view"></div>
		<ul id="control">
			
			
        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User Id."); ?> <span id="username"><?php echo $user; ?></span></span> <span class="separator" />
        	</li>
         	

         	<li>
         		<a id="pause" class="action icon" href="<?php echo BASE_URL;?>/processBrowser/pause?processUri=<?php echo urlencode($browserViewData['processUri']); ?>"><?php echo __("Pause"); ?></a> <span class="separator" />
         	</li>

         	
			
        	
         	
         
         	<li>
         		<a id="logout" class="action icon" href="<?php echo BASE_URL;?>/DeliveryServer/logout"><?php echo __("Logout"); ?></a>
         	</li>

		</ul>

		<div id="content">
			<div id="business">
		
				<div id="navigation">
					<?php if ($browserViewData['isBackable'] && USE_PREVIOUS): ?>
					<input type="button" id="back" value="<?php echo __("Back"); ?>"/>
					<?php endif; ?>
										
					<input type="button" id="next" value="<?php echo __("Forward"); ?>"/>
				</div>
			
				
				<div id="navigation_floating">
					<?php if ($browserViewData['isBackable']): ?>
						<input type="button" id="back_floating" value="&lt;&lt;"/>
					<?php endif; ?>
					<input type="button" id="next_floating" value="&gt;&gt;"/>
				</div>
				
				

			
				<div id="tools">
					<iframe frameborder="0" id="tools" src="<?php echo $services[0]->getCallUrl($variablesViewData);  ?>"/></iframe>
				</div>

			</div>
			
			<br class="clear" />
  		</div>
 

  		<div id="consistency" title="<?php echo ((!$consistencyViewData['isConsistent']) ? $consistencyViewData['source'] : '') . ' ' . __("Edit error"); ?>"></div>

	</body>

</html>