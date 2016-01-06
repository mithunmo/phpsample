function fileBrowser(field_name, url, type, win) {
	var cmsURL = "/tinymceAction/browse";
 	if (cmsURL.indexOf("?") < 0) {
 	//add the type as the only query parameter
 	cmsURL = cmsURL + "?type=" + type;
 	}
 	else {
 	//add the type as an additional query parameter
 	// (PHP session ID is now included if there is one at all)
 	cmsURL = cmsURL + "&type=" + type;
 	}

 	tinyMCE.activeEditor.windowManager.open({
 	file : cmsURL,
 	title : 'File Browser',
 	width : 640, // Your dimensions may differ - toy around with them!
 	height : 480,
 	resizable : "yes",
 	inline : "yes", // This parameter only has an effect if you use the inlinepopups plugin!
 	close_previous : "no"
 	}, {
 	window : win,
 	input : field_name
 	});
 	return false;
 }
