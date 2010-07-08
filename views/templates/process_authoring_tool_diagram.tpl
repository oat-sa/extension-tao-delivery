<?include('header.tpl')?>



<?if(get_data('error')):?>

	<div class="main-container">
		<div class="ui-state-error ui-corner-all" style="padding:5px;">
			<?=__('Please select a delivery before authoring it!')?>
			<br/>
			<?=get_data('errorMessage')?>
		</div>
		<br />
		<span class="ui-widget ui-state-default ui-corner-all" style="padding:5px;">
			<a href="#" onclick="selectTabByName('manage_deliveries');"><?=__('Back')?></a>
		</span>
	</div>
	
<?else:?>
	<link rel="stylesheet" type="text/css" href="/<?=get_data('extension')?>/views/css/process_authoring_tool.css" />
	
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/authoringConfig.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/json2.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/util.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/arrows.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/activityDiagram.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/modeController.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/modeInitial.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/modeActivityLabel.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/modeActivityAdd.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/modeActivityMenu.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/modeArrowLink.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/modeActivityMove.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/modeConnectorMove.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/modeArrowEdit.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/modeLinkedActivityAdd.js"></script>
	
	<script type="text/javascript">
	//init:
	var canvas = "#process_diagram_container";
	ActivityDiagramClass.canvas = "#process_diagram_container";
	ArrowClass.canvas = ActivityDiagramClass.canvas;
	var processUri = "<?=get_data("processUri")?>";
	ActivityDiagramClass.localNameSpace = "<?=tao_helpers_Uri::encode(core_kernel_classes_Session::singleton()->getNameSpace().'#')?>";
	
	ModeArrowLink.tempId = "defaultConnectorId";
	
	//constants:
	const RDFS_LABEL = "<?=tao_helpers_Uri::encode(RDFS_LABEL)?>";
	
	
	$(function() {
		window.loadFirebugConsole();
		
		$(ActivityDiagramClass.canvas).scroll(function(){
			//TODO: set a more cross-browser way to retrieve scroll left and top values:
			ActivityDiagramClass.scrollLeft = this.scrollLeft;
			ActivityDiagramClass.scrollTop = this.scrollTop;
		});
		
		// $(ArrowClass.canvas).mousemove(function(e){
			  // $('#status').html(e.pageX +', '+ e.pageY);
		// });
		
		try{
			
			// ActivityDiagramClass.drawActivity("ActivityTempId", {
				// left: 150,
				// top: 100
			// },
			// 'ActivityTemp');
			
			// ActivityDiagramClass.setActivityMenuHandler("ActivityTempId");
			// console.log('ModeActivityMenu', ModeActivityMenu);
			// ModeActivityMenu.on("ActivityTempId");
			
			// ActivityDiagramClass.feedDiagram();
			// ActivityDiagramClass.drawDiagram();
			ActivityDiagramClass.loadDiagram();
			
		}
		catch(err){
			console.log('feed&draw diagram exception', err);
		}
		
	});

	</script>

	<div class="main-container" style="display:none;"></div>
	<div id="authoring-container" class="ui-helper-reset">
	<div id="process_center_panel">
		<div id="process_diagram_feedback" ></div>
		<div id="process_diagram_container" >
			<div id="status"/>
		</div>
	</div>
	<div id="accordion1" style="font-size:0.8em;">
		<h3><a href="#"><?=__('Service Definition')?></a></h3>
		<div>
			<div id="serviceDefinition_tree"/>
			<div id="serviceDefinition_form"/>
		</div>
		<h3><a href="#"><?=__('Formal Parameter')?></a></h3>
		<div>
			<div id="formalParameter_tree"/>
			<div id="formalParameter_form"/>
		</div>
		<h3><a href="#"><?=__('Role')?></a></h3>
		<div>
			<div id="role_tree"/>
			<div id="role_form"/>
		</div>
		<h3><a href="#"><?=__('Process Variables')?></a></h3>
		<div>
			<div id="variable_tree"/>
			<div id="variable_form"/>
		</div>
	</div><!--end accordion -->
	
	<div id="accordion_container_2">
	<div id="accordion2" style="font-size:0.8em;">
		<h3><a href="#"><?=__('Activity Editor')?></a></h3>
		<div>
			<div id="activity_menu">
				<a href="#" id="activity_menu_addActivity">Add Activity</a><br/><br/>
			</div>
			<div id="activity_tree"/>
			<div id="activity_form"/>
		</div>
		<!--<h3><a href="#">Specialized form</a></h3>
		<div>
			<div id="spForm"><a id="ancre_spForm" href="#">spForm</a></div>
		</div>-->
		<h3><a href="#"><?=__('Process Property')?></a></h3>
		<div>
			<!--<div id="process_info"><?=__('loading...')?></div>-->
			<div id="process_form"><?=__('loading...')?></div>
		</div>
		<h3><a href="#"><?=__('Compilation')?></a></h3>
		<div>
			<div id="compile_info"><?=__('loading...')?></div>
			<div id="compile_form"></div>
		</div>
	</div><!--end accordion -->
	</div><!--end accordion_container_2 -->
	
	</div><!--end authoring-container -->
	
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/activity.tree.js"></script>
	<script type="text/javascript">
	
	
	
	$(function(){
		 
	
		// EventMgr.unbind('activityAdded');
		EventMgr.unbind();
		
		EventMgr.bind('activityAdded', function(event, response){
			var activity = ActivityDiagramClass.feedActivity({
				"data": response.label,
				"attributes": {"id": response.uri}
			});
			//draw activity with the default positionning:
			ActivityDiagramClass.drawActivity(activity.id);
			ActivityDiagramClass.setActivityMenuHandler(activity.id);
			
			//draw arrow if need be (i.e. in case of adding an activity with a connector)
			if(response.previousConnectorUri && response.port){
				//should be a connector:
				var previousObjectId = ActivityDiagramClass.getIdFromUri(response.previousConnectorUri);
				var originEltId = ActivityDiagramClass.getActivityId('connector', previousObjectId);
				var arrowId = ActivityDiagramClass.getActivityId('connector', previousObjectId, 'bottom', response.port);
				
				var activityId = ActivityDiagramClass.getActivityId('container', activity.id);
				ActivityDiagramClass.positionNewActivity($('#'+originEltId), $('#'+activityId));
				
				//create and draw arrow:
				ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId), $('#'+connectorTopId), 'top', new Array(), false);
				ArrowClass.drawArrow(arrowId, {
					container: ActivityDiagramClass.canvas,
					arrowWidth: 2
				});
				
				//save diagram:
				ActivityDiagramClass.saveDiagram();
			}
			
		});
		
		EventMgr.bind('connectorAdded', function(event, response){
			try{
				//a connector is always added throught the "linked mode"
				var previousObjectId = ActivityDiagramClass.getIdFromUri(response.previousActivityUri);
				if(response.previousIsActivity){
					var originEltId = ActivityDiagramClass.getActivityId('activity', previousObjectId);
					var arrowId = ActivityDiagramClass.getActivityId('activity', previousObjectId, 'bottom');
				}else{
					//should be a connector:
					var originEltId = ActivityDiagramClass.getActivityId('connector', previousObjectId);
					var arrowId = ActivityDiagramClass.getActivityId('connector', previousObjectId, 'bottom', response.port);
				}
				var connector = ActivityDiagramClass.feedConnector(
					{
						"data": response.label,
						"attributes": {"id": response.uri},
						"type": response.type
					},
					null,
					originEltId,
					null
				);
				
				//draw connector and reposition it:
				var connectorId = ActivityDiagramClass.getActivityId('connector', connector.id);
				var connectorTopId = ActivityDiagramClass.getActivityId('connector', connector.id, 'top');
				
				ActivityDiagramClass.drawConnector(connector.id);
				ActivityDiagramClass.positionNewActivity($('#'+originEltId), $('#'+connectorId));
				ActivityDiagramClass.setConnectorMenuHandler(connector.id);
				
				//create and draw arrow:
				ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId), $('#'+connectorTopId), 'top', new Array(), false);
				ArrowClass.drawArrow(arrowId, {
					container: ActivityDiagramClass.canvas,
					arrowWidth: 2
				});
				
				//save diagram:
				ActivityDiagramClass.saveDiagram();
			}catch(ex){
				console.log('connectorAdded exception:', ex);
				console.log('connector', connector);
				console.log('originEltId', originEltId);
				console.log('connectorId', connectorId);
				console.log('arrowId', arrowId);
			}
				
		});
		
		
		EventMgr.bind('activityPropertiesSaved', function(event, response){
			//simply reload the tree:
			console.log('activityPropertiesSaved');
			var anActivityTree = ActivityTreeClass.instances[ActivityDiagramClass.relatedTree];
			if(anActivityTree){
				console.log('refreshing tree');
				var aJsTree = anActivityTree.getTree();
				ActivityTreeClass.refreshTree({
					TREE_OBJ: aJsTree
				});
			}
			
		});
		
		$(ActivityDiagramClass.canvas).click(function(evt){
			if (evt.target == evt.currentTarget) {
				ModeController.setMode('ModeInitial');
			}
		});
		
		$("#activity_menu_addActivity").click(function(event){
			try{
				event.preventDefault();
				GatewayProcessAuthoring.addActivity(authoringControllerPath+"addActivity", processUri);
			}
			catch(err){
				console.log('addactivity on click:', err);
			}
		});
		
		$("#accordion1").accordion({
			fillSpace: false,
			autoHeight: false,
			collapsible: false,
			active: 0,
			icons: { 'header': 'ui-icon-plus', 'headerSelected': 'ui-icon-minus' }
		});
		
		//load activity tree:
		loadActivityTree();
		
		
		//load the trees:
		loadSectionTree("serviceDefinition");//use get_value instead to get the uriResource of the service definition class and make
		/*loadSectionTree("formalParameter");
		loadSectionTree("role");
		loadSectionTree("variable");
		
		processProperty();
		
		loadCompilationForm();
		*/
	});
	
	$(function(){
		$("#accordion2").accordion({
			fillSpace: false,
			autoHeight: false,
			collapsible: false,
			
			icons: { 'header': 'ui-icon-plus', 'headerSelected': 'ui-icon-minus' }
		});
		
		//load the trees:
		
	});
	
	function processProperty(){
		_load("#process_form", 
			authoringControllerPath+"editProcessProperty", 
			{processUri: processUri}
		);
	}
	
	function loadSectionTree(section){
	//section in [serviceDefinition, formalParameter, role]
		$.ajax({
			url: authoringControllerPath+'getSectionTrees',
			type: "POST",
			data: {section: section},
			dataType: 'html',
			success: function(response){
				$('#'+section+'_tree').html(response);
			}
		});
	}
	
	function loadActivityTree(){
		$.ajax({
			url: authoringControllerPath+'getActivityTree',
			type: "POST",
			data: {section: "activity"},
			dataType: 'html',
			success: function(response){
				$('#activity_tree').html(response);
			}
		});
	}
	
	function loadCompilationForm(){
		$.ajax({
			url: authoringControllerPath+'compileView',
			type: "POST",
			data: {processUri: processUri},
			dataType: 'html',
			success: function(response){
				$('#compile_info').html(response);
			}
		});
	}
	</script>
	
<?endif?>

<?include('footer.tpl')?>
