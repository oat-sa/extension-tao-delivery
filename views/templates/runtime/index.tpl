<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="<?=tao_helpers_I18n::getLangCode()?>"
    lang="<?=tao_helpers_I18n::getLangCode()?>">
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
<link rel="stylesheet" type="text/css"
    href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css" />
<style media="screen">
@import url(<?echo BASE_WWW; ?>css/main.css);
</style>
</head>

<body>
    <div id="process_view"></div>

    <ul id="control">
        <li>
	    <span id="connecteduser" class="icon" ><?php echo __("Logged in as:"); ?>
		<span
                id="username"><?php echo $login; ?></span>
		</span>
	    <span class="separator"></span>
	</li>
        <li><a id="logout" class="icon action"
            href="<?=_url('logout', 'DeliveryServerAuthentification')?>"><?php echo __("Logout"); ?></a>
        </li>
    </ul>
    
    <div id="content">
        <div class="contentHeader">
	   <?php echo __("Welcome:"); ?><b> <?php echo $login; ?>!</b>
	</div>
        <div id="business" class="deliveries ">
	    <?php if(count(get_data('startedDeliveries')) > 0) : ?>
		<h2 class="section_title"><?php echo __("Paused Test"); ?> <span class="counter">(<?php echo count($startedDeliveries); ?>)</span></h2>
		<div id="running_process" class="deliveries resume">
		    <ul>
		    <?php foreach ($startedDeliveries as $delivery): ?>
			<li>
			   
			    <a
			    href="<?=_url('resumeDeliveryExecution', 'DeliveryServer', null, array('uri' => $delivery->getUri()))?>">
				<span class="deliveryLabel">
			     <?php echo wfEngine_helpers_GUIHelper::sanitizeGenerisString($delivery->getLabel()); ?>
				</span>
				<span class="button">
				<?php echo __("Resume Test"); ?>
				 </span>
			    </a>
			</li>
		    <?php endforeach;  ?>
		    </ul>
		</div>
	    <?php endif; ?>

	    <!-- End of Active Processes -->
	    <?php if(count(get_data('availableDeliveries')) > 0) : ?>
		<h2 class="section_title"><?php echo __("Assigned Test"); ?> <span class="counter">(<?php echo count($availableDeliveries); ?>)</span></h2>
		<div id="new_process" class="deliveries start">
		    <ul>
			<?php foreach($availableDeliveries as $delivery) : ?>
			<li>
			    <a
				accesskey=""href="<?=_url('initDeliveryExecution', 'DeliveryServer', null, array('uri' => $delivery->getUri()))?>">
				<span class="deliveryLabel">
				<?php echo wfEngine_helpers_GUIHelper::sanitizeGenerisString($delivery->getLabel()); ?>
				</span>
				<span class="button">
				    <?php echo __("Take Test"); ?>
				</span>
			    </a>
			</li>
			<?php endforeach;  ?>
		    </ul>
		</div>
	    <?php endif; ?>

	    <!-- End of New Processes -->



	    <?php if(count(get_data('finishedDeliveries')) > 0) : ?>
		    <h2 class="section_title"><?php echo __("Finished tests"); ?></h2>
		    <div id="old_process" class="deliveries finished">
			<ul>
			<?php foreach($finishedDeliveries as $delivery) : ?>
			<li>
				<?php echo wfEngine_helpers_GUIHelper::sanitizeGenerisString($delivery->getLabel()); ?></a>
			</li>
			<?php endforeach;  ?>
			</ul>
		    </div>
	    <?php endif; ?>

	    </div>

    </div>
    <!-- End of content -->
		<? include TAO_TPL_PATH .'footer/layout_footer_'.TAO_RELEASE_STATUS.'.tpl' ?>