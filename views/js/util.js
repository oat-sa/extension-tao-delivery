processUtil = new Object();

processUtil.isset = function(object){

	if(typeof(object)=='undefined' || object===null){
		return false;
	}else{
		return true;
	}

}