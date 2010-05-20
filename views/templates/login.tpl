<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $GLOBALS['lang']; ?>" lang="<?php echo $GLOBALS['lang']; ?>">
	<head>
		<title><?= __("TAO Delivery Server"); ?></title>
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>/css/layout.css" />
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>/css/form.css" />
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>/css/custom-theme/jquery-ui-1.8.custom.css" />
		<style media="screen">
			@import url(<?echo BASE_WWW; ?>/css/login.css);
		</style>
	</head>
	
	<body>
		<ul id="control">    
			<li></li>
		</ul>
		<div id="content">
			<h1 id="welcome_message"><img src="<?=BASE_WWW?>/img/taoDelivery.png" />&nbsp;<?= __("TAO Delivery Server"); ?></h1>
			<div id="business">
				<br />
				<?if(get_data('errorMessage')):?>
					<div class="ui-widget ui-corner-all ui-state-error error-message">
						<?=urldecode(get_data('errorMessage'))?>
					</div>
				<?endif?>
				<div id="login_title" class="ui-widget ui-widget-header ui-state-default ui-corner-top">
					<?=__("Please login")?>
				</div>
				<div id="login_form" class="ui-widget ui-widget-content ui-corner-bottom">
					<?=get_data('form')?>
				</div>
			</div>
		</div>
	</body>
</html>