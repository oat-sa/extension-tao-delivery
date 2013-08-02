<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=__("TAO - An Open and Versatile Computer-Based Assessment Platform")?></title>

		<script type="text/javascript">
			var taobase_www = '<?=TAOBASE_WWW?>';
			var root_url = '<?=ROOT_URL?>';
			var base_lang = '<?=strtolower(tao_helpers_I18n::getLangCode())?>';
		</script>
		<script src="<?=TAOBASE_WWW?>js/require-jquery.js"></script>
		<script src="<?=TAOBASE_WWW?>js/main.js"></script>
		
		<script type="text/javascript" src="<?=ROOT_URL?>wfEngine/views/js/wfApi/wfApi.min.js"></script>
		<script type="text/javascript" src="<?=ROOT_URL?>wfEngine/views/js/serviceApi/ServiceWfImpl.js"></script>
		<script type="text/javascript" src="<?=ROOT_URL?>wfEngine/views/js/WfRunner.js"></script>
		<script type="text/javascript">
			require(['require', 'jquery', 'json2'], function(req, $) {
				$("#debug").click(function(){
					$("#debugWindow").toggle('slow');
				});
				var wfRunner = new WfRunner(
					<?=json_encode(get_data('activityExecutionUri'))?>,
					<?=json_encode(get_data('processUri'))?>,
					<?=json_encode(get_data('activityExecutionNonce'))?>
				);
				<?foreach($services as $service):?>
					wfRunner.initService(<?=json_encode($service['resource']->getUri())?>,<?=json_encode($service['style'])?>,<?=json_encode($service['callUrl'])?>);
				<?endforeach;?>
				
				$("#back").click(function(){
					wfRunner.backward();
				});
				$("#next").click(function(){
					wfRunner.forward();
				});
				
				$("#loader").css('display', 'none');
			});		
		</script>
		
		<style media="screen">
			@import url(<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css);
			@import url(<?=BASE_WWW?>css/process_browser.css);
		</style>

	</head>

	<body>
		<div id="loader"><img src="<?=BASE_WWW?>img/ajax-loader.gif" /> <?=__('Loading next item...')?></div>
		<div id="process_view"></div>
        <?if(!has_data('allowControl') || get_data('allowControl')):?>
			<ul id="control">
	
	
	        	<li>
	        		<span id="connecteduser" class="icon"><?=__("User name:")?> <span id="username"><?=$userViewData['username']?></span></span>
	        		<span class="separator"></span>
	        	</li>
	
	
	         	<li>
	         		<a id="pause" class="action icon" href="<?=BASE_URL?>ProcessBrowser/pause?processUri=<?=urlencode($browserViewData['processUri'])?>"><?=__("Pause")?></a> <span class="separator"></span>
	         	</li>
	
	         	<?if(get_data('debugWidget')):?>
				<li>
					<a id="debug" class="action icon" href="#">Debug</a> <span class="separator"></span>
				</li>
	        	<?endif?>
	
	         	<li>
	         		<a id="logout" class="action icon" href="<?=BASE_URL?>DeliveryServerAuthentification/logout"><?=__("Logout")?></a>
	         	</li>
	
			</ul>

			<?if(get_data('debugWidget')):?>
					<div id="debugWindow" style="display:none;">
						<?foreach(get_data('debugData') as $debugSection => $debugObj):?>
						<fieldset>
							<legend><?=$debugSection?></legend>
							<pre>
								<?print_r($debugObj)?>
							</pre>
						</fieldset>
						<?endforeach?>
					</div>
			<?endif?>
		<?endif?>

		<div id="content">
			<div id="business">

				<div id="navigation">
					<?if(USE_PREVIOUS):?>
						<?if($browserViewData['controls']['backward']):?>
							<input type="button" id="back" value="<?= __("Back")?>"/>
						<?else:?>
							<input type="button" id="back" value="" style="display:none;"/>
						<?endif?>
					<?endif?>

					<?if($browserViewData['controls']['forward']): ?>
						<input type="button" id="next" value="<?= __("Forward")?>"/>
					<?else:?>
						<input type="button" id="next" value="" style="display:none;"/>
					<?endif?>
				</div>

				<div id="tools">
				</div>

			</div>

			<br class="clear" />
  		</div>
	<? include TAO_TPL_PATH .'footer/layout_footer_'.TAO_RELEASE_STATUS.'.tpl' ?>