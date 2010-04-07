//TODO: replace attribute 'name' by 'class'

var arrows = new Array();
var tempArrows = new Array();
var margin = 20;

// alert("OK!");

function calculateArrow(point1, point2, type, flex){
	if(!isset(flex)){
		flex = new Array();
	}
	
	if(!isset(type)){
		//TODO: allow default value for type??
		if(isset(arrows[point1.attr('id')])){
			type = arrows[point1.attr('id')].type;
		}else{
			return false;
		}
	}
	
	//type in array('left','top','right');
	// alert("dsfdf"+type);
	// type ='top';
	var p1 = getCenterCoordinate(point1);
	var p2 = getCenterCoordinate(point2);
	var Dx = p2.x - p1.x;
	var Dy = p1.y - p2.y;//important
	var flexPointNumber = -1;
	
	
	
	if(Dy>0 && type=='top'){
		flexPointNumber = 3;
	}else if(Dy<0 && type=='top'){
		flexPointNumber = 1;
	}else if( Dy<0 && ((Dx>0 && type=='left') || (Dx<0 && type=='right')) ){
		flexPointNumber = 0;
	}else{
		flexPointNumber = 2;
	}
	// alert("flexPointNb: "+flexPointNumber+ ", Dx: "+ Dx+ ", Dy: "+ Dy);
	
	var arrow = new Array();
	var flexPoints = new Array();
	arrow[0] = {x:p1.x, y:p1.y};
	
	switch(flexPointNumber){
		case 0:{
			arrow[1] = {x:p1.x, y:p2.y}; 
			arrow[2] = {x:p2.x, y:p2.y}; 
			break;
			}
		case 1:{
			//check the value flex1 for the arrow exists:
			if(isset(flex[1])){
				flex1 = flex[1];
			}else{
				//if not calculate flex1: (delta2)
				flex1 = (p2.y-p1.y)/2;
			}
			flexPoints[1] = flex1;
			arrow[1] = {x:p1.x, y:p1.y + flex1}; 
			arrow[2] = {x:p2.x, y:p1.y + flex1};
			arrow[3] = {x:p2.x, y:p2.y}; 		
			break;
		}
		case 2:{
			if(isset(flex[1])){
				flex1 = flex[1];
			}else{
				//calculate default value
				if(Dy>0){
					flex1 = margin;
				}else{
					flex1 = (p2.y-p1.y)/2 - point2.height()/2;
				}
			}
			flexPoints[1] = flex1;
			
			if(isset(flex[2])){
				flex2 = flex[2];
			}else{
				if(Dx>0){
					if(type=='right'){
						flex2 = (p2.x + margin) - p1.x;
					}else{
						flex2 = (p2.x - p1.x)/2 - point1.width()/2;//warning: division by 0!
					}
				}else{
					if(type=='right'){
						flex2 = (p2.x - p1.x)/2 - point1.width()/2;
					}else{
						flex2 = (p2.x - margin) - p1.x;
					}
				}
			}
			flexPoints[2] = flex2;	
			arrow[1] = {x:p1.x, y:p1.y + flex1}; 
			arrow[2] = {x:p1.x+flex2, y:p1.y + flex1};
			arrow[3] = {x:p1.x+flex2, y:p2.y};
			arrow[4] = {x:p2.x, y:p2.y}; 			
			break;
		}
		case 3:{
			if(isset(flex[1])){
				flex1 = flex[1];
			}else{
				flex1 = margin;
			}
			if(isset(flex[2])){
				flex2 = flex[2];
			}else{
				flex2 = (p2.x-p1.x)/2;
			}
			if(isset(flex[3])){
				flex3 = flex[3];
			}else{
				flex3 = (-1) * margin;
			}
			flexPoints[1] = flex1;
			flexPoints[2] = flex2;
			flexPoints[3] = flex3;
			
			arrow[1] = {x:p1.x, y:p1.y + flex1}; 
			arrow[2] = {x:p1.x+flex2, y:p1.y + flex1};
			arrow[3] = {x:p1.x+flex2, y:p2.y+flex3};
			arrow[4] = {x:p2.x, y:p2.y+flex3};
			arrow[5] = {x:p2.x, y:p2.y};			
			break;
		}
	}
	
	if(!isset(arrows[point1.attr('id')])){
		arrows[point1.attr('id')] = new Array();
	}
	
	arrows[point1.attr('id')] = {
		'end': point2.attr('id'),
		'coord': arrow,
		'type': type,
		'flex': flexPoints
	}
	
	return true;
	// console.log('test',point1.attr('id'));
	//console.log('x1',p1.x);
	//console.log('y1',p1.y);
	//console.log('x2',p2.x);
	//console.log('y2',p2.y);
	//console.log('Dx',Dx);
	//console.log('Dy',Dy);
	//console.log('flexPt', flexPointNumber);
	//console.log('flex1', flex1);
	//console.log('flex2', flex2);
	// console.log('flex3', flex3);
	//console.dir(arrows);
}

function createArrow(origineId, position){

	//initialize the arrow:
	if(!isset(position.left)){
		left = 0;
	}else{
		left = position.left;
	}
	if(!isset(position.top)){
		top = 0;
	}else{
		top = position.top;
	}
	
	//add the arrow tip element
	var tipId = origineId + '_tip';
	var elementTip = $('<div id="'+tipId+'"></div>');//put connector id here instead
	elementTip.addClass('diagram_arrow_tip');
	elementTip.css('position', 'absolute');
	elementTip.css('left', Math.round(left)+'px');
	elementTip.css('top', Math.round(top)+'px');
	elementTip.appendTo(canvas);
	
	//calculate the initial position & drow it
	calculateArrow($('#'+origineId), elementTip, 'top', null);//default value of the 'type' set to 'top' ...
	drawArrow(origineId, {
		container: canvas,
		arrowWidth: 1
	});
	
	//transform to draggable
	$('#'+elementTip.attr('id')).draggable({
		snap: '.diagram_activity_droppable',
		snapMode: 'inner',
		drag: function(event, ui){
			
			// var position = $(this).position();
			// $("#message").html("<p> left: "+position.left+", top: "+position.top+"</p>");
			var id = $(this).attr('id');
			var arrowName = id.substring(0,id.indexOf('_tip'));
			
			var arrow = arrows[arrowName];
			
			//TODO edit 'type' at the same time:
			
			removeArrow(arrowName);
			calculateArrow($('#'+arrowName), $(this), arrow.type, null);
			drawArrow(arrowName, {
				container: canvas,
				arrowWidth: 1
			});
		},
		containment: canvas,
		stop: function(event, ui){
			var id = $(this).attr('id');
			var arrowName = id.substring(0,id.indexOf('_tip'));
			getDraggableFlexPoints(arrowName);
		}
	});
	
}


function drawArrow(arrowName, options){
	
	if(!isset(arrows[arrowName].coord)){
		throw new Exception('the arrow does not exist');
	}
	if(!isset(options)){
		throw new Exception('no options set');
	}
	
	
	if(options.temp){
		var p = tempArrows[arrowName].coord;
	}else{
		var p = arrows[arrowName].coord;
	}
		
	options.name = arrowName;
	if(isset(p[0])&&isset(p[1])){
		options.index = 1;
		drawVerticalLine(p[0], p[1], options);
		if(isset(p[2])){
			options.index = 2;
			drawHorizontalLine(p[1], p[2], options);
			if(isset(p[3])){
				options.index = 3;
				drawVerticalLine(p[2], p[3], options);
				if(isset(p[4])){
					options.index = 4;
					drawHorizontalLine(p[3], p[4], options);
					if(isset(p[5])){
						options.index = 5;
						drawVerticalLine(p[4], p[5], options);
					}
				}
			}
		}
		
		//draw the extremity: the tip (a picture?)
	}
	
}

function drawVerticalLine(p1, p2, options){
	var arrowWidth = 0;
	if(options.arrowWidth){
		arrowWidth = options.arrowWidth; 
	}else{
		arrowWidth = 2;
	}
	
	width = arrowWidth;
	height = Math.abs(p1.y - p2.y);
	left =  p1.x - arrowWidth/2;//p[0].x  == p[0].y 
	top = Math.min(p1.y,p2.y);
	
	drawArrowPart(1,left,top,width,height,options.container,options.name,options.index);
}

function drawHorizontalLine(p1, p2, options){
	var arrowWidth = 0;
	if(options.arrowWidth){
		arrowWidth = options.arrowWidth; 
	}else{
		arrowWidth = 2;
	}
	
	width = Math.abs(p2.x-p1.x);
	height = arrowWidth;
	left = Math.min(p1.x, p2.x);
	top = p1.y - arrowWidth/2;
	
	drawArrowPart(1,left,top,width,height,options.container,options.name,options.index);
}

function drawArrowPart(border,left,top,width,height,container,name,arrowPartIndex){
	
	if(container && name){
	//"#"+arrowName+"_arrowPart_"+arrowPartIndex
		var borderStr = Math.round(border)+'px '+'solid'+' '+'red';
		var element = $('<div id="'+name+'_arrowPart_'+arrowPartIndex+'"></div>');
		element.addClass(name);
		element.css('border', borderStr);
		element.css('position', 'absolute');
		element.css('left', Math.round(left)+'px');
		element.css('top', Math.round(top)+'px');
		element.css('width', Math.round(width)+'px');
		element.css('height', Math.round(height)+'px');
		
		//console.log('left:',element.css('left'));
		//console.log('top:',element.css('top'));
		//console.log('w:',element.css('width'));
		//console.log('h:',element.css('height'));
		// console.log('x2',p2.x);
		// console.log('y2',p2.y);
	
		element.appendTo(container);
	}
}

function removeArrow(name, complete){
	if(!isset(complete)){
		complete = true;
	}
	
	if(complete){
		arrows[name] = null;
	}
	
	$("."+name).remove();

}

function getDraggableFlexPoints(arrowName){
	//get the postion of flex points, and transform them into draggable object:
	var arrow = arrows[arrowName];
	
	for(i=1;i<=arrow.flex.length;i++){
		
		if(isset(arrow.flex[i])){
			if(i%2){
				//vertical only:
				authorizedAxis = 'y';
			}else{
				//horizontal only:
				authorizedAxis = 'x';
			}
			
			var arrowPartIndex = i + 1 ;
			var arrowPartId = arrowName + "_arrowPart_"+arrowPartIndex;
			var dragHandleId = arrowPartId + '_handle';
			
			//create the handle in the middle:
			var handleElement = $('<div id="'+dragHandleId+'"/>');
			handleElement.addClass(arrowName);
			var borderStr = '1px '+'solid'+' '+'green';
			handleElement.css('border', borderStr);
			handleElement.css('width', '5px');
			handleElement.css('height', '5px');
			handleElement.appendTo("#"+arrowPartId);
			$('#'+dragHandleId).position({
				of: "#"+arrowPartId,
				my: "center",
				at: "center"
			});
			
			
			//get the element and transform it into a draggable (with constraint):
			$("#"+arrowPartId).draggable({
				axis: authorizedAxis,
				opacity: 0.7,
				helper: 'clone',
				handle: "#"+dragHandleId,
				start: function(event, ui){
					// console.log($(this).draggable('option', 'handle'));
				},
				drag: function(event, ui){
										
				},
				containment: '#process_diagram_container',
				stop: function(event, ui){
					
					var offset = 0;
					if($(this).draggable('option', 'axis') == 'x'){
						offset = ui.position.left - ui.originalPosition.left;
					}else if($(this).draggable('option', 'axis') == 'y'){
						offset = ui.position.top - ui.originalPosition.top;
					}else{
						return false;
					}
					
					//get value of flex points:
					var flexPoints = new Array();
					var id = $(this).attr('id');
					var tempIndex = parseInt(id.substr(id.lastIndexOf("arrowPart_")+10)) - 1;
					
					// arrowNameTemp = $(this).attr('name');
					arrowNameTemp = id.substring(0,id.indexOf('_arrowPart_'));
					// console.log(arrowNameTemp);
					arrowTemp = arrows[arrowNameTemp];
					flexPoints = editArrowFlex(arrowNameTemp, tempIndex, offset);
					
					calculateArrow($("#"+arrowNameTemp), $("#"+arrowTemp.end), arrowTemp.type, flexPoints);
					removeArrow(arrowNameTemp, false);
					drawArrow(arrowNameTemp, {
						container: "#process_diagram_container",
						arrowWidth: 1
					});
					
					getDraggableFlexPoints(arrowNameTemp);
				}

			});
			
			
			
		}else{
			break;
		}
	}
	//clear momemory:
	arrowName = '';
	authorizedAxis = '';
}

function getCenterCoordinate(element){
	
	var position = element.position();
	x = position.left + element.width()/2;
	y = position.top + element.height()/2;
	//console.log('Cx',element.width());
	//console.log('Cy',element.height());
	// alert(x+', '+y);
	return {x:x, y:y};
	
}

function isset(object){
	if(typeof(object)=='undefined' || object===null){
		return false;
	}else{
		return true;
	}
}


function editArrowFlex(arrowName, flexPosition, offset){
	
	var flexPoints = new Array();
	
	if(isset(arrows[arrowName])){
		var arrow = arrows[arrowName];
		//get value of flex points:
		
		for(i=1;i<=arrow.flex.length;i++){
			if(isset(arrow.flex[i])){
				if(i == flexPosition){
					//TODO: define allowed range of value for offset
					if(i == 1){
						//the first flex point cannot be above the point of origin:
						if(arrow.flex[i]+offset <= 0){
							continue;//do not modify it
						}
					}else if(i == 3){
						if(arrow.flex[i]+offset >= 0){
							continue;//do not modify it
						}
					}
					
					flexPoints[i] = arrow.flex[i] + offset;// + or -
				}else{
					flexPoints[i] = arrow.flex[i];
				}
			}else{
				break;
			}
		}
		
	}
	
	//immediately followed by calculateArrow and drawArrow;
	return flexPoints;
}

function editArrowType(arrowName, newType){
	//newType in left, top, right
	arrowTemp = arrows[arrowName];
	
	if(isset(arrowTemp)){
		// calculateArrow($("#"+arrowNameTemp), $("#"+arrowTemp.end), newType);
		arrows[arrowName].type = newType;
	}
	
	console.dir(arrowTemp);
	//do not forget to draw it when done;
}

function editArrowClass(){

}
	