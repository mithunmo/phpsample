$('#photouploader').submit(function(){

			var isValid = true;    
			var title = $('#Title').val();
			var desc = $('#Description').val();
			
			if ( $("#photoFileName").val() == "" ) {
				alert("Upload a file ");
				isValid = false;
			}
			
			if ( title && title.length > 2 && title.length < 55 ) {
				$('#msg_title').html('').hide();
			} else {
				isValid = false;
				$('#msg_title').html(mofilm.lang.messages.titleRequired).show();
			}
			
			if ( desc && desc.length > 2 ) {
				$('#msg_desc').html('').hide();
			} else {
				isValid = false;
				$('#msg_desc').html(mofilm.lang.messages.descRequired).show();
			}
			
			if ( $('#tnc').is(':checked') ) {
				$('#msg_agree').html('').hide();
			} else {
				isValid = false;
				$('#msg_agree').html(mofilm.lang.messages.acceptTerms).show();
			}
			
			if ( isValid ) {
			    $.blockUI();
			} else {
			    return isValid;
			}
});