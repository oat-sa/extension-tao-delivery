<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=__("TAO - An Open and Versatile Computer-Based Assessment Platform")?></title>

		<script type="text/javascript">
			var root_url = '<?=ROOT_URL?>';
			var base_url = '<?=BASE_URL?>';
			var taobase_www = '<?=TAOBASE_WWW?>';
			var base_www = '<?=BASE_WWW?>';
			var base_lang = '<?=strtolower(tao_helpers_I18n::getLangCode())?>';
		</script>
		<script src="<?=TAOBASE_WWW?>js/require-jquery.js"></script>
		<script src="<?=TAOBASE_WWW?>js/main.js"></script>

		<script type="text/javascript">
			window.processUri = '<?=urlencode($browserViewData['processUri'])?>';
			window.activityUri = '<?=urlencode($activity->uriResource)?>';
			window.activeResources = <?=$browserViewData['active_Resource']?>;

			function goToPage(page_str){
				$("#loader").css('display', 'block');
				$("#tools").empty();
				window.location.href = page_str;
			}

			require(['require', 'jquery', 'json2', "<?=BASE_WWW?>js/jquery.ui.taoqualDialog.js", "<?=BASE_WWW?>js/wfEngine.js", "<?=BASE_WWW?>js/process_browser.js"], function(req, $) {
				$(function(){
					$("#loader").css('display', 'none');
					// Back and next function bindings for the ProcessBrowser.
					$("#back").click(function(){
						$("#navigation").hide();
						goToPage('<?=BASE_URL;?>ProcessBrowser/back?processUri=<?=urlencode($browserViewData['processUri'])?>&activityUri=<?=urlencode($browserViewData['activityExecutionUri'])?>&nc=<?=$browserViewData['activityExecutionNonce']?>');
						$(this).unbind('click');
						$("#next").unbind('click');
					});

					$("#next").click(function(){
						$("#navigation").hide();
						goToPage('<?=BASE_URL;?>ProcessBrowser/next?processUri=<?=urlencode($browserViewData['processUri'])?>&activityUri=<?=urlencode($browserViewData['activityExecutionUri'])?>&nc=<?=$browserViewData['activityExecutionNonce']?>');
						$(this).unbind('click');
						$("#back").unbind('click');
					});

<?foreach($services as $service):?>
					var $aFrame = $('<iframe class="toolframe" frameborder="0" style="<?=$service['style']?>" src="<?=BASE_URL?>ProcessBrowser/loading"></iframe>').appendTo('#tools');
					$aFrame.unbind('load').load(function(){
						$(this).attr('src', "<?=$service['callUrl']?>");
						$(this).unbind('load');

						$(this).load(function() {
							// Auto adapt tool container regarding iframe heights
							var frame = this;
							var doc = frame.contentWindow || frame.contentDocument;

							if (doc.document) {
								doc = doc.document;
							}

							var oldHeight = $('#tools').height();
							var height = $(doc).height();
							$('#tools').height(height + oldHeight);
						});
					});

<?endforeach;?>
<?if(get_data('debugWidget')):?>

					$("#debug").click(function(){
						$("#debugWindow").toggle('slow');
					});
<?endif?>
				});
			});
		</script>

		<style media="screen">
			@import url(<?=BASE_WWW?>css/process_browser.css);
		</style>

	</head>

	<body>
		<div id="loader"><img src="<?=BASE_WWW?>img/ajax-loader.gif" /> <?=__('Loading next item...')?></div>
		<div id="process_view"></div>
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