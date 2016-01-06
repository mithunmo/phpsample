/**
 * Mofilm HTML5 based custom file uploader
 *
 * @author Mithun Mohan
 * @copyright Mofilm Ltd
 * @package mofilm
 * @subpackage websites_base_libraries
 * 
 */


/**
 * Object for XMLHttpRequest
 *
 */
function xmlRequest() {
	this.xhr = null;
	this.create = function() {
		this.xhr = new XMLHttpRequest();
		this.xhr.addEventListener("load", uploadComplete, false);
		this.xhr.addEventListener("error", uploadFailed, false);
		this.xhr.addEventListener("abort", uploadCanceled, false);		
	};
	
	this.getRequest = function() {
		return this.xhr;
	};
	
	this.endRequest = function() {
		this.xhr =null;
	};
}

/**
 * Object for file being uploaded
 *
 */
function fileProperties() {
	this.fileName = null;
	this.fileType = null;
	this.fileSize = 0;
	this.fileBlock = 0;
	this.fileUploadTime = 0;
	this.totalChunks = 0;
	
	
	
	this.setFileName = function(inFileName) {
		this.fileName = inFileName;
	};
	
	this.getFileName = function() {
		return this.fileName;
	};

	this.setFileType = function(inFileType) {
		this.fileType = inFileType;
	};
	
	this.getFileType = function() {
		return this.fileType;
	};

	this.setFileSize = function(inFileSize) {
		this.fileSize = inFileSize;
	};
	
	this.getFileSize = function() {
		return this.fileSize;
	};

	this.setFileBlock = function(inFileBlock) {
		this.fileBlock = inFileBlock;
	};
	
	this.getFileBlock = function() {
		return this.fileBlock;
	};

	this.setFileUploadTime = function(inFileUploadTime) {
		this.fileUploadTime = inFileUploadTime;
	};
	
	this.getFileUploadTime = function() {
		return this.fileUploadTime;
	};

	this.setFileChunks = function(inFileChunks) {
		this.fileChunks = inFileChunks;
	};
	
	this.getFileChunks = function() {
		return this.fileChunks;
	};
	
	this.checkValidType = function(inValue) {
		for (i=0;i<typeArr.length;i++)
		{
			if ( typeArr[i] == inValue ) {
				return true;
			}
		}
		return false;
	};
}

/**
 * Object for earch chunk of a file
 *
 */
function chunkProperties() {
	this.chunkStart = 0;
	this.chunkEnd = 0;
	this.chunkPosition = 0;
	
	
	
	this.setChunkStart = function(inChunkStart) {
		this.chunkStart = inChunkStart;
	};
	
	this.getChunkStart = function() {
		return this.chunkStart;
	};

	this.setChunkEnd = function(inChunkEnd) {
		this.chunkEnd = inChunkEnd;
	};
	
	this.getChunkEnd = function() {
		return this.chunkEnd;
	};

	this.setChunkPosition = function(inChunkPosition) {
		this.chunkPosition = inChunkPosition;
	};
	
	this.getChunkPosition = function() {
		return this.chunkPosition;
	};


}

function supportAjaxUploadProgressEvents() {
    var xhr = new XMLHttpRequest();
    return !! (xhr && ('upload' in xhr) && ('onprogress' in xhr.upload));
};



//if (window.File && window.FileReader && window.FileList && window.Blob && supportAjaxUploadProgressEvents() ) {
			
//} else {
	jQuery(document).ready(function() {
		$("#uploadForm").remove();
		$("#uploader").plupload({
			// General settings
			runtimes : "flash,html5,html4",
			url : "/account/upload/doUpload/",
			max_file_size : "300mb",
			max_file_count: 1, // user can add no more then 20 files at a time
			chunk_size : "1mb",
			unique_names : true,
			multipart: true,
			multipart_params: {
				'uploaderType': 2 // use filename as a key
			},
			// Resize images on clientside if we can
			resize : {
				width : 220, 
				height : 140, 
				quality : 90
			},

			// Rename files by clicking on their titles
			rename: true,

			// Sort files
			sortable: true,

			// Specify what files to browse for
			filters : [
			{
				title : "Video files", 
				extensions : "avi,mpg,mov,wmv,mp4,m4v"
			}
			],

			// Flash settings
			flash_swf_url : "/libraries/plupload/js/plupload.flash.swf"
		});
		
		var Uploader = $('#uploader').plupload('getUploader');

		Uploader.bind('FileUploaded', function(Up, File, Response) {	
			if( (Uploader.total.uploaded ) == Uploader.files.length) {
				document.getElementById("fileNameStored").value = File.target_name;
			};
		});
	
		Uploader.bind('FilesAdded', function(Up, File) {
			$("#uploadStatus").val("Started");	
			Up.refresh();
		});
		
		Uploader.bind('UploadComplete', function(Up, File) {
			$("#uploadStatus").val("");	
		});
		
		$("#uploader_stop").click(function() {
			var Uploader = $('#uploader').plupload('getUploader');
			$("#uploadStatus").val("");	
			Uploader.destroy();
			window.location.reload();
		});
		
	});
//}
			

var xhrReq =false;
var filePropObj = false;
var chunkPropObj = false;
var typeArr = ["avi", "mpg", "mov", "wmv", "mp4", "m4v"];

/**
 * This is called when user selects a file and it intialises the 3 js objects defined 
 *
 */
function fileSelected() {
	var file = document.getElementById("fileToUploadID").files[0];
	if ( file ) {
		var fileSize = 0;
		if ( file.size > 1024 * 1024 ) {
			fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + "MB";
		} else {
			fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + "KB";
		}	
		
		document.getElementById("fileName").innerHTML = file.name;
		document.getElementById("fileSize").innerHTML = fileSize;
		
		jQuery(document).ready(function() {
			$("#pb1").progressBar();
			$("#pb1").progressBar(0);
			$("#uploadMain").removeAttr("disabled");
			$("#uploadCancel").removeClass("ui-state-active");
			$("#uploadResume").removeClass("ui-state-active");
			$("#uploadStatus").val("Started");	
		});
		
		xhrReq = new xmlRequest();
		filePropObj = new fileProperties();
		chunkPropObj = new chunkProperties();

		
		filePropObj.setFileName(file.name);
		filePropObj.setFileSize(file.size);
		filePropObj.setFileType(/[^.]+$/.exec(filePropObj.getFileName()));
		
		if ( !filePropObj.checkValidType(filePropObj.getFileType()) ) {
			alert("Not supported");
			window.location.reload();
			return false;
		}
		filePropObj.setFileBlock(1000000);
		var d = new Date();
		filePropObj.setFileUploadTime(d.getTime());
		var chunks = Math.floor(filePropObj.getFileSize()  /  filePropObj.getFileBlock());
		var remChunks = Math.floor(filePropObj.getFileSize()  %  filePropObj.getFileBlock());
		if ( remChunks != 0 ) {
			chunks++;
		}
		filePropObj.setFileChunks(chunks);
		document.getElementById("fileNameStored").value = d.getTime() + filePropObj.getFileName();
	}
}			

/**
 * Handles the uploading of chunks
 *
 */
function uploadChunk() {
	var blob = document.getElementById("fileToUploadID").files[0];					
	if (blob) {
		if ( chunkPropObj.getChunkEnd() == 0 ) {
			chunkPropObj.setChunkStart(0);
			chunkPropObj.setChunkEnd(filePropObj.getFileBlock());
		}

		var chunk = false;
		if ( blob.slice ) {
			chunk = blob.slice(chunkPropObj.getChunkStart(), filePropObj.getFileBlock());	   ///only few browser support this
		} else if ( blob.mozSlice ) {
			chunk = blob.mozSlice(chunkPropObj.getChunkStart(), chunkPropObj.getChunkEnd());
		} else if ( blob.webkitSlice) {
			chunk = blob.webkitSlice(chunkPropObj.getChunkStart(), chunkPropObj.getChunkEnd());
		} else {
			alert("No support");
			return false;
		}

		xhrReq.create();
		var xhrobj = xhrReq.getRequest();

		xhrobj.open("POST", "/account/upload/doUpload/", "true");				
		var fd = new FormData();
		fd.append("file", chunk);
		fd.append("chunk", chunkPropObj.getChunkPosition());
		fd.append("chunks", filePropObj.getFileChunks());
		fd.append("name", filePropObj.getFileUploadTime()+filePropObj.getFileName());
		fd.append("uploaderType", 1);
		xhrobj.send(fd);			
	}
}

/**
 * Handles the resume part of upload
 *
 */
function resumeChunk() {
	if (xhrReq) {
		if ( xhrReq.getRequest() == null ) {
			uploadChunk();
		} else {
			xhrReq.endRequest();
			uploadChunk();		
		}
	}
}

/**
 * Handles the part after an XMLHttpRequest is complete
 *
 */
function uploadComplete(evt) {
	/* This event is raised when the server send back a response */
	chunkPropObj.setChunkStart(chunkPropObj.getChunkEnd());
	chunkPropObj.setChunkEnd(chunkPropObj.getChunkStart() + filePropObj.getFileBlock());
	xhrReq.endRequest();
	chunkPos = chunkPropObj.getChunkPosition();
	chunkPos++;
	var percentComplete = Math.floor( chunkPos * 100 / filePropObj.getFileChunks() );
	jQuery(document).ready(function() {
		$("#pb1").progressBar(parseInt(percentComplete));
	});
	
	if ( chunkPropObj.getChunkStart() < filePropObj.getFileSize()  && chunkPropObj.getChunkPosition() < filePropObj.getFileChunks() )  {
		var pos = chunkPropObj.getChunkPosition();
		pos ++;
		chunkPropObj.setChunkPosition(pos);
		uploadChunk();
	} else {
		$("#uploadStatus").val("");	
		return false;
	}
				
				
}

/**
 *  This event is called when there is an error
 * 
 */
function uploadFailed(evt) {
	alert("There was an error attempting to upload the file.");
}

/**
 * This event is called when the user stops the download
 * 
 */
function uploadCanceled(evt) {
	if (xhrReq) {
		var xhrobj = xhrReq.getRequest();	
		xhrobj.abort();
		xhrReq.endRequest();
	}
} 
