/**
 *  JS Resource for Credit/Contributors autocomplete
 *
 * @author Mithun Mohan
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
			source: availableRoles,
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
			
			
		var cnt = $('#contributors tbody tr').length;
		

		$('input.contributorRole').autocomplete(acOptions);
		
		//$("input.contributorRole").autocomplete("widget").after("<li> adasd </li>");
			
		$( "input.contributorUser").autocomplete(
			{
				 source: "/video/getUsers",
				select: function(event, ui) {
					event.preventDefault();
					
					if (ui.item.value == "none") {
						
					
					} else {
						$(this).attr("readonly","readonly");
						$(this).val(ui.item.label);
						$(this).parent().prev().find("input").val(ui.item.key)
					}
					
				},
				change: function(event,ui) {
					if ( ui.item == null ) {
						if (validateEmail($(this).val())) {
							$(this).attr("readonly","readonly");
						} else {
							alert(mofilm.lang.messages.userNotFound);
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
							$(this).attr("readonly","readonly");
						}
						
					}
					
				},
				html: true
			}
		);
			
		$('div.controls').show();

		$('div.addContributor').click(function() {
			cnt++;
			var newEles = $(
				'<tr>' +
					'<td><input type="hidden" name="Contributors[' + cnt + '][ID]" value="0" /><span class="recordNumber">' + ($('#contributors tbody tr').length + 1) + '</span></td>' +
					'<td><input type="text" class="contributorUser string" name="Contributors[' + cnt + '][Name]" value="" /></td>' +
					'<td><div class="ui-widget"><input type="text" class="contributorRole small" name="Contributors[' + cnt + '][Role]" value="" /></div></td>' +
					'<td><div class="removeCurContributor formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisContributor + '"><span class="ui-icon ui-icon-minusthick"></span></div></td>' +
					'<td><input type="hidden" name="Contributors['+ cnt +'][roleID]" value="0" /></td></tr>'
				);

			newEles.appendTo('#contributors tbody');
			newEles.find('.contributorRole').autocomplete(acOptions);
			newEles.find('.contributorUser').autocomplete(
				{ 
				source: '/video/getUsers',
				select: function(event, ui) {
					event.preventDefault();
					
					if (ui.item.value == "none") {
						
					} else {					
						$(this).attr("readonly","readonly");
						$(this).val(ui.item.label);
						$(this).parent().prev().find("input").val(ui.item.key)
					}
				},
				change: function(event,ui) {
					if ( ui.item == null  ) {
						if (validateEmail($(this).val())) {
							$(this).attr("readonly","readonly");
						} else {
						
							alert(mofilm.lang.messages.userNotFound);
							$(this).val("");
						}	
						//alert("The user does not exist");
					} else {
					if ( ui.item.value == "none") {
						
						if (validateEmail($(this).val())) {
							$(this).attr("readonly","readonly");
							
						} else {
						
							alert(mofilm.lang.messages.userNotFound);
							$(this).val("");
						}	
							
							//alert("not null user does not exist");
						} else {
							//alert("user exist");
							$(this).attr("readonly","readonly");
						}
						
					}
					
				},
				html: true
			}
				
			);
			newEles.find('div.removeCurContributor').click(function() {
				$(this).parents('#contributors tr').remove();
				$('#contributors span.recordNumber').text(function(index) {
					return index + 1;
				});
			});
		});

		$('input[type=checkbox].addRemoveControl').replaceWith(
			'<div class="removeCurContributor formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisContributor + '"><span class="ui-icon ui-icon-minusthick"></span></div>'
			);

		$('div.removeContributor').click(function() {
			$('#contributors tbody tr').last().remove();
		});

		$('div.removeCurContributor').click(function() {
			$(this).parents('#contributors tr').remove();
			$('#contributors span.recordNumber').text(function(index) {
				return index + 1;
			});
			formChangedWarningBox();
		});
	}

	if  ( $("#myVideoSave").length > 0 ) {
				
		$("#myVideoForm").submit(function(e) {
			$('input.contributorRole').each( function(index) {
				
				if ( $.trim($(this).val()).length < 40  ) {
					
				} else {
					isValid = false;
					message = mofilm.lang.messages.roleDesc;
					$('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>'+message+'</p></div>');
					$('#body div.container div.messageBox').delay(8500).slideUp(200);
					e.preventDefault();
				}
				
				if ( $.trim($(this).parent().parent().prev().children("input").val()).length > 0 && ( $.trim($(this).val()).length <= 1 || $(this).parent().parent().next().next().find("input").val() == 0 ) ) {
					message = mofilm.lang.messages.roleRequired;
					$('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>'+message+'</p></div>');
					$('#body div.container div.messageBox').delay(8500).slideUp(200);
					e.preventDefault();
					
				}

				if ( $.trim($(this).parent().parent().prev().children("input").val()).length == 0 && $.trim($(this).val()).length > 0  ) {
					message = mofilm.lang.messages.creditName;
					$('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>'+message+'</p></div>');
					$('#body div.container div.messageBox').delay(8500).slideUp(200);
					e.preventDefault();
					
				}
				

			});
		});

		$("#movieDetailsForm").submit(function(e) { 
			$('input.contributorRole').each( function(index) {
				
				if ( $.trim($(this).val()).length < 40 ) {
					
				} else {
					isValid = false;
					message = mofilm.lang.messages.roleDesc;
					$('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>'+message+'</p></div>');
					$('#body div.container div.messageBox').delay(8500).slideUp(200);
					e.preventDefault();
				}
				
				if ( $.trim($(this).parent().parent().prev().children("input").val()).length > 0 && ( $.trim($(this).val()).length <= 1 || $(this).parent().parent().next().next().children("input").val() == 0 ) ) {
					message = mofilm.lang.messages.roleRequired;
					$('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>'+message+'</p></div>');
					$('#body div.container div.messageBox').delay(8500).slideUp(200);
					e.preventDefault();
					
				}

				if ( $.trim($(this).parent().parent().prev().children("input").val()).length == 0 && $.trim($(this).val()).length > 0  ) {
					message = mofilm.lang.messages.creditName;
					$('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>'+message+'</p></div>');
					$('#body div.container div.messageBox').delay(8500).slideUp(200);
					e.preventDefault();
					
				}
				
				
			});
		});



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
