// we need both to initialise the api
var itemApi = null;
var frame = null;

function onServiceApiReady(serviceApi) {
	var storage = new ItemVariableStorage(serviceApi.getServiceCallId());
	itemApi = new ItemServiceImpl(serviceApi, storage);
	console.log('Api ready');
	bindApi();
};

var bindApi = function() {
	if (frame != null && itemApi != null) {
		console.log('Connecting');
		itemApi.connect(frame);
	}
}

$(document).ready(function() {
	frame = document.getElementById('item-container');
	if (jQuery.browser.msie) {
		frame.onreadystatechange = function(){	
			if(this.readyState == 'complete'){
				console.log('Frame ready');
				bindApi();
			}
		}
	} else {		
		frame.onload = function(){
			console.log('Frame ready');
			bindApi();
		}
	}
});