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
						TREE_OBJ.open_branch($("li.node-process-root:first"));
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
					}else if( ($(NODE).hasClass('node-inferenceRule-onBefore')||$(NODE).hasClass('node-inferenceRule-onAfter')) && instance.options.editInferenceRuleAction){
						_load(instance.options.formContainer, 
							instance.options.editInferenceRuleAction,
							{inferenceRuleUri:$(NODE).attr('id')}
						);
					}else if( $(NODE).hasClass('node-consistencyRule') && instance.options.editConsistencyRuleAction){
						_load(instance.options.formContainer, 
							instance.options.editConsistencyRuleAction,
							{consistencyRuleUri:$(NODE).attr('id')}
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
							icon: "/taoDelivery/views/img/view-refresh.png",
							visible : function (NODE, TREE_OBJ) {
								if( $(NODE).hasClass('node-process-root')){
									return 1;
								}
								return -1;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.refreshTree({
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
							},
							separator_after : true
						},
						select: {
							label: "Edit",
							icon: "/taoDelivery/views/img/pencil.png",
							visible : function (NODE, TREE_OBJ) {
								if( $(NODE).hasClass('node-process-root') || $(NODE).hasClass('node-then') || $(NODE).hasClass('node-else')){
									return -1;
								}
								return 1;
							},
							action  : function(NODE, TREE_OBJ){
								TREE_OBJ.select_branch(NODE);
							},
							separator_after : true
						},
						addActivity: {
							label: "Add Activity",
							icon: "/taoDelivery/views/img/process_activity.png",
							visible : function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return -1; 
								}
								if($(NODE).hasClass('node-process-root') && TREE_OBJ.check("creatable", NODE) && instance.options.createActivityAction){ 
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
						isFirst:{
							label	: "Define as the first activity",
							icon	: "/taoDelivery/views/img/flag-green.png",
							visible	: function (NODE, TREE_OBJ) {
								if($(NODE).hasClass('node-activity') && !$(NODE).hasClass('node-activity-initial')){ 
									return 1;
								}
								return -1;
							}, 
							action	: function (NODE, TREE_OBJ) {
								ActivityTreeClass.setFirstActivity({
									url: instance.options.setFirstActivityAction,
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
								return false;
							}
						},
						isLast:{
							label	: "Define as a final activity",
							icon	: "/taoDelivery/views/img/flag-red.png",
							visible	: function (NODE, TREE_OBJ) {
								if($(NODE).hasClass('node-activity') && !$(NODE).hasClass('node-activity-last')){ 
									return 1;
								}
								return -1;
							}, 
							action	: function (NODE, TREE_OBJ) {
								//find the child connector node and delete it
								$.each(TREE_OBJ.children(NODE), function(){ 
									var selectedNode = this;
									if($(selectedNode).hasClass('node-connector') && instance.options.deleteConnectorAction){
										// data =  {activityUri: $(selectedNode).attr('id')};
										// alert($(selectedNode).attr('id'));
										ActivityTreeClass.removeNode({
											url: instance.options.deleteConnectorAction,
											NODE: $(selectedNode),
											TREE_OBJ: TREE_OBJ
										});
									}
								});
								return false;
							}
						},
						unsetLast:{
							label	: "Unset the final activity",
							icon	: "/taoDelivery/views/img/unset-flag-red.png",
							visible	: function (NODE, TREE_OBJ) {
								if($(NODE).hasClass('node-activity-last')){ 
									return 1;
								}
								return -1;
							}, 
							action	: function (NODE, TREE_OBJ) {
								ActivityTreeClass.unsetLastActivity({
									url: instance.options.unsetLastActivityAction,
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
								return false;
							}
						},
						addInteractiveService: {
							label: "Add Interactive Service",
							icon: "/taoDelivery/views/img/process_service.png",
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
							},
							separator_before : true
						},
						addOnBeforeInferenceRule: {
							label: "Add 'OnBefore' InferenceRule",
							icon: "/taoDelivery/views/img/inference-rule.png",
							visible : function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return -1; 
								}
								if($(NODE).hasClass('node-activity') && TREE_OBJ.check("creatable", NODE) && instance.options.createInferenceRuleAction){ 
									return 1;
								}
								return -1;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.addInferenceRule({
									url: instance.options.createInferenceRuleAction,
									type: 'onBefore',
									id: $(NODE).attr('id'),
									NODE: NODE,
									TREE_OBJ: TREE_OBJ,
									cssClass: instance.options.instanceClass
								});
							}
						},
						addOnAfterInferenceRule: {
							label: "Add 'OnAfter' InferenceRule",
							icon: "/taoDelivery/views/img/inference-rule.png",
							visible : function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return -1; 
								}
								if($(NODE).hasClass('node-activity') && TREE_OBJ.check("creatable", NODE) && instance.options.createInferenceRuleAction){ 
									return 1;
								}
								return -1;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.addInferenceRule({
									url: instance.options.createInferenceRuleAction,
									type: 'onAfter',
									id: $(NODE).attr('id'),
									NODE: NODE,
									TREE_OBJ: TREE_OBJ,
									cssClass: instance.options.instanceClass
								});
							}
						},
						addConsistencyRule: {
							label: "Add Consistency Rule",
							icon: "/taoDelivery/views/img/process_consistency_rule.png",
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
								ActivityTreeClass.addConsistencyRule({
									url: instance.options.createConsistencyRuleAction,
									id: $(NODE).attr('id'),
									NODE: NODE,
									TREE_OBJ: TREE_OBJ,
									cssClass: instance.options.instanceClass
								});
							}
						},
						deleteActivity:{
							label	: "Remove activity",
							icon	: "/taoDelivery/views/img/delete.png",
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
							},
							separator_before: true
						},
						deleteConnector:{
							label	: "Remove connector",
							icon	: "/taoDelivery/views/img/delete.png",
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
							},
							separator_before: true 
						},
						deleteService:{
							label	: "Remove interactive service",
							icon	: "/taoDelivery/views/img/delete.png",
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
							},
							separator_before: true 
						},
						deleteInferenceRule:{
							label	: "Remove inferenceRule",
							icon	: "/taoDelivery/views/img/delete.png",
							visible	: function (NODE, TREE_OBJ){
								var ok = -1;
								$.each(NODE, function (){
									if( ($(NODE).hasClass('node-inferenceRule-onBefore') || $(NODE).hasClass('node-inferenceRule-onAfter')) 
									&& instance.options.deleteInferenceRuleAction 
									&& (TREE_OBJ.check("deletable", this) == true)){
										ok = 1;
										return 1;
									}
								});
								return ok;
							}, 
							action	: function (NODE, TREE_OBJ){
								ActivityTreeClass.removeNode({
									url: instance.options.deleteInferenceRuleAction,
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
								return false;
							},
							separator_before: true 
						},
						deleteConsistencyRule:{
							label	: "Remove consistencyRule",
							icon	: "/taoDelivery/views/img/delete.png",
							visible	: function (NODE, TREE_OBJ){
								var ok = -1;
								$.each(NODE, function (){
									if( ($(NODE).hasClass('node-consistencyRule') ) 
									&& instance.options.deleteConsistencyRuleAction 
									&& (TREE_OBJ.check("deletable", this) == true)){
										ok = 1;
										return 1;
									}
								});
								return ok;
							}, 
							action	: function (NODE, TREE_OBJ){
								ActivityTreeClass.removeNode({
									url: instance.options.deleteConsistencyRuleAction,
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
								return false;
							},
							separator_before: true 
						},
						gotonode:{
							label	: "Goto",
							icon	: "/taoDelivery/views/img/go-jump.png",
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
						'class': cssClass+' '+response.class
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
//node-inferenceRule-then
//node-inferenceRule-else
//node-inferenceRule-onBefore
//node-inferenceRule-onAfter
ActivityTreeClass.addInferenceRule = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	var  cssClass = 'node-inferenceRule';
	if(options.type == 'onBefore'){
		cssClass += '-onBefore';
	}else if(options.type == 'onAfter'){
		//on after:
		cssClass += '-onAfter';
	}else{
		return false;
	}
	if(options.cssClass){
		 cssClass += ' ' + options.cssClass;
	}
	
	$.ajax({
		url: options.url,
		type: "POST",
		data: {activityUri: options.id, type: options.type},
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
 * add a consistency rule
 * @param {Object} options
 */
ActivityTreeClass.addConsistencyRule = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	var  cssClass = 'node-consistencyRule';
	if(options.cssClass){
		 cssClass += ' ' + options.cssClass;
	}
	
	$.ajax({
		url: options.url,
		type: "POST",
		data: {activityUri: options.id},
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
	if(confirm(__("Please confirm deletion.\n Warning: related resources might be affected."))){
		
			data = false;
			// var selectedNode = this;
			if(NODE.hasClass('node-connector')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {connectorUri: NODE.attr('id')};
			}
			else if(NODE.hasClass('node-activity')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {activityUri: NODE.attr('id')};
			}
			else if(NODE.hasClass('node-interactive-service')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {serviceUri: NODE.attr('id')};
			}
			else if(NODE.hasClass('node-inferenceRule-onBefore') || NODE.hasClass('node-inferenceRule-onAfter')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {inferenceUri: NODE.attr('id')};
			}
			else if(NODE.hasClass('node-consistencyRule')){
				data =  {consistencyUri: NODE.attr('id')};
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
		
	}
}

ActivityTreeClass.setFirstActivity = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	data = {processUri:TREE_OBJ.parent(NODE).attr('id'), activityUri:NODE.attr('id')};
	
	if(data){
		$.ajax({
			url: options.url,
			type: "POST",
			data: data,
			dataType: 'json',
			success: function(response){
				if(response.set){
					TREE_OBJ.refresh();
				}
			}
		});
	}
	
}

ActivityTreeClass.unsetLastActivity = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	data = {activityUri:NODE.attr('id')};
	
	if(data){
		$.ajax({
			url: options.url,
			type: "POST",
			data: data,
			dataType: 'json',
			success: function(response){
				if(response.created){
					TREE_OBJ.refresh();
				}
			}
		});
	}
	
}
