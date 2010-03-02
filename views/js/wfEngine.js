function openProcess(url)
{
		var processView = document.getElementById("process_view");
		$(processView).empty();
		var req = null; 
		if(window.XMLHttpRequest)
			req = new XMLHttpRequest(); 
		else if (window.ActiveXObject)
			req  = new ActiveXObject(Microsoft.XMLHTTP); 

		req.onreadystatechange = function()
		{ 
			
			if(req.readyState == 4)
			{
				if(req.status == 200)
				{
					processView.innerHTML = req.responseText+"<a style=\"position:absolute;\" href=\"#\"><img  src=\"../../views/PIAAC/img/close_process_view.png\" onClick=\"closeProcess()\"/></a>";	
				}	
				else	
				{	
					//document.getElementById("zone").innerHTML =="Error: returned status code " + req.status + " " + req.statusText;
				}	
			} 
		}; 
		 

		req.open( "GET", url,  true); 
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
		req.send(null); 
	
	$("#process_view").fadeIn('slow');
	$('#process_view').css('left', $(window).width() / 2 - 320 + 'px');
}
this.closeProcess = function()
{
	$("#process_view").fadeOut('slow');
}