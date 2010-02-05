ActivityTreeClass.instances = [];

/**
 * Constructor
 * @param {String} selector the jquery selector of the tree container
 * @param {String} dataUrl the url to call, it must provide the json data to populate the tree 
 * @param {Object} options
 */
function ActivityTreeClass(selector, dataUrl, options){
	try{
		if(!options){
			options = ActivityTreeClass.defaultOptions;
		}
		this.selector = selector;
		this.options = options;
		this.dataUrl = dataUrl;//could be hard coded: /taoDelivery/DeliveryAuthoring/getActivityTree
		var instance = this;
		
		if(!options.instanceName){
			options.instanceName = 'instance';
		}
		
		ActivityTreeClass.instances[ActivityTreeClass.instances.length + 1] = instance;
		
		this.treeOptions = {
			data: {
				type: "json",
				async : true,
				opts: {
					method : "POST",
					url: instance.dataUrl
				}
			},
			types: {
			 "default" : {
					renameable	: false,
					deletable	: true,
					creatable	: true,
					draggable	: false
				}
			},
			ui: {
				theme_name : "custom"
			},
			callback : {
				beforedata:function(NODE, TREE_OBJ) { 
					return { 
						type : $(TREE_OBJ.container).attr('id'),
						// filter: $("#filter-content-" + options.actionId).val()
					}
				},
				onload: function(TREE_OBJ){
					if (instance.options.selectNode && !instance.nodeSelected) {
						TREE_OBJ.select_branch($("li[id='"+instance.options.selectNode+"']"));
						instance.nodeSelected = true;	//select it only on first load
					}
					else{
						TREE_OBJ.open_branch($("li.node-class:first"));
					}
				},
				ondata: function(DATA, TREE_OBJ){
					// if(instance.options.instanceClass){
						// if(DATA.children){
							// function addClassToNodes(nodes, clazz){
								// $.each(nodes, function(i, node){
									// if(node.attributes['class'] == 'node-instance'){
										// node.attributes['class'] = 'node-instance ' + clazz;
									// }
									// if(node.children){
										// addClassToNodes(node.children, clazz);
									// }
								// });
							// }
							// addClassToNodes(DATA.children, instance.options.instanceClass);
						// }
					// }
					return DATA;
				},
				onselect: function(NODE, TREE_OBJ){
					
					if($(NODE).hasClass('node-class') && instance.options.editClassAction){
						_load(instance.options.formContainer, 
							instance.options.editClassAction,
							{classUri:$(NODE).attr('id')}
						);
					}
					
					if( ($(NODE).hasClass('node-activity')||$(NODE).hasClass('node-property')) && instance.options.editActivityPropertyAction){
						_load(instance.options.formContainer, 
							instance.options.editActivityPropertyAction, 
							{uri: $(NODE).attr('id')}//put encoded uri as the id of the activity node
						);
					}else if( $(NODE).hasClass('node-activity-goto') && instance.options.editActivityPropertyAction){
						//hightlight the target node
						var index = $(NODE).attr('id').lastIndexOf('_goto');
						if(index > 0){
							var activityUri = $(NODE).attr('id').substring(0,index);
							_load(instance.options.formContainer, 
								instance.options.editActivityPropertyAction, 
								{uri: activityUri}
							);
						}
					}else if( $(NODE).hasClass('node-connector') && instance.options.editConnectorAction){
						_load(instance.options.formContainer, 
							instance.options.editConnectorAction,
							{classUri:$(NODE).attr('id')}
						);
					}else if( $(NODE).hasClass('node-connector-goto') && instance.options.editConnectorAction){
						//hightlight the target node
						var index = $(NODE).attr('id').lastIndexOf('_goto');
						if(index > 0){
							// TREE_OBJ.select_branch(NODE);
							var connectorUri = $(NODE).attr('id').substring(0,index);
							_load(instance.options.formContainer, 
								instance.options.editConnectorAction,
								{classUri: connectorUri}
							);
						}
					}else if( $(NODE).hasClass('node-interactive-service') && instance.options.editInteractiveServiceAction){
						_load(instance.options.formContainer, 
							instance.options.editInteractiveServiceAction,
							{classUri:$(NODE).attr('id')}
						);
					}
					return false;
				}
			},
			plugins: {
				contextmenu : {
					items : {
						select: {
							label: "Edit",
							icon: "/tao/views/img/pencil.png",
							visible : function (NODE, TREE_OBJ) {
								if( $(NODE).hasClass('node-main') || $(NODE).hasClass('node-then') || $(NODE).hasClass('node-else')){
									return false;
								}
								return true;
							},
							action  : function(NODE, TREE_OBJ){
								TREE_OBJ.select_branch(NODE);
							}
						},
						addActivity: {
							label: "Add Activity",
							icon: "/tao/views/img/class_add.png",
							visible : function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return false; 
								}
								if($(NODE).hasClass('node-main') && TREE_OBJ.check("creatable", NODE) ){ 
									return true;
								}
								return false;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.addActivity({
									url: instance.options.createActivityAction,
									id: $(NODE).attr('id'),
									NODE: NODE,
									TREE_OBJ: TREE_OBJ,
									cssClass: instance.options.instanceClass
								});
							},
						},
						addInteractiveService: {
							label: "Add Interactive Service",
							icon: "/tao/views/img/instance_add.png",
							visible : function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return false; 
								}
								if($(NODE).hasClass('node-activity') && TREE_OBJ.check("creatable", NODE) ){ 
									return true;
								}
								return false;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.addActivity({
									url: instance.options.createInteractiveServiceAction,
									id: $(NODE).attr('id'),
									NODE: NODE,
									TREE_OBJ: TREE_OBJ,
									cssClass: instance.options.instanceClass
								});
							},
		                    separator_before : true
						},
						addStatementAssignation: {
							label: "Add Interactive Service",
							icon: "/tao/views/img/instance_add.png",
							visible : function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return false; 
								}
								if($(NODE).hasClass('node-activity') && TREE_OBJ.check("creatable", NODE) ){ 
									return true;
								}
								return false;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.addActivity({
									url: instance.options.createInteractiveServiceAction,
									id: $(NODE).attr('id'),
									NODE: NODE,
									TREE_OBJ: TREE_OBJ,
									cssClass: instance.options.instanceClass
								});
							},
		                    separator_before : true
						},
						addConsistencyRule: {
							label: "Add Interactive Service",
							icon: "/tao/views/img/instance_add.png",
							visible : function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return false; 
								}
								if($(NODE).hasClass('node-activity') && TREE_OBJ.check("creatable", NODE) ){ 
									return true;
								}
								return false;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.addActivity({
									url: instance.options.createInteractiveServiceAction,
									id: $(NODE).attr('id'),
									NODE: NODE,
									TREE_OBJ: TREE_OBJ,
									cssClass: instance.options.instanceClass
								});
							},
		                    separator_before : true
						},
						// duplicate:{
							// label	: "Duplicate",
							// icon	: "/tao/views/img/duplicate.png",
							// visible	: function (NODE, TREE_OBJ) { 
									// if($(NODE).hasClass('node-activity')  && instance.options.duplicateAction){
										// return true;
									// }
									// return false;
								// }, 
							// action	: function (NODE, TREE_OBJ) { 
								// ActivityTreeClass.cloneNode({
									// url: instance.options.duplicateAction,
									// NODE: NODE,
									// TREE_OBJ: TREE_OBJ
								// });
							// }
						// },
						del:{
							label	: "Remove",
							icon	: "/tao/views/img/delete.png",
							visible	: function (NODE, TREE_OBJ) { 
								var ok = true; 
								$.each(NODE, function () { 
									if(TREE_OBJ.check("deletable", this) == false || !instance.options.deleteAction) 
										ok = false; 
										return false; 
									}); 
									return ok; 
								}, 
							action	: function (NODE, TREE_OBJ) { 
								ActivityTreeClass.removeNode({
									url: instance.options.deleteAction,
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
								return false;
							} 
						},
						gotonode:{
							label	: "Goto",
							icon	: "/tao/views/img/instance_add.png",
							visible	: function (NODE, TREE_OBJ) {
								var ok = true; 
								$.each(NODE, function () { 
									if($(NODE).hasClass('node-activity-goto') || $(NODE).hasClass('node-connector-goto')){ 
									return true;
									}
									return false;
								}, 
							action	: function (NODE, TREE_OBJ) { 
								//hightlight the target node
								var index = $(NODE).attr('id').lastIndexOf('_goto');
								if(index > 0){
									// TREE_OBJ.select_branch(NODE);
									var targetId = $(NODE).attr('id').substring(0,index);
									TREE_OBJ.select_branch(NODE);
								}
								return false;
							} 
						},
						remove: false,
						create: false,
						rename: false
					}
				}
			}
		};
		
		//create the tree
		$(selector).tree(this.treeOptions);
		
		$("#open-action-" + options.actionId).click(function(){
			$.tree.reference(instance.selector).open_all();
		});
		$("#close-action-" + options.actionId).click(function(){
			$.tree.reference(instance.selector).close_all();
		});
		
		$("#filter-action-" + options.actionId).click(function(){
			$.tree.reference(instance.selector).refresh();
		});
		$("#filter-content-" + options.actionId).bind('keypress', function(e) {
	        if(e.keyCode==13 && this.value.length > 0){
				$.tree.reference(instance.selector).refresh();
	        }
		});

	}
	catch(exp){
		//console.log(exp);
	}
}


/**
 * add an activity
 * @param {Object} options
 */
ActivityTreeClass.addActivity = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	var  cssClass = 'node-instance';
	if(options.cssClass){
		 cssClass += ' ' + options.cssClass;
	}
	
	$.ajax({
		url: options.url,
		type: "POST",
		data: {classUri: options.id, type: 'instance'},
		dataType: 'json',
		success: function(response){
			if (response.uri) {
				TREE_OBJ.select_branch(TREE_OBJ.create({
					data: response.label,
					attributes: {
						id: response.uri,
						'class': cssClass
					}
				}, TREE_OBJ.get_node(NODE[0])));
			}
		}
	});
}

/**
 * select a node in the current tree
 * @param {String} id
 * @return {Boolean}
 */
ActivityTreeClass.selectTreeNode = function(id){
	i=0;
	while(i < ActivityTreeClass.instances.length){
		anActivityTree = ActivityTreeClass.instances[i];
		if(aGenerisTree){
			aJsTree = aActivityTree.getTree();
			if(aJsTree){
				if(aJsTree.select_branch($("li[id='"+id+"']"))){
					return true;
				}
			}
		}
		i++;
	}
	return false;
}