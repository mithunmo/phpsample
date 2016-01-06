/**
 *  JS Resource for Credit/Contributors autocomplete
 *
 * @author Poulami Chakraborty
 * @copyright Mofilm Ltd
 * @package mofilm
 * @subpackage websites_mofilm.com_libraries
 * @version $Rev: 393 $
 */
jQuery(document).ready(function(){
	


	/*
	 * Add fancy contributor editing
	 */
	  if ($('#contributors').length > 0) {
              
		var acOptions = {

			minLength: 1,
			select: function(event,ui) {
				$(this).parent().parent().next().next().find("input").val(ui.item.key)
			},
			change: function(event,ui) {
                       if ( !ui.item ) {
						   $(this).val("");
							message = mofilm.lang.messages.predefinedRole;
							$('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>'+message+'</p></div>');
							$('#body div.container div.messageBox').delay(4500).slideUp(200);
						   
						   //alert("Select Role from the list");
						   return false;
                       } else {
						   $(this).parent().parent().next().next().find("input").val(ui.item.key)
					   }
			},
			html:true

		};
			
		$( "input.contributorUser").autocomplete(
			{
				 source: "/video/getUsers",
				select: function(event, ui) {
					event.preventDefault();
					if (ui.item.value == "none") {
						
					
					} else {
						$(this).val(ui.item.label);
                                                $('#FilmMaker').val(ui.item.key);
						$(this).parent().prev().find("input").val(ui.item.key)
					}
					
				},
				change: function(event,ui) {
					if ( ui.item == null ) {
						if (validateEmail($(this).val())) {
							//$(this).attr("readonly","readonly");
						} else {
							$(this).val("");
						
						}
					} else {
						if ( ui.item.value == "none") {
							if (validateEmail($(this).val())) {
								$(this).attr("readonly","readonly");
							} else {

								alert(mofilm.lang.messages.userNotFound);
								$(this).val("");
							}	
						} else {
							//$(this).attr("readonly","readonly");
						}
						
					}
					
				},
				html: true
			}
		);		
	} 
        
	function validateEmail(txtEmail){
	   //var a = document.getElementById(txtEmail).value;
	   var filter = /^((\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*?)\s*;?\s*)+/;
		if(filter.test(txtEmail)){
			return true;
		}
		else{
			return false;
		}
	}
	

});
