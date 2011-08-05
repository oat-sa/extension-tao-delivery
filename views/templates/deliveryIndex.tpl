<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $GLOBALS['lang']; ?>" lang="<?php echo $GLOBALS['lang']; ?>">
	<head>
		<title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
		<script type="text/javascript" src="<?echo BASE_WWW; ?>/js/jquery.js"/></script>
		<script type="text/javascript" src="<?echo BASE_WWW; ?>/js/wfEngine.js"/></script>
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>/css/custom-theme/jquery-ui-1.8.custom.css" />
		<style media="screen">
			@import url(<?echo BASE_WWW; ?>/css/main.css);
		</style>
	</head>
	
	<body>
		<div id="process_view"></div>
		
		<ul id="control">
        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User name:"); ?> <span id="username"><?php echo $login; ?></span> </span>
        		<span class="separator"></span>
        	</li>
         	<li>
         		<a class="action icon" id="logout" href="<?php echo BASE_URL;?>/DeliveryServerAuthentification/logout"><?php echo __("Logout"); ?></a>
         	</li>
		</ul>
		
		<div id="content" class='ui-corner-bottom'>
			<h1 id="welcome_message"><img src="<?=BASE_WWW?>/img/taoDelivery_medium.png" alt='delivery' />&nbsp;<?= __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></h1>	
			<div id="business">
				<h2 class="section_title"><?php echo __("Active Deliveries"); ?></h2>
			<?php if(!empty($processViewData)) : ?>
			<table id="active_processes">
				<thead>
					<tr>
						<th><?php echo __("Status"); ?></th>
						<th><?php echo __("Delivery"); ?></th>
						<th><?php echo __("Start/Resume the test"); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($processViewData as $procData): ?>
					<tr>
						<td class="status"><img src="<?php echo BASE_WWW;?>/<?php echo GUIHelper::buildStatusImageURI($procData['status']); ?>"/></td>
						
						
						<td class="label"><?php echo GUIHelper::sanitizeGenerisString($procData['label']); ?></td>
		
						<td class="join">
							<?php if ($procData['status'] != 'Finished'): ?>
								<?php foreach ($procData['activities'] as $activity): ?>
									<?php if ($activity['may_participate']): ?>
									<a href="<?php echo BASE_URL;?>/ProcessBrowser/index?processUri=<?php echo urlencode($procData['uri']); ?>"><?php echo $activity['label']; ?></a>
									<?php else: ?>
									<span></span>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php else: ?>
								<span><?php echo __("Finished Test"); ?></span>
							<?php endif; ?>
						</td>
						<!--<td class="situation"><a href="#"><img onclick="openProcess('../../../WorkFlowEngine/index.php?do=processInstance&param1=<?php echo urlencode($procData['uri']); ?>')" src="<?php echo BASE_WWW;?>/<?php echo $GLOBALS['dir_theme']; ?>img/open_process_view.png"/></a></td>-->
					</tr>
					<?php endforeach;  ?>
				</tbody>
			</table>
			<?php else:?>
			<br/><br/>
			<?php endif; ?>
			
			<!-- End of Active Processes -->
			<?php if(!empty($availableProcessDefinition)) : ?>
				<h2 class="section_title"><?php echo __("Initialize new test"); ?></h2>
				<div id="new_process">
					<ul>
						<?php foreach($availableProcessDefinition as $procDef) : ?>
						<li>
							<a href="<?php echo BASE_URL;?>/DeliveryServer/ProcessAuthoring?processDefinitionUri=<?php echo urlencode($procDef->uriResource); ?>">
							<?php echo GUIHelper::sanitizeGenerisString($procDef->getLabel()); ?></a>
						</li>
						<?php endforeach;  ?>		
					</ul>	
				</div>
			<?php endif; ?>
			</div>
			
		</div>
		<!-- End of content -->
		<div id="footer">
			TAO<sup>&reg;</sup> - <?=date('Y')?> - A joint initiative of CRP Henri Tudor and the University of Luxembourg
		</div>
	</body>
</html>