	var FileBrowserDialogue = {
		    init : function () {
		    }
	}

	function selectImage(url) {

		      var win = tinyMCEPopup.getWindowArg("window");

		      // insert information now
		      win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = url;

		      // update preview image in dialog
		      win.ImageDialog.showPreviewImage(url);

		      // for image browsers: update image dimensions
		      if (win.getImageData) win.getImageData();

    		      // close popup window
		      tinyMCEPopup.close();
	}

	tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);
