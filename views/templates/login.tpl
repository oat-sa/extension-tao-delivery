<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?= __("TAO Delivery Server"); ?></title>
	 	<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/layout.css" />
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/form.css" />
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css" />

		<style media="screen">
			@import url(<?echo BASE_WWW; ?>css/login.css);
		</style>
		<script type="text/javascript">
			var root_url = '<?=ROOT_URL?>';
			var base_url = '<?=BASE_URL?>';
			var taobase_www = '<?=TAOBASE_WWW?>';
			var base_www = '<?=BASE_WWW?>';
			var base_lang = '<?=strtolower(tao_helpers_I18n::getLangCode())?>';
		</script>
		<script src="<?=TAOBASE_WWW?>js/require-2.0.6.js"></script>
		<script src="<?=TAOBASE_WWW?>js/main.js"></script>
		<script type="text/javascript" src="<?=TAOBASE_WWW?>js/login.js"></script>
	</head>

	<body style="background-color:#FFFFFF;">
		<ul id="control">
			<li></li>
		</ul>
		<div id="content" class='ui-corner-bottom'>
			<h1 id="welcome_message"><img src="<?=BASE_WWW?>/img/taoDelivery_medium.png" alt='delivery' />&nbsp;<?= __("TAO Delivery Server"); ?></h1>
			<div id="business">
				<div id="login-box">
					<?if(get_data('errorMessage')):?>
						<div class="ui-widget ui-corner-all ui-state-error error-message">
							<?=urldecode(get_data('errorMessage'))?>
						</div>
						<br />
					<?endif?>
					<div id="login-title" class="ui-widget ui-widget-header ui-state-default ui-corner-top">
						<?=__("Please login")?>
					</div>
					<div id="login-form" class="ui-widget ui-widget-content ui-corner-bottom">
						<?=get_data('form')?>
					</div>
				</div>

			</div>
		</div>


		<? include TAO_TPL_PATH .'footer/layout_footer_'.TAO_RELEASE_STATUS.'.tpl' ?>

