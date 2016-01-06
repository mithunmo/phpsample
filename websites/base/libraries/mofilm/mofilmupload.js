$(function() {
	$("#uploader").plupload({
		// General settings
		runtimes : 'html5,flash,html4',
		url : '/account/upload/doplupload',
		max_file_size : '2000mb',
		max_file_count: 1, // user can add no more then 20 files at a time
		chunk_size : '1mb',
		unique_names : true,
		multiple_queues : false,

		// Resize images on clientside if we can
		resize : {
			width : 320, 
			height : 240, 
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
			extensions : "mp4,mov,mpg,m4v,wmv,avi"
		}
		],

		flash_swf_url : '/themes/libraries/plupload/js1/plupload.flash.swf'

	});

	// Client side form validation
	$('#plu').submit(function(e) {
				
		var uploader = $('#uploader').plupload('getUploader');
				

		// Files in queue upload them first
		if (uploader.files.length > 0) {
			// When all files are uploaded submit form
			uploader.bind('StateChanged', function() {
				if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
					$('form')[0].submit();
				}
			});
                
			uploader.start();
			
			
			
			
			
		} else
			alert('You must at least upload one file.');

		return false;
	});
	 

	if ( $('#basicuploader').length > 0 ) {
		$('#signup').click(function() {
			
			if ( $("#fileNameStored").val() == "" ) {
				alert("Upload a file ");
				return false;
			}
			
			$('#basicuploader').validate({
				rules: {
					title: "required",
					description: "required"
				},
				messages: {
					title: {
						required: "Enter the title"
					},
					description: {
						required: "Enter the description"
					}
				}
			});
		});
	}


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
