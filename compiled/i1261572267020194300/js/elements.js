/**
 * @author
 */

// Properties
var flashHeight;
var flashWidth;

// Utility methods
replace = function (string, toFind, toReplaceWith){
    return string.split(toFind).join(toReplaceWith);
}
 
// Shows Element
function showElement(id){
    $(id).css({ display: "block"});
}

// Hides Element
function hideElement(id){
    $(document).ready(function(){
        $(id).css({ display: "none"});
    });
}

// Inserts Element ()
function insertUrlEncodedContentInNode(id,expr){
    $(document).ready(function(){
        $(id).html(unescape(expr));
    });
}

// Inserts Element dedicated to Javascript
function insertContentInNodeJs(id){
    $(document).ready(function(){
        $(id).html('<script type="text/javascript" src="js/test.js"></script>');
    });
}

// Removes Element
function removeNodeContent(id){
    var idElement_str = (id.indexOf("#") == 0) ? id.substr(1) : id ; 
    var element_obj = document.getElementById(idElement_str);
    while (element_obj.firstChild){
        element_obj.removeChild(element_obj.firstChild);
    } 
}

// Gets Flash object's Id
function getFlashId(){
    return "flashMovie";
}

// Resizes Element
function resizeElement(id,newWidth,newHeight){
    $(document).ready(function(){
        $(id).css({ width: newWidth, height: newHeight });
    });
}

// Hides Flash object
function hideFlash(idFlashMovie){
    $(document).ready(function(){
        $(idFlashMovie).css({visibility: "hidden"});
        flashHeight=Number($(idFlashMovie).attr('height'));
        flashWidth=Number($(idFlashMovie).attr('width'));
        resizeElement(idFlashMovie,1,1);
    });
}

// Shows Flash object
function showFlash(idFlashMovie){
    $(document).ready(function(){
        $(idFlashMovie).css({visibility: "visible"});
        resizeElement(idFlashMovie,flashWidth,flashHeight);
    });
}

// Shows Flash object
function setPosition(id,Xpos,Ypos){
    $(document).ready(function(){
        $(id).css({ position: 'absolute', top: Ypos, left: Xpos });
    });
}


// Reset CSS Style
function resetStyle(){
    $(document).ready(function(){
        $("#bodyText").attr("style","position:relative;");
        $("#flashMovie").attr("style","position:relative;");
    });
}
