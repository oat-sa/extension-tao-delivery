<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=tao_helpers_I18n::getLangCode()?>" lang="<?=tao_helpers_I18n::getLangCode()?>">
	<head>
		<title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
		<script type="text/javascript">
			var root_url = '<?=ROOT_URL?>';
			var base_url = '<?=BASE_URL?>';
			var taobase_www = '<?=TAOBASE_WWW?>';
			var base_www = '<?=BASE_WWW?>';
			var base_lang = '<?=strtolower(tao_helpers_I18n::getLangCode())?>';
		</script>
		<script src="<?=TAOBASE_WWW?>js/require-jquery.js"></script>
		<script src="<?=TAOBASE_WWW?>js/main.js"></script>
		<script src="<?=TAOBASE_WWW?>js/serviceApi/StateStorage.js"></script>
        <script src="<?=TAOBASE_WWW?>js/serviceApi/ServiceApi.js"></script>
        <script type="text/javascript">
            require(['require', 'jquery', 'json2'], function(req, $) {
            	$(function(){
            		$("#loader").css('display', 'none');
            
            		var serviceApi = <?=get_data('serviceApi')?>;
            		var $frame = $('#iframeDeliveryExec');
            		
            		var autoResize = function autoResize() {
						$frame = $('#iframeDeliveryExec');
						$frame.height($frame.contents().height());
					};
            		
            		if (jQuery.browser.msie) {
						$frame[0].onreadystatechange = function(){	
							if(this.readyState == 'complete'){
								setInterval(autoResize, 10);
							}
						};
					} else {		
						$frame[0].onload = function(){
							setInterval(autoResize, 10);
						};
					}
            		
            		serviceApi.loadInto($frame[0]);

            		serviceApi.onFinish(function() {
            			$.ajax({
            				url  		: <?= tao_helpers_Javascript::buildObject(_url('finishDeliveryExecution', 'DeliveryServer'))?>,
            				data 		: <?= tao_helpers_Javascript::buildObject(array('deliveryExecution' => get_data('deliveryExecution')))?>,
            				type 		: 'post',
            				dataType	: 'json',
            				success     : function(data) {
                				window.location = data.destination;
            				}
            			});
            		});
            	});
            });
        </script>
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css" />
		<style media="screen">
			@import url(<?echo BASE_WWW; ?>css/main.css);
		</style>

	</head>

	<body>
		<div id="process_view"></div>

		<ul id="control">
        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User name:"); ?> <span id="username"><?php echo get_data('userLabel'); ?></span> </span>
        		<span class="separator"></span>
        	</li>
         	<li>
         		<a class="action icon" id="logout" href="<?=_url('logout', 'DeliveryServerAuthentification')?>"><?php echo __("Logout"); ?></a>
         	</li>
		</ul>

		<div id="content" class='ui-corner-bottom'>
                <div id="tools">
                    <iframe id="iframeDeliveryExec" class="toolframe" frameborder="0" style="width:100%;overflow:hidden"></iframe>
				</div>
		</div>
		<!-- End of content -->
		<? include TAO_TPL_PATH .'footer/layout_footer_'.TAO_RELEASE_STATUS.'.tpl' ?>