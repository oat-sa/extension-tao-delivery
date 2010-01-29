<?include('header.tpl')?>


<?if(get_data('error')):?>

	<div class="main-container">
		<div class="ui-state-error ui-corner-all" style="padding:5px;">
			<?=__('Please select a delivery aythoring it!')?>
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
	#draggable {padding: 0.5em;width:auto; }
	#draggable1 {padding: 0.5em;width:auto;}
	#accordion1 {position:absolute;left:1%;top:1%;width:25%;height=100%;}
	#demo {position:absolute;left:27%;top:1%;width:50%;height=auto;}
	#process {position:absolute;left:78%;top:1%;width:21%;height=auto;}
	#main {width:1000px;height:700px;}
	
	</style>

	<script type="text/javascript">
	$(function(){
		
	});
	</script>

	<div class="main-container" style="display:none;"></div>
	<div id="authoring-container" class="ui-helper-reset">
	
	<div id="accordion1" style="font-size:0.8em;">
		<h3><a href="#">Service Definition</a></h3>
		<div>
			<div id="serviceDefinition_tree"/>
			<div id="serviceDefinition_form"/>
		</div>
		<h3><a href="#">Formal Parameter</a></h3>
		<div>
			<div id="formalParameter_tree"/>
			<div id="formalParameter_form"/>
		</div>
		<h3><a href="#">Role</a></h3>
		<div>
			<div id="role_tree"/>
			<div id="role_form"/>
		</div>
	</div><!--end accordion -->

		
	</div><!--end authoring-container -->
	
	<script type="text/javascript">
	$(function(){
		$("#accordion1").accordion({
			fillSpace: false,
			autoHeight: false,
			collapsible: true,
			active: 0,
			icons: { 'header': 'ui-icon-plus', 'headerSelected': 'ui-icon-minus' }
		});
		
		//load the trees:
		loadSectionTree("serviceDefinition");//use get_value instead to get the uriResource of the service definition class and make
		loadSectionTree("formalParameter");
		loadSectionTree("role");
	});
	
	function loadSectionTree(section){
	//section in [serviceDefinition, formalParameter, role]
		$.ajax({
			url: '/taoDelivery/Delivery/getSectionTrees',
			type: "POST",
			data: {section: section},
			dataType: 'html',
			success: function(response){
				$('#'+section+'_tree').html(response);
			}
		});
	}
	</script>
	
<?endif?>

<?include('footer.tpl')?>