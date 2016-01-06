/**
 * Mofilm TinyMCE Validation/Functions JS Resource
 *
 * @author Mithun Mohan
 * @copyright Mofilm Ltd
 * @package mofilm
 * @subpackage websites_base_libraries
 * 
 */
jQuery(document).ready(function() {

	if ( $('#tinyMCEImagefileID').length > 0 ) {
		$('#tinymceUpload').click(function() {
			$("#tinymceForm").validate({
				rules: {
					tinyMCEImagefile: {
						required: true,
						accept: "png|jpg|jpeg|gif"
					}
				},
				messages: {
					tinyMCEImagefile: {
						required: "Enter a valid extension"
					}
				}
			});
		});
	}
	
	if ( $('#tinyBody').length > 0 ) {
		$('#tinyDelete').click(function() {
			$("input:checked").each(function() {
				var pathdir = $(this).attr("id");
				$.post(
					"/tinymceAction/deletedir",
					{
						path: pathdir
					},
					function(data) {
						alert(data);
						location.reload();
					}
				);
			});
			return false;
		});
	}

	if ( $('#tinymceFolder').length > 0 ) {
		$('#tinymceCreatedir').click(function() {
			$("#tinymceNewfolder").validate({
				rules: {
					newdir: {
						required  : true,
						maxlength : 12,
						minlength : 3
					}
				},
				messages: {
					newdir: {
						required  : "Enter a valid name between 3 and 10 chars",
						maxlength : "Enter a max of 12 chars",
						minlength : "Enter a min of 3 chars"
					}
				}
			});
		});
	}
});