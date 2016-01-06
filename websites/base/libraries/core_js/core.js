/**
 * Scorpio Framework Core Javascript include file
 *
 * Contains useful functions for the sites, taken from all over the net
 * if anyone recognises the code and wants credit, then please contact
 * scorpio (at) madagasgar.com and your details will be added to this
 * file and the copyright / about pages on the admin system.
 *
 * @package scorpio
 * @subpackage websites_base_libraries
 * @category core_js
 */


/**
 * Opens a browser window named theName, with URI theUrl and features theFeatures
 *
 * @param string theUrl
 * @param string theName
 * @param string theFeatures
 * @return reference
 */
function openWindow(theUrl, theName, theFeatures) {
	return window.open(theUrl, theName, theFeatures);
}

/**
 * Moves options from fbox to tbox only on selection sets
 *
 * @param string fbox
 * @param string tbox
 * @return void
 */
function move(fbox, tbox, sorttbox) {
	var arrFbox = new Array();
	var arrTbox = new Array();
	var arrLookup = new Array();
	var i;
	for (i = 0; i < tbox.options.length; i++) {
		arrLookup[tbox.options[i].text] = tbox.options[i].value;
		arrTbox[i] = tbox.options[i].text;
	}
	var fLength = 0;
	var tLength = arrTbox.length;
	for(i = 0; i < fbox.options.length; i++) {
			arrLookup[fbox.options[i].text] = fbox.options[i].value;
		if (fbox.options[i].selected && fbox.options[i].value != "") {
			arrTbox[tLength] = fbox.options[i].text;
			tLength++;
		}
		else {
			arrFbox[fLength] = fbox.options[i].text;
			fLength++;
		}
	}
	
	if ( sorttbox === true ) {
		arrFbox.sort();
		arrTbox.sort();
	}
	fbox.length = 0;
	tbox.length = 0;
	var c;
	for(c = 0; c < arrFbox.length; c++) {
		var no = new Option();
		no.value = arrLookup[arrFbox[c]];
		no.text = arrFbox[c];
		fbox[c] = no;
	}
	for(c = 0; c < arrTbox.length; c++) {
		var no = new Option();
		no.value = arrLookup[arrTbox[c]];
		no.text = arrTbox[c];
		tbox[c] = no;
   }
}

/**
 * Sets the status of all checkboxes named theTarget
 *
 * @param string theTarget
 * @param boolean theStatus
 * @return void
 */
function selectAll(theTarget, theStatus) {
	var selected = false;
	formVar = document.getElementById(theTarget);
	for ( var i=0; i < formVar.length; i++ ){
		obj = formVar.elements[i];
		obj.checked = theStatus;
	}
}

/**
 * Sets the status of all options in a select list
 *
 * @param string theTarget
 * @param boolean theStatus
 * @return void
 */
function selectAllOptions(theTarget, theStatus) {
	var selected = false;
	formVar = document.getElementById(theTarget);
	for ( var i=0; i < formVar.length; i++) {
		obj = formVar.options[i];
		obj.selected = theStatus
	}
}

/**
 * Fetches the object named obj
 *
 * @param string obj
 * @return style object property
 */
function getObject(obj) {
	var theObj;
	if(document.all) {
		if(typeof obj=="string") {
			return document.all(obj);
		} else {
			return obj.style;
		}
	}
	if(document.getElementById) {
		if(typeof obj=="string") {
			return document.getElementById(obj);
		} else {
			return obj.style;
		}
	}
	return null;
}

/**
 * Count characters used in entrada and updates display in salida with max set to characteres
 *
 * @param string entrada
 * @param string salida
 * @param string texto
 * @param integer characteres
 * @return void
 */
function Contar(entrada,salida,texto,caracteres) {
	var entradaObj=getObject(entrada);
	var salidaObj=getObject(salida);
	var longitud=caracteres - entradaObj.value.length;
	if (longitud <= 0) {
		longitud=0;
		texto='<span style="color: #f00;">Warning: '+texto+' </span>';
		//entradaObj.value=entradaObj.value.substr(0,caracteres);
	}
	salidaObj.innerHTML = texto.replace("[CHAR]",longitud);
}

/**
 * Returns the element height
 *
 * @param string elemID
 * @return integer
 */
function elementHeight(elmID) {
	if (document.getElementById(elmID).clientHeight) {
		return (document.getElementById(elmID).clientHeight);
	} else if(document.getElementById(elmID).offsetHeight) {
		return (document.getElementById(elmID).offsetHeight);
	} else if(document.getElementById(elmID).innerHeight) {
		return (document.getElementById(elmID).innerHeight);
	}
}

/**
 * Returns the element width
 *
 * @param string elemID
 * @return integer
 */
function elementWidth(elmID) {
	if (document.getElementById(elmID).clientWidth) {
		return (document.getElementById(elmID).clientWidth);
	} else if (document.getElementById(elmID).offsetWidth) {
		return (document.getElementById(elmID).offsetWidth);
	} else if (document.getElementById(elmID).innerWidth) {
		return (document.getElementById(elmID).innerWidth);
	}
}

/**
 * Returns the co-ordinates for the object
 *
 * @param object obj
 * @return array(left,top)
 */
function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
}

/**
 * Places a loading image over the object where ajax content will be loaded
 *
 * @param object element
 * @return void
 */
function ajaxLoader(element) {
	pos = findPos(element);
	size = 'top: '+pos[1]+'px; left: '+pos[0]+'px; width:'+elementWidth(element.id)+'px ; height:'+elementHeight(element.id)+'px;';
	margin = (elementHeight(element.id)-35)/2 ;
	str  = '<div id="ajaxLoaderLayer" align="center" style="display:block; background-color: #0066cc; position: absolute; opacity: 0.50; '+size+';">';
	str += '<img style="margin-top:'+margin+'px; width: 35px; height: 35px; border: 0px;" src="/themes/shared/loaders/loader7.gif" alt="loading" />';
	str += '</div>';
	element.innerHTML = str+element.innerHTML;
}



/**
 * Read the JavaScript cookies tutorial at:
 * http://www.netspade.com/articles/javascript/cookies.xml
 * http://www.netspade.com/2005/11/16/javascript-cookies/
 */

/**
 * Sets a Cookie with the given name and value.
 *
 * name       Name of the cookie
 * value      Value of the cookie
 * [expires]  Expiration date of the cookie (default: end of current session)
 * [path]     Path where the cookie is valid (default: path of calling document)
 * [domain]   Domain where the cookie is valid
 *              (default: domain of calling document)
 * [secure]   Boolean value indicating if the cookie transmission requires a
 *              secure transmission
 */
function setCookie(name, value, expires, path, domain, secure)
{
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

/**
 * Gets the value of the specified cookie.
 *
 * name  Name of the desired cookie.
 *
 * Returns a string containing value of specified cookie,
 *   or null if cookie does not exist.
 */
function getCookie(name)
{
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1)
    {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1)
    {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}

/**
 * Deletes the specified cookie.
 *
 * name      name of the cookie
 * [path]    path of the cookie (must be same as path used to create cookie)
 * [domain]  domain of the cookie (must be same as domain used to create cookie)
 */
function deleteCookie(name, path, domain)
{
    if (getCookie(name))
    {
        document.cookie = name + "=" + 
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}