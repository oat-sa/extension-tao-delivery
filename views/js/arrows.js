var arrows = new Array();
var margin = 20;
// alert("sdfsdg");

function calculateArrow(point1, point2, type, flex){
	if(!isset(flex)){
		flex = new Array();
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
		arrows[point1.attr('id')] = {
			'end': point2.attr('id'),
			'coord': arrow,
			'type': type,
			'flex': flexPoints
		}
	}
		
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

function editArrow(arrowName, flexPosition, offset){
	
	var arrow = arrows[arrowName];
	//get value of flex points:
	var flexPoints[] = new Array();
	for(i=1;i<=arrow.flex.length;i++){
		if(isset(arrow.flex[i])){
			if(i == flexPosition){
				//TODO: define allowed range of value for offset
				flexPoints[i] = arrow.flex[i] + offset;// + or -
			}else{
				flexPoints[i] = arrow.flex[i];
			}
		}else{
			break;
		}
	}
	
	return flexPoints;
	/*
	//TODO define allowed range of value for offset
	//flexPosition in {1,3}
	if(flexPosition>=1 && flexPosition<=flexPoints.length){
		flexPoints[flexPosition] += offset;// + or -
	}
	*/
	//immediately followed by calculateArrow and drawArrow;
}

function getFlexPoints(arrowName){
	var arrow = arrows[arrowName];
	//get value of flex points:
	var flexPoints[] = new Array();
	for(i=1;i<=arrow.flex.length;i++){
		if(isset(arrow.flex[i])){
			flexPoints[i] = arrow.flex[i];
		}else{
			break;
		}
	}
	
	return flexPoints;
}

function drawArrow(origineElt, options){
	
	if(!isset(arrows[origineElt.attr('id')].coord)){
		throw new Exception('the arrow does not exist');
	}
	
	var p = arrows[origineElt.attr('id')].coord;
	options.name = origineElt.attr('id');
	if(isset(p[0])&&isset(p[1])){
		
		drawVerticalLine(p[0], p[1], options);
		if(isset(p[2])){
			drawHorizontalLine(p[1], p[2], options);
			if(isset(p[3])){
				drawVerticalLine(p[2], p[3], options);
				if(isset(p[4])){
					drawHorizontalLine(p[3], p[4], options);
					if(isset(p[5])){
						drawVerticalLine(p[4], p[5], options);
					}
				}
			}
		}
		
		//draw the extremity(a picture?)
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
	
	drawDiv(1,left,top,width,height,options.container,options.name);
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
	
	drawDiv(1,left,top,width,height,options.container,options.name);
}

function drawDiv(border,left,top,width,height,container,name){
	
	if(container && name){
	
		var borderStr = Math.round(border)+'px '+'solid'+' '+'red';
		var element = $('<div name="'+name+'"></div>');
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

function removeArrow(name){
	arrows[name] = null;
	$("div[name="+name+"]").remove();

}

function getDraggableFlexPoints(arrowName){
	//get the postion of flex points, and transform them into draggable object:
	var arrow = arrows[arrowName];
	//get value of flex points:
	var flexPoints[] = new Array();
	for(i=1;i<=arrow.flex.length;i++){
		if(isset(arrow.flex[i])){
			if(i%2){
				//horizontal only:
				authorizedAxis = 'x';
			}else{
				//vertical only:
				authorizedAxis = 'y';
			}
			//create the handle in the middle:
			
			
			//get the element and transform it into a draggable (with constraint):
			$("#arrow_"+arrowName+"_flex"+i).draggable({
				axis: authorizedAxis,
				start: function(event, ui){
					//record position somehow
				},
				drag: function(event, ui){
					//horizontal;
					var currentPosition = $(this).position().left;
					var offset = initialPosition - currentPosition;//initial postion ??????
					
					flexPoints = editArrow(arrowName, flexPosition, offset);//scope of arrowName???
					$("div[name="+arrowName+"]").remove();
					calculateArrow($("#"+arrowName), $("#"+arrow.destination), arrow.type, flexPoints);
					drawArrow($("#"+arrowName), {
						container: "#process_diagram_container",
						arrowWidth: 1
					});
					
				},
				containment: '#process_diagram_container',
				stop: function(event, ui){
				}

			});
			//
		}else{
			break;
		}
	}
	
	
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
	