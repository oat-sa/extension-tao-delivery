/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
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