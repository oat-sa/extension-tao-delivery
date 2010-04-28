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
	<style type="text/css">
	#draggable {width:5px;height:5px;border:1px solid blue;}
	#draggable1 {width:5px;height:5px;border:1px solid blue;}
	
	#accordion1 {position:absolute;left:0%;top:0%;width:15%;height:100%;}
	#accordion_container_2 {position:absolute;left:75%;top:0%;width:25%;height:100%;}
	#process_diagram_container {position:absolute;left:15%;top:27px;width:60%;height:100%;border:1px solid black;}
	#process_diagram_feedback {position:absolute;left:15%;top:0px;width:60%;height:25px;border:1px solid black;}
	
	#demo {position:absolute;left:27%;top:1%;width:50%;height=auto;}
	#process {position:absolute;left:78%;top:1%;width:21%;height=auto;}
	#main {width:1000px;height:700px;}
	
	.diagram_arrow_tip {width:5px;height:5px;border:1px solid green;}
	.diagram_activity {width:120px;height:50px;border:1px solid red;}
	.diagram_link {width:1px;height:30px;border:1px solid black;}
	.diagram_connector {width:30px;height:30px;border:1px solid red;}
	.diagram_activity_droppable {width:5px;height:5px;border:1px solid blue;}
	.diagram_activity_border_point {width:5px;height:5px;border:1px solid blue;}
	</style>

	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/arrows.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/activityDiagram.js"></script>
	<script type="text/javascript">
	var canvas = "#process_diagram_container";
	
	$(function() {
		
		
		ActivityDiagramClass.drawActivity("activity1_uri", {
			left: 50,
			top: 50
		});
		/*
		createDroppablePoints("activity1_uri");
		
		drawActivity("activity2_uri", {
			left: 150,
			top: 50
		});
		createDroppablePoints("activity2_uri");
		
		createArrow('origine2',{
			left: 200,
			top: 30
		});
		
		
		$("#draggable1").draggable({
			snap: '.diagram_activity_droppable',
			snapMode: 'inner',
			drag: function(event, ui){
				
				var position = $(this).position();
				$("#message").html("<p> left: "+position.left+", top: "+position.top+"</p>");
				
				removeArrow("origine");
				calculateArrow($("#origine"), $(this), 'right', null);
				drawArrow("origine", {
					container: "#process_diagram_container",
					arrowWidth: 1
				});
				
			},
			containment: canvas,
			stop: function(event, ui){
				// console.dir(ui);
				getDraggableFlexPoints('origine');
				
				// var coord = getCenterCoordinate($(this));
				// alert(coord.x+', '+coord.y);
				// removeArrow("origine");
				// createArrow($("#origine"), $(this), 'right');
				// drawArrow($("#origine"), {
					// container: "#process_diagram_container",
					// arrowWidth: 1
				// });
			}

		});
		*/
	});

	</script>

	<div class="main-container" style="display:none;"></div>
	<div id="authoring-container" class="ui-helper-reset">
	<div id="process_diagram_container" ></div>
	<div id="process_diagram_feedback" ></div>
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
	var processUri = "<?=get_data("processUri")?>";
	var authoringControllerPath = '/taoDelivery/DeliveryAuthoring/';
	
	$(function(){
		EventMgr.unbind('activityAdded');
		
		EventMgr.bind('activityAdded', function(event, response){
			console.log("added from menu");
		});
		
		$("#activity_menu_addActivity").click(function(event){
			event.preventDefault();
			GatewayProcessAuthoring.addActivity(authoringControllerPath+"addActivity", processUri);
		});
		
		$("#accordion1").accordion({
			fillSpace: false,
			autoHeight: false,
			collapsible: true,
			active: 0,
			icons: { 'header': 'ui-icon-plus', 'headerSelected': 'ui-icon-minus' }
		});
		
		//load activity tree:
		loadActivityTree();
		
		/*
		//load the trees:
		loadSectionTree("serviceDefinition");//use get_value instead to get the uriResource of the service definition class and make
		loadSectionTree("formalParameter");
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