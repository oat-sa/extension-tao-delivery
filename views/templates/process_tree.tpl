<div class="ui-widget-content ui-corner-bottom">
	
	<?$sectionName=get_data("section");?>
	
	<div id="tree-<?=$sectionName?>" ></div>
	<div id="tree-actions-<?=$sectionName?>" class="tree-actions">
		<input type="text"   id="filter-content-<?=$sectionName?>" value="*"  autocomplete='off'  size="10" title="<?=__('Use the * character to replace any string')?>" />
		<input type='button' id="filter-action-<?=$sectionName?>"  value="<?=__("Filter")?>" 	  /><br />
		<input type='button' id="open-action-<?=$sectionName?>"    value='<?=__("Open all")?>'  />
		<input type='button' id="close-action-<?=$sectionName?>"   value='<?=__("Close all")?>' />
	</div>
	
</div>
			
<script type="text/javascript">
	$(function(){
		new GenerisTreeClass('#tree-<?=$sectionName?>', authoringControllerPath+"getInstancesOf?instanceof=<?=$sectionName?>", {
			formContainer: 			"#<?=$sectionName?>_form",
			actionId: 				"<?=$sectionName?>",
			editInstanceAction: 	authoringControllerPath+"editInstance",
			createInstanceAction: 	authoringControllerPath+"addInstance",
			deleteAction: 			authoringControllerPath+"delete",
			duplicateAction: 		authoringControllerPath+"cloneInstance",
			instanceName:			"<?=$sectionName?>"
		});
	});
	
	$(function(){
	<?if($sectionName == "serviceDefinition"):?>
		//add a callback function to disable instantiating servicedefinition class directly
		$("#tree-serviceDefinition > ul > li:first").removeClass("node-class");
		// $("li[id=<?=tao_helpers_Uri::encode(CLASS_SERVICESDEFINITION)?>]").removeClass("node-class");
		// alert("id=<?=tao_helpers_Uri::encode(CLASS_SERVICESDEFINITION)?>and"+$("li[id=<?=tao_helpers_Uri::encode(CLASS_SERVICESDEFINITION)?>]").html());
		//alert("sth:"+$("#tree-serviceDefinition>ul ").html());
		//<li class="last"><a class="loading" href="#"><ins>&nbsp;</ins>Loading ...</a></li>
	<?endif;?>
	});
</script>