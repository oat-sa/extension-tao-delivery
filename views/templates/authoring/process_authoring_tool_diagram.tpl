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
			<a href="#" onclick="helpers.selectTabByName('manage_deliveries');"><?=__('Back')?></a>
		</span>
	</div>

<?else:?>
	<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/process_authoring_tool.css" />

	<script type="text/javascript">
		//constants:
		RDFS_LABEL = "<?=tao_helpers_Uri::encode(RDFS_LABEL)?>";
		PROPERTY_CONNECTORS_TYPE = "<?=tao_helpers_Uri::encode(PROPERTY_CONNECTORS_TYPE)?>";
		INSTANCE_TYPEOFCONNECTORS_SEQUENCE = "<?=tao_helpers_Uri::encode(INSTANCE_TYPEOFCONNECTORS_SEQUENCE)?>";
		INSTANCE_TYPEOFCONNECTORS_CONDITIONAL = "<?=tao_helpers_Uri::encode(INSTANCE_TYPEOFCONNECTORS_CONDITIONAL)?>";
		INSTANCE_TYPEOFCONNECTORS_PARALLEL = "<?=tao_helpers_Uri::encode(INSTANCE_TYPEOFCONNECTORS_PARALLEL)?>";
		INSTANCE_TYPEOFCONNECTORS_JOIN = "<?=tao_helpers_Uri::encode(INSTANCE_TYPEOFCONNECTORS_JOIN)?>";
	</script>

	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/authoringConfig.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/json2.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/util.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/arrows.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/activityDiagram.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/modeController.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/modeInitial.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/modeActivityLabel.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/modeActivityAdd.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/modeActivityMenu.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/modeArrowLink.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/modeActivityMove.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/modeConnectorMove.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/modeArrowEdit.js"></script>
	<script type="text/javascript" src="<?=BASE_WWW?>js/authoring/modeLinkedActivityAdd.js"></script>

	<script type="text/javascript">
	//init:
	var canvas = "#process_diagram_container";
	ActivityDiagramClass.canvas = "#process_diagram_container";
	ArrowClass.canvas = ActivityDiagramClass.canvas;
	var processUri = "<?=get_data("processUri")?>";
	ActivityDiagramClass.localNameSpace = "<?=tao_helpers_Uri::encode(core_kernel_classes_Session::singleton()->getNameSpace().'#')?>";

	ModeArrowLink.tempId = "defaultConnectorId";

	$(function() {
		// window.loadFirebugConsole();

		$(ActivityDiagramClass.canvas).scroll(function(){
			//TODO: set a more cross-browser way to retrieve scroll left and top values:
			ActivityDiagramClass.scrollLeft = this.scrollLeft;
			ActivityDiagramClass.scrollTop = this.scrollTop;
		});

		// $(ArrowClass.canvas).mousemove(function(e){
			  // $('#status').html(e.pageX +', '+ e.pageY);
		// });

		try{



			// ActivityDiagramClass.setActivityMenuHandler("ActivityTempId");
			// console.log('ModeActivityMenu', ModeActivityMenu);
			// ModeActivityMenu.on("ActivityTempId");

			// ActivityDiagramClass.feedDiagram();
			// ActivityDiagramClass.drawDiagram();
			ActivityDiagramClass.loadDiagram();

		}
		catch(err){
			//console.log('feed&draw diagram exception', err);
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

	<script type="text/javascript" src="<?=BASE_WWW?>js/activity.tree.js"></script>
	<script type="text/javascript">



	$(function(){


		// eventMgr.unbind('activityAdded');
		eventMgr.unbind();

		eventMgr.bind('activityAdded', function(event, response){
			//console.log('adding act response:', response);
			try{
				var activity = ActivityDiagramClass.feedActivity({
					"data": response.label,
					"attributes": {"id": response.uri}
				});

				//console.log('activity', activity);

				//draw activity with the default positionning:
				ActivityDiagramClass.drawActivity(activity.id);
				ActivityDiagramClass.setActivityMenuHandler(activity.id);

				//draw arrow if need be (i.e. in case of adding an activity with a connector)
				if(response.previousConnectorUri && response.port>=0){
					//should be a connector:
					var previousObjectId = ActivityDiagramClass.getIdFromUri(response.previousConnectorUri);
					var originEltId = ActivityDiagramClass.getActivityId('connector', previousObjectId);
					var arrowId = ActivityDiagramClass.getActivityId('connector', previousObjectId, 'bottom', response.port);

					var activityId = ActivityDiagramClass.getActivityId('container', activity.id);
					ActivityDiagramClass.positionNewActivity($('#'+originEltId), $('#'+activityId));
					// ActivityDiagramClass.setActivityMenuHandler(activityId);

					//create and draw arrow:
					var activityTopId = ActivityDiagramClass.getActivityId('activity', activity.id, 'top');
					ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId), $('#'+activityTopId), 'top', new Array(), false);
					ArrowClass.drawArrow(arrowId, {
						container: ActivityDiagramClass.canvas,
						arrowWidth: 2
					});

					//save diagram:
					ActivityDiagramClass.saveDiagram();
				}
			}catch(ex){
			//	console.log('activityAdded exception:', ex);
			}
		});

		eventMgr.bind('connectorAdded', function(event, response){
			try{
				//a connector is always added throught the "linked mode"
				var previousObjectId = ActivityDiagramClass.getIdFromUri(response.previousActivityUri);
				if(response.previousIsActivity){
					var originEltId = ActivityDiagramClass.getActivityId('activity', previousObjectId);
					var arrowId = ActivityDiagramClass.getActivityId('activity', previousObjectId, 'bottom');

					var activityRefId = previousObjectId;
				}else{
					//should be a connector:
					var originEltId = ActivityDiagramClass.getActivityId('connector', previousObjectId);
					var arrowId = ActivityDiagramClass.getActivityId('connector', previousObjectId, 'bottom', response.port);
					if(ActivityDiagramClass.connectors[previousObjectId]){
						var activityRefId = ActivityDiagramClass.connectors[previousObjectId].activityRef;

						//update the local datastore on the previous activity:
						ActivityDiagramClass.connectors[previousObjectId].port[response.port].targetId = ActivityDiagramClass.getIdFromUri(response.uri);
						//update multiplicity here?
					}else{
						throw 'the connector does not exist in the connectors array';
					}

				}

				var connector = ActivityDiagramClass.feedConnector(
					{
						"data": response.label,
						"attributes": {"id": response.uri},
						"type": response.type
					},
					null,
					previousObjectId,
					null,
					activityRefId
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
				//console.log('connectorAdded exception:', ex);
				//console.log('connector', connector);
				//console.log('originEltId', originEltId);
				//console.log('connectorId', connectorId);
				//console.log('arrowId', arrowId);
			}

		});

		eventMgr.bind('connectorSaved', function(event, response){
			//console.log('connectorSaved triggered');

			var added = false
			if(response.newActivities && response.previousConnectorUri){
				if(response.newActivities.length > 0){
					var activityAddedResponse = response.newActivities[0];//currently, the first one is enough
					activityAddedResponse.previousConnectorUri = response.previousConnectorUri;
					eventMgr.trigger('activityAdded', activityAddedResponse);
					added = true;
				}
			}

			if(response.newConnectors && response.previousConnectorUri){
				if(response.newConnectors.length > 0){
					var connectorAddedResponse = response.newConnectors[0];//currently, the first one is enough
					connectorAddedResponse.previousActivityUri = response.previousConnectorUri;
					connectorAddedResponse.previousIsActivity = false;//the previous activity is obviously a connector here
					eventMgr.trigger('connectorAdded', connectorAddedResponse);
					added = true;
				}
			}

			if(!added){
				//reload the tree:
				ActivityDiagramClass.refreshRelatedTree();
				ActivityDiagramClass.loadDiagram();
			}

		});


		eventMgr.bind('activityPropertiesSaved', function(event, response){
			//simply reload the tree:
			ActivityDiagramClass.refreshRelatedTree();
		});

		eventMgr.bind('activityDeleted', function(event, response){
			ActivityDiagramClass.reloadDiagram();
		});

		eventMgr.bind('connectorDeleted', function(event, response){
			ActivityDiagramClass.reloadDiagram();
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
				//console.log('addactivity on click:', err);
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
