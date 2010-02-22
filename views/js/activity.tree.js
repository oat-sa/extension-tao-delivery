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
		this.dataUrl = dataUrl;
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
						processUri : instance.options.processUri
						// filter: $("#filter-content-" + options.actionId).val()
					}
				},
				onload: function(TREE_OBJ){
					if (instance.options.selectNode && !instance.nodeSelected) {
						TREE_OBJ.select_branch($("li[id='"+instance.options.selectNode+"']"));
						instance.nodeSelected = true;	//select it only on first load
					}
					else{
						TREE_OBJ.open_branch($("li.node-root:first"));
						// TREE_OBJ.reselect(true);
					}
				},
				ondata: function(DATA, TREE_OBJ){
					return DATA;
				},
				onselect: function(NODE, TREE_OBJ){
					
					if( ($(NODE).hasClass('node-activity') || $(NODE).hasClass('node-property')) && instance.options.editActivityPropertyAction){
						var index = $(NODE).attr('id').indexOf("prop_");
						var activityUri = '';
						if(index == 0){
							//it is a property node
							activityUri = $(NODE).attr('id').substr(5);
						}else{
							activityUri = $(NODE).attr('id');
						}
						_load(instance.options.formContainer, 
							instance.options.editActivityPropertyAction, 
							{ activityUri: activityUri}//put encoded uri as the id of the activity node
						);
					}else if( $(NODE).hasClass('node-activity-goto') && instance.options.editActivityPropertyAction){
						//hightlight the target node
						var activityUri = $(NODE).attr('rel');
						_load(instance.options.formContainer, 
							instance.options.editActivityPropertyAction, 
							{ activityUri: activityUri}
						);
						
					}else if( $(NODE).hasClass('node-connector') && instance.options.editConnectorAction){
						_load(instance.options.formContainer, 
							instance.options.editConnectorAction,
							{connectorUri:$(NODE).attr('id')}
						);
					}else if( ($(NODE).hasClass('node-connector-goto')||$(NODE).hasClass('node-connector-prev')) && instance.options.editConnectorAction){
						//hightlight the target node
						// TREE_OBJ.select_branch(NODE);
						var connectorUri = $(NODE).attr('rel');
						_load(instance.options.formContainer, 
							instance.options.editConnectorAction,
							{connectorUri: connectorUri}
						);
					}else if( $(NODE).hasClass('node-interactive-service') && instance.options.editInteractiveServiceAction){
						_load(instance.options.formContainer, 
							instance.options.editInteractiveServiceAction,
							{uri:$(NODE).attr('id')}
						);
					}
					return false;
					
				}
			},
			plugins: {
				contextmenu : {
					items : {
						refreshTree: {
							label: "Refresh",
							icon: "/tao/views/img/pencil.png",
							visible : function (NODE, TREE_OBJ) {
								if( $(NODE).hasClass('node-root')){
									return 1;
								}
								return -1;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.refreshTree({
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
							}
						},
						select: {
							label: "Edit",
							icon: "/tao/views/img/pencil.png",
							visible : function (NODE, TREE_OBJ) {
								if( $(NODE).hasClass('node-root') || $(NODE).hasClass('node-then') || $(NODE).hasClass('node-else')){
									return -1;
								}
								return 1;
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
									return -1; 
								}
								if($(NODE).hasClass('node-root') && TREE_OBJ.check("creatable", NODE) && instance.options.createActivityAction){ 
									return 1;
								}
								return -1;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.addActivity({
									url: instance.options.createActivityAction,
									id: $(NODE).attr('id'),
									NODE: NODE,
									TREE_OBJ: TREE_OBJ,
									cssClass: instance.options.instanceClass
								});
							}
						},
						addInteractiveService: {
							label: "Add Interactive Service",
							icon: "/tao/views/img/instance_add.png",
							visible : function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return -1; 
								}
								if($(NODE).hasClass('node-activity') && TREE_OBJ.check("creatable", NODE) ){ 
									return 1;
								}
								return -1;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.addInteractiveService({
									url: instance.options.createInteractiveServiceAction,
									id: $(NODE).attr('id'),
									NODE: NODE,
									TREE_OBJ: TREE_OBJ,
									cssClass: instance.options.instanceClass
								});
							}
						},
						// addStatementAssignation: {
							// label: "Add statement assignation",
							// icon: "/tao/views/img/instance_add.png",
							// visible : function (NODE, TREE_OBJ){
								// if(NODE.length != 1) {
									// return false; 
								// }
								// if($(NODE).hasClass('node-activity') && TREE_OBJ.check("creatable", NODE) ){ 
									// return true;
								// }
								// return false;
							// },
							// action  : function(NODE, TREE_OBJ){
								// ActivityTreeClass.addActivity({
									// url: instance.options.createInteractiveServiceAction,
									// id: $(NODE).attr('id'),
									// NODE: NODE,
									// TREE_OBJ: TREE_OBJ,
									// cssClass: instance.options.instanceClass
								// });
							// }
						// },
						// addConsistencyRule: {
							// label: "Add Consistency Rule",
							// icon: "/tao/views/img/instance_add.png",
							// visible : function (NODE, TREE_OBJ) {
								// if(NODE.length != 1) {
									// return -1; 
								// }
								// if($(NODE).hasClass('node-activity') && TREE_OBJ.check("creatable", NODE) ){ 
									// return 1;
								// }
								// return -1;
							// },
							// action  : function(NODE, TREE_OBJ){
								// ActivityTreeClass.addActivity({
									// url: instance.options.createConsistencyRuleAction,
									// id: $(NODE).attr('id'),
									// NODE: NODE,
									// TREE_OBJ: TREE_OBJ,
									// cssClass: instance.options.instanceClass
								// });
							// }
						// },
						del:{
							label	: "Remove",
							icon	: "/tao/views/img/delete.png",
							visible	: function (NODE, TREE_OBJ){
								var ok = -1;
								$.each(NODE, function (){
									if( ($(NODE).hasClass('node-activity')|| $(NODE).hasClass('node-interactive-service') || $(NODE).hasClass('node-consistency-rule')) 
									&& instance.options.deleteAction 
									&& (TREE_OBJ.check("deletable", this) == true)){
										ok = 1;
										return 1;
									}
								});
								return ok;
							}, 
							action	: function (NODE, TREE_OBJ){ 
								ActivityTreeClass.removeNode({
									url: instance.options.deleteAction,
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
								return false;
							} 
						},
						deleteActivity:{
							label	: "Remove activity",
							icon	: "/tao/views/img/delete.png",
							visible	: function (NODE, TREE_OBJ){
								var ok = -1;
								$.each(NODE, function (){
									if( $(NODE).hasClass('node-activity')
									&& instance.options.deleteActivityAction 
									&& (TREE_OBJ.check("deletable", this) == true)){
										ok = 1;
										return 1;
									}
								});
								return ok;
							}, 
							action	: function (NODE, TREE_OBJ){
								ActivityTreeClass.removeNode({
									url: instance.options.deleteActivityAction,
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
								return false;
							} 
						},
						deleteConnector:{
							label	: "Remove connector",
							icon	: "/tao/views/img/delete.png",
							visible	: function (NODE, TREE_OBJ){
								var ok = -1;
								$.each(NODE, function (){
									if( $(NODE).hasClass('node-connector')
									&& TREE_OBJ.parent(NODE).hasClass('node-connector')
									&& instance.options.deleteConnectorAction 
									&& (TREE_OBJ.check("deletable", this) == true)){
										ok = 1;
										return 1;
									}
								});
								return ok;
							}, 
							action	: function (NODE, TREE_OBJ){
								ActivityTreeClass.removeNode({
									url: instance.options.deleteConnectorAction,
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
								return false;
							} 
						},
						deleteService:{
							label	: "Remove interactive service",
							icon	: "/tao/views/img/delete.png",
							visible	: function (NODE, TREE_OBJ){
								var ok = -1;
								$.each(NODE, function (){
									if( $(NODE).hasClass('node-interactive-service')
									&& instance.options.deleteInteractiveServiceAction 
									&& (TREE_OBJ.check("deletable", this) == true)){
										ok = 1;
										return 1;
									}
								});
								return ok;
							}, 
							action	: function (NODE, TREE_OBJ){
								ActivityTreeClass.removeNode({
									url: instance.options.deleteInteractiveServiceAction,
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
								if($(NODE).hasClass('node-activity-goto') || $(NODE).hasClass('node-connector-goto')){ 
									return 1;
								}
								return -1;
							}, 
							action	: function (NODE, TREE_OBJ) {
								//hightlight the target node
								targetId = $(NODE).attr('rel');
								TREE_OBJ.select_branch($("li[id='"+targetId+"']"));
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
		
		// $("#open-action-" + options.actionId).click(function(){
			// $.tree.reference(instance.selector).open_all();
		// });
		// $("#close-action-" + options.actionId).click(function(){
			// $.tree.reference(instance.selector).close_all();
		// });
		
		// $("#filter-action-" + options.actionId).click(function(){
			// $.tree.reference(instance.selector).refresh();
		// });
		// $("#filter-content-" + options.actionId).bind('keypress', function(e) {
	        // if(e.keyCode==13 && this.value.length > 0){
				// $.tree.reference(instance.selector).refresh();
	        // }
		// });

	}
	catch(exp){
		// console.log(exp);
	}
}


/**
 * add an activity
 * @param {Object} options
 */
ActivityTreeClass.addActivity = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	var  cssClass = 'node-activity';
	if(options.cssClass){
		 cssClass += ' ' + options.cssClass;
	}
	
	$.ajax({
		url: options.url,
		type: "POST",
		data: {processUri: options.id, type: 'activity'},
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
				
				//create property node:
				TREE_OBJ.create({
					data: 'property',
					attributes: {
						id: 'prop_'+response.uri,
						'class': 'node-property'
					}
				});
				
				
				//TODO: to be debugged
				if(response.connector){
					//create next connector node:
					TREE_OBJ.create({
						data: response.connector.data,
						attributes: {
							id: response.connector.attributes.id,
							'class': response.connector.attributes.class
						}
					});
				}
			}
		}
	});
}

/**
 * refresh the tree
 * @param {Object} options
 */
ActivityTreeClass.refreshTree = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	TREE_OBJ.refresh();
	// TREE_OBJ.reselect(true);
}
		
/**
 * add an activity
 * @param {Object} options
 */
ActivityTreeClass.addInteractiveService = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	var  cssClass = 'node-interactive-service';
	if(options.cssClass){
		 cssClass += ' ' + options.cssClass;
	}
	
	$.ajax({
		url: options.url,
		type: "POST",
		data: {activityUri: options.id, type: 'interactive-service'},
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
 * @return {Object} the tree instance
 */
ActivityTreeClass.prototype.getTree = function(){
	return $.tree.reference(this.selector);
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
		if(anActivityTree){
			aJsTree = anActivityTree.getTree();
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

/**
 * remove a resource
 * @param {Object} options
 */
ActivityTreeClass.removeNode0 = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	if(confirm(__("Please confirm deletion"))){
		$.each(NODE, function () { 
			data = false;
			var selectedNode = this;
			if($(selectedNode).hasClass('node-activity')){
				data =  {activityUri: $(selectedNode).attr('id')}
			}
			if($(selectedNode).hasClass('node-interactive-service') || $(selectedNode).hasClass('node-consistency-rule')){
				PNODE = TREE_OBJ.parent(selectedNode);
				data =  {uri: $(selectedNode).attr('id'), activityUri: $(PNODE).attr('id')}
			}
			if(data){
				$.ajax({
					url: options.url,
					type: "POST",
					data: data,
					dataType: 'json',
					success: function(response){
						if(response.deleted){
							TREE_OBJ.remove(selectedNode); 
						}
					}
				});
			}
		}); 
	}
}

/**
 * remove an activity or a connector node:
 * @param {Object} options
 */
ActivityTreeClass.removeNode = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	if(confirm(__("Please confirm deletion? warning: the related connectors may be affected"))){
		// $.each(NODE, function (){
			data = false;
			// var selectedNode = this;
			if(NODE.hasClass('node-connector')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {connectorUri: NODE.attr('id')};
			}
			if(NODE.hasClass('node-activity')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {activityUri: NODE.attr('id')};
			}
			if(NODE.hasClass('node-interactive-service')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {serviceUri: NODE.attr('id')};
			}
			if(data){
				$.ajax({
					url: options.url,
					type: "POST",
					data: data,
					dataType: 'json',
					success: function(response){
						if(response.deleted){
							TREE_OBJ.refresh();
						}
					}
				});
			}
		// }); 
	}
}

