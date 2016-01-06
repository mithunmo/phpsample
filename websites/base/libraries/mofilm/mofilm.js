/**
 * Mofilm JS Resource
 *
 * @author Dave Redfern
 * @copyright Mofilm Ltd
 * @package mofilm
 * @subpackage websites_mofilm.com_libraries
 * @version $Rev: 393 $
 */
jQuery(document).ready(function(){
	
	var messageBox = function(status, message) {
		$('#body div.container').append('<div class="messageBox '+status+'"><p>'+message+'</p></div>');
		$('#body div.container div.messageBox').delay(10000).slideUp(200);
	};
	
	var formChangedWarningBox = function() {
		if ( $('#formWarningBox').length == 0 ) {
			$('#body div.container').append('<div id="formWarningBox" class="messageBox warning"><p>'+mofilm.lang.messages.formContentsChanged+'</p></div>');
			$('#body div.container div.messageBox').delay(8500).slideUp(200);
		}
	};
	
	/*
	 * Check for forms and attach a change() event listener to display a
	 * warning that the form needs saving
	 */
	if ( $('form.monitor').length > 0 ) {
		$('form.monitor').each(function() {
			$(this).data('initialValues', $(this).serialize());
			$(this).change(function() {
				formChangedWarningBox();
			});
		});
	}
	
	/*
	 * Make message boxes closeable
	 */
	if ( $('.messageBox').length > 0 ) {
		$('.messageBox.closeable').each(function() {
			$(this).append('<div class="click_to_close" style="cursor: pointer;"></div>');
		});
		$('.click_to_close').click(function() {
			$(this).parent().clearQueue();
			$(this).parent().slideUp(200);
		});
		$('div.messageBox').delay(5000).slideUp(200);
	}
	
	/*
	 * Add a generic AJAX get call, expects back a JSON object containing status and message
	 */
	if ( $('a.ajaxUpdate').length > 0 ) {
		$('a.ajaxUpdate').click(function(){
			$.get(
				$(this).attr('href')+"/as.json",
				null,
				function(data, textStatus, XMLHttpRequest) {
					messageBox(data.status, data.message);
					return false;
				},
				'json'
				);
			return false;
		});
	}
	
	/*
	 * Add datepicker
	 */
	if ( $('.datepicker').length > 0 ) {
		$('.datepicker').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			yearRange: '2005:+2'
		});
	}

	/*
	 * Add validation to profile
	 */
	if ( $('#profileForm').length > 0 ) {
		var active = $('#ProfileActive').val();

		$('#profileForm').validate({
			ignore: ".ignore",
			rules: {
				Firstname: "required",
				Surname: "required",
				Password: {
					minlength: 8,
					equalTo: "#confirmPassword"
				},
				confirmPassword: {
					equalTo: "#newPassword"
				}
			},
			messages: {
				Firstname: {
					required: mofilm.lang.messages.profileFirstnameRequired
				},
				Surname: {
					required: mofilm.lang.messages.profileSurnameRequired
				},
				Password: {
					equalTo: mofilm.lang.messages.profilePasswordConfirm,
					minlength: jQuery.format(mofilm.lang.messages.profilePasswordMinLength)
				},
				confirmPassword: {
					equalTo: mofilm.lang.messages.profilePasswordConfirm
				}
			},
			submitHandler: function(form) {
			
				if ( $('.skillsp:checked').size() <=4 && $('.skillsp:checked').size() >=1  ) {
				
				} else {
					alert("Select upto 4 skills");
					return false;
				}
				
				$.post(
					"/account/profile/doUpdate/as.json",
					$(form).serialize(),
					function(data, textStatus, XMLHttpRequest) {
						$(".profileLink").attr("href", "http://mofilm.com/user/"+$("#profileName").val());
						messageBox(data.status, data.message);
					},
					"json"
					);

				if ( active != $('#ProfileActive').val() ) {
					document.reload();
				}

				return false;
			}
		});
	}

	/*
	 * Add validation to login form
	 * validate only if mofilm username and password is being used, it will bypass if one is using facebook login
	 */
	if ( $('#loginForm').length > 0 ) {
		$('.login').click(function() {
			$('#loginForm').validate({
				rules: {
					username: "required",
					password: "required"
				},
				messages: {
					username: {
						required: mofilm.lang.messages.loginErrorUsername
					},
					password: {
						required: mofilm.lang.messages.loginErrorPassword
					}
				}
			});
		});
	}


	$.validator.addMethod("valueNotEquals", function(value, element, arg){
		return arg != value;
	}, "Value must not equal arg.");


	/*
	 * Add validation to register form
	 * validate only if mofilm username and password is being used, it will bypass if one is using facebook login
	 */
	
	if ( $('#registerForm').length > 0 ) {
		$("#")
		$('.signup').click(function() {
			$('#registerForm').validate({
				rules: {
					territory:  {
						valueNotEquals: 0
					},
					SignupCode: {
						valueNotEquals: 0
					},
					"Skills[]": {
						minlength: 1, 
						maxlength: 4
					}
					

				},
				messages: {
					territory: {
						valueNotEquals: "Please select an item!"
					},
					SignupCode: {
						valueNotEquals: "Please select an item!"
					},
					"Skills[]": "Please select upto 4 skills"
				}
			});
		});
	}
	


	/*
	 * Add date selector to DOB field
	 */
	if ( $('#propertyDateOfBirth').length > 0 ) {
		$('#propertyDateOfBirth').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			yearRange: '1900:+0'
		});
	}
	
	/*
	 * Use accordion on user info form
	 */
	if ( $('#userFormAccordion').length > 0 ) {
		$('#userFormAccordion').accordion({
			collapsible: true,
			autoHeight: false,
			navigation: true
		});
	}

	/*
	 * Auto-collapse help text
	 */
	if ( $('.helpText').length > 0 ) {
		$('.helpText').hide();
		$('#generalHelpToggle').click(function(){
			$('#generalHelp').toggle();
		});
		$('.helpToggle').attr('title', mofilm.lang.messages.helpTextTitle);
		$('.content .helpToggle').click(function() {
			$(this).parents('.formFieldContainer').find('.helpText').toggle();
		});
	}
	
	/*
	 * Add ajax calls for PMs
	 */
	if ( $('a.deletePm').length > 0 ) {
		$('a.deletePm').click(function(event){
			uri = $(this).attr('href')+'/as.json';
			
			$.get(
				uri,
				function(data, textStatus, XMLHttpRequest){
					messageBox(data.status, data.message);
				},
				"json"
				);
			
			$(this).parents('tr').remove();
			
			return false;
		});
	}
	
	/*
	 * Add profile image upload controls
	 */
	if ( $('img.profileImage').length > 0 ) {
		$('img.profileImage').addClass('pointer');
		$('img.profileImage').click(function(event){
			$(this).slideUp(200, function(){
				$('#upload').show();
			});
		});
		$('#upload .cancel').click(function(event){
			$('#upload').hide();
			$('img.profileImage').slideDown(200);
		});
	}
	
	/*
	 * Switch county field to a selector for US / Canada
	 */
	if ( $('#territoryID').length > 0 ) {
		if ( parseInt($('#territoryID').val()) == 39 || parseInt($('#territoryID').val()) == 233 ) {
			$( "#County" ).autocomplete({
				source: availableTags
			});
		}
		$('#territoryID').change(function(event){
			if ( parseInt($(this).val()) == 39 || parseInt($(this).val()) == 233 ) {
				$( "#County" ).autocomplete({
					source: availableTags, 
					disabled: false
				});
			} else {
				$( "#County" ).autocomplete({
					disabled: true
				});
			}
		});
	}
	
	/*
	 * Add profile name check
	 */
	if ( $('#profileName').length > 0 ) {
		$('#profileName').bindWithDelay('keyup', function(event){
			$.post(
				'/account/profile/checkName/as.json',
				{
					ProfileName: $(this).val()
				},
				function(data, textStatus, XMLHttpRequest){
					$('#profileNameLive').html($('#profileName').val());
					messageBox(data.status, data.message);
				},
				"json"
				);
		}, 400);
	}
	
	/*
	 * Add sortable profile movie stuff
	 */
	if ( $('#profileMovies').length > 0 ) {
		var setEditable = function(selector) {
			selector.editable(
				'/account/profile/updateProfileMovie/as.json',
				{
					indicator : '<img src="/themes/shared/loaders/loader9.gif" alt="loading" class="smallIcon" />',
					tooltip   : mofilm.lang.messages.profileMovieEditText,
					cssclass  : 'ignore',
					style     : 'display: inline;',
					cancel    : '<img src="/themes/shared/icons/cancel.png" title="Cancel Edit" alt="cancel" style="margin-left: 5px;" class="smallIcon" />',
					submit    : '<img src="/themes/shared/icons/disk.png" title="Save Changes" alt="save" style="margin-left: 5px;" class="smallIcon" />',
					width     : '350px',
					height    : '16px',
					callback : function(value, settings) {
						id = parseInt(this.id.replace(/movID-/, ''));
						$(this).parent().find('input[name="ProfileMovieTitles['+id+']"]').val(value);
					}
				}
				);
			selector.editable('enable');
		};
		
		$( "#profileMovies, #availableProfileMovies" ).sortable({
			connectWith: ".connectedSortable",
			receive: function(event, ui) {
				if ( $('#profileMovies').children().length > 5 ) {
					//ui.sender: will cancel the change. Useful in the 'receive' callback.
					$(ui.sender).sortable('cancel');
				}
				if ( $(this).children().length > 0 ) {
					// auto-select and attach editable
					$('#profileMovies').find(':checkbox').attr('checked', true);
					$('#profileMovies .movieTitle').addClass('editable');
					setEditable($('#profileMovies .movieTitle.editable'));
					
					// de-select and remove editable
					$('#availableProfileMovies').find(':checkbox').attr('checked', false);
					$('#availableProfileMovies .movieTitle').removeClass('editable').editable('disable');
				}
			},
			stop: function(event, ui) {
				$('#profileForm').submit();
			}
		}).disableSelection();
		
		setEditable($('#profileMovies .movieTitle.editable'));
	}

	if ( $("#uploadForm").length > 0 ) {
		$(":button").hover(
			function () {
				$(this).addClass("ui-state-hover");
			},
			function () {
				$(this).removeClass("ui-state-hover");
			}
			);
			
		$("#uploadResume").click(function() {
			$(this).addClass("ui-state-active");
			$("#uploadCancel").removeClass("ui-state-active");
			resumeChunk();
		});
		
		$("#uploadCancel").click(function() {
			$(this).addClass("ui-state-active");
			$("#uploadResume").removeClass("ui-state-active");
			uploadCanceled();
		});

		$("#uploadMain").click(function() {
			$(this).attr("disabled", "disabled");
			$("#uploadResume").removeAttr("disabled");
			$("#uploadCancel").removeAttr("disabled");
			uploadChunk();
		});
	
		$("#uploadStop").click(function() {
			window.location.reload();
		});
	
		
	}
	
	if ( $('#wizard').length > 0 ) {
		
		function onFinishCallback() {
			if ( validateSteps(3) ) {
				$('form').submit();
				$(".buttonFinish").attr("class","buttonFinish buttonDisabled");
			} 
		}
		
		function leaveAStepCallback(obj) {
			$('.msgBox').remove();
			var step_num= obj.attr('rel');
			return validateSteps(step_num);
		}
		
		function validateSteps(step) {
			var isStepValid = true;
			if ( step == 2 ) {
				if ( validateStep2() == false ){
					isStepValid = false; 
					$('#wizard').smartWizard('showMessage',mofilm.lang.messages.uploadStep+step+mofilm.lang.messages.uploadStepNext);
					$('#wizard').smartWizard('setError',{
						stepnum:step,
						iserror:true
					});         
				} else {
					$('#wizard').smartWizard('setError',{
						stepnum:step,
						iserror:false
					});
				}
			}
			if ( step == 1 ) {
				if ( validateStep1() == false ){
					isStepValid = false;
					$('#wizard').smartWizard('setError',{
						stepnum:step,
						iserror:true
					});         
				} else {
					$('#wizard').smartWizard('setError',{
						stepnum:step,
						iserror:false
					});
				}
			}
			if ( step == 3 ) {
				if ( validateStep3() == false ){
					isStepValid = false; 
					$('#wizard').smartWizard('setError',{
						stepnum:step,
						iserror:true
					});         
				} else {
					$('#wizard').smartWizard('setError',{
						stepnum:step,
						iserror:false
					});
				}
			}
			
			
			return isStepValid;
		}
		
		function validateStep1() {
			var isValid = true;    
			var eventName = $('#eventUpload').val();
			var sourceName = $('#sourceUpload').val();
			
			if ( eventName && eventName.length > 0 ) {
				$('#msg_event').html('').hide();
			} else {
				isValid = false;
				$('#msg_event').html(mofilm.lang.messages.eventRequired).show();
			} 
			
			if ( sourceName && sourceName.length > 0 ) {
				$('#msg_source').html('').hide();
			} else {
				isValid = false;
				$('#msg_source').html(mofilm.lang.messages.sourceRequired).show();
			} 

			if ( $('#tnc').is(':checked') ) {
				$('#msg_agree').html('').hide();
				
			} else {
				isValid = false;
				$('#msg_agree').html(mofilm.lang.messages.acceptTerms).show();
				
			}
			
	
			return isValid;
		}
		
		function validateStep2() {
			var isValid = true;    
			var status = $('#msg_filename').text();
						
			if ( status && status.length > 1 ) {
			} else {
				isValid = false;
			} 
			
			return isValid;
		}
		
		function validateStep3() {					
			var isValid = true;    
			var title = $('#movieTitle').val();
			var desc = $('#movieDesc').val();
			var credit = $('#movieCredit').val();
			var duration = $('#movieDuration').val();
			var customLicense = $('#cLicense').val();
			var mmLicense = $("#licenseContent tr td input:checked").val();
			
			/*var movietags = $('#mofilmMovieTag').val();
			var movietagsarray = movietags.split(',');
			
			$.each(movietagsarray, function(i, n) {
				if ( $.trim(n).length > 40 ) {
					isValid = false;
					alert(mofilm.lang.messages.tagsLimit);
				}
			});*/
			
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

			var $checkBoxes = $(".industry");
			var checkCount = 0;
			$checkBoxes.each(function(){ if( this.checked ) checkCount++; });

			if( checkCount > 1 ) {
				$checkBoxes.removeAttr("checked");
				alert( 'Please select one Industry in Tags' );
				isValid = false;
			}
			

			if ( customLicense.length > 0 || mmLicense != undefined ) {
				

			} else {
				isValid = false;
				alert(mofilm.lang.messages.licenseInfoRequired);
			} 
			
			$('input.contributorRole').each( function(index) {
				if ( $.trim($(this).val()).length < 20 ) {
					
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
			
			
			
			/*	
			if ( credit && credit.length > 0 ) {
				$('#msg_credit').html('').hide();
			} else {
				isValid = false;
				$('#msg_credit').html('Credit Required').show();
			} 

			if ( duration && duration.length > 0 && !isNaN(duration) ) {
				$('#msg_duration').html('').hide();
			} else {
				isValid = false;
				$('#msg_duration').html('Duration Required').show();
			} 
		*/	
			return isValid;			
		}
		
		function showStepCallback(obj) {
			var step_num= obj.attr('rel');
		}
		
		$('#wizard').smartWizard( 
		{
			onFinish : onFinishCallback, 
			onLeaveStep:leaveAStepCallback, 
			onShowStep:showStepCallback, 
			selected:0,
			labelNext: mofilm.lang.messages.nextb,
			labelPrevious: mofilm.lang.messages.prevb,
			labelFinish: mofilm.lang.messages.finishb
		} 
		);
		
	}

	if ($('#commitUserMovie').length > 0) {
		$('#commitUserMovie').click(function() {
			$.post(
				"/account/review/readyToCommit/as.json",
				{
					movieID : $("#usermovieID").val()
				},
				function(data) {
					if ( data.name == "done" ) {
						$.post(
							"/account/review/userCommit/as.json",
							{
								movieID : $("#usermovieID").val()
							},
							function(data) {
								alert(data.name);
							},
							'json'
							);
					} else {
						alert(data.name);
					} 
				},
				'json'
				);
			
	
			return false;
		});
	}


	if ($('#rejectUserMovie').length > 0) {
		$('#rejectUserMovie').click(function() {
			$.post(
				"/account/review/userReject/as.json",
				{
					movieID : $("#usermovieID").val()
				},
				function(data) {
					alert(data.name);
				},
				'json'
				);
			return false;	
		});	
	}


	/*
	if ( $("#notif_Container").length > 0 ) {
		$.get(
			"/account/myVideo/totalEncodedVideo/as.json",
			{
				movieID : $("#usermovieID").val()
			},
			function(data) {
				if ( data != null && data.total > 0 ) {
					$("#notif_elem").text(data.total);
				} else {
					$("#notif_elem").remove();
				}
			},
				'json'
		);
	}
*/	
	if ( $('#profileForm1').length > 0 ) {
		$('#movieSave').click(function() {
			$("#profileForm1").validate({
				rules: {
					Title: {
						required: true
					},
					Description: {
						required: true
					}
				},
				messages: {
					Title: {
						required: "Enter a valid string"
					},
					Description: {
						required: "Enter a valid string"
					}
					
				}
			});
		});
	}
	
	if ( $("#eventUpload").length > 0 ) {

		$.get(
			'/account/upload/eventSources/as.xml',
			{
				EventID: getUrlVars()["EventID"]
			},
			function(data, textStatus) {
				var htmlOptions = '';
				$(data).find('source').each(function() {
					if( getUrlVars()["sourceID"] == $(this).find('id').text() ) { 
						htmlOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';
					}
				});
				$('#sourceUpload').html(htmlOptions);
			},
			'xml'
			);

		$('#eventUpload').change(function() {
			$.get(
				'/account/upload/eventSources/as.xml',
				{
					EventID: $(this).val()
				},
				function(data, textStatus) {
					var htmlOptions = '';
					$(data).find('source').each(function() {
						htmlOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';
					});
					$('#sourceUpload').html(htmlOptions);
				},
				'xml'
				);
		});
	}

	if ($("#FileUpload").length > 0) {
		$("input[type=file]").change(function() {
			alert("coming here");
			$("#uploadStatus").val("started");	
			
		});
	}	

	if ($("#uSearch").length > 0) {
		$('#uSearch').bind('keypress', function(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if( code == 13 ){
				window.location.href = "/user/search?userSearch="+$('#uSearch').val();
			}
		});

	}
		
	$( "#uSearch" ).autocomplete({
		source: '/user/users'
	});
	
	/*
	 * Add validation to profile
	 */
	if ( $('#grantsApplyForm').length > 0 ) {

		$('#grantsApplyForm').validate({
			   
			});
	}
	
	/*$('#ShowReelURL').blur(function() {
		
		var url = $('#ShowReelURL').val();
		if(/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(url) || url == 'N/A'){
		    return true;
		} else {
		    $('#ShowReelURL').focus();
		    alert("Show Reel URL field : Has a InValid or No URL. If 'Show Reel URL' is not applicable just put N/A");
		}
	});*/
	
	$('.userMovieGrantsSubmitButton').click(function(){
		var url = $('#ShowReelURL').val();
		if(/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(url) || url == 'N/A'){
		    return confirm('Are you sure you wish to make this change to the grant?');
		} else {
		    $('#ShowReelURL').focus();
		    alert("Showreel URL field : Has a InValid or No URL. If 'Showreel URL' is not applicable just put N/A");
		}
		return false;
	});

	/*	
    var i = $('#creditInput').size() + 1;
 
    $('#add').click(function() {
        $('<div><input type="text" class="field" name="dynamic[]" value="' + i + '" /></div>').fadeIn('slow').appendTo('.inputs');
        i++;
    });
 
    $('#remove').click(function() {
    if(i > 1) {
        $('.field:last').remove();
        i--;
    }
    });
 
    $('#reset').click(function() {
    while(i > 2) {
        $('.field:last').remove();
        i--;
    }
    });
*/

	$(function() {
		function split( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}
		
		$( "#mofilmMovieTag" )
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
			if ( event.keyCode === $.ui.keyCode.TAB &&
				$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				$.getJSON( "/account/upload/searchTag/as.json", {
					tag: extractLast( request.term )
				}, response );
			},
			search: function() {
				// custom minLength
				var term = extractLast( this.value );
				if ( term.length < 2 ) {
					return false;
				}
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push( "" );
				this.value = terms.join( ", " );
				return false;
			}
		});
	});
	
	function getUrlVars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}


	if ($("#ckeyword").length > 0) {
		$('#ckeyword').bind('keypress', function(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if( code == 13 ){
				window.location.href = "/user/skills/page/1/"+$('#ckeyword').val();
			}
		});

	}

	if ($(".round").length > 0) {

		$(".round").live("click",function(){
				$(".roundselected").removeClass("roundselected").addClass("round");
						
				$(this).removeClass("round");
				$(this).addClass("roundselected");		
			
		});
		
		$(".roundselected").live("click",function(){
			$(".roundselected").removeClass("roundselected").addClass("round");
		});
					
	}
	
	if ( $("#searchskill").length > 0 ) {
		$(document).keypress(function(e) {
		  if( e.which == 13 ) {
			var location = $("#location").val().trim();
		    var skill =  $(".roundselected").text().trim();
		    var url = "/user/skills/page/1/?";
		   if (location && skill ) {
				location = "(" +location.trim()+")";
				skill = skill.trim();
			    url =  url +"location="+location+"&skill="+skill;
		   } else if (location) {
				location = "(" +location.trim()+")";
				skill = skill.trim();
			    url =  url + "location="+location;			   
		   } else if ( skill) {
			    skill = skill.trim();
			    url = url + "skill="+skill;
		   }
		   
			window.location.href = url;
		 }
		});

	
		$("#searchskill").click(function(){
			
			var location = $("#location").val().trim();
		    var skill =  $(".roundselected").text().trim();
		    var url = "/user/skills/page/1/?";
		   if (location && skill ) {
				location = "(" +location.trim()+")";
				skill = skill.trim();
			    url =  url +"location="+location+"&skill="+skill;
		   } else if (location) {
				location = "(" +location.trim()+")";
				skill = skill.trim();
			    url =  url + "location="+location;			   
		   } else if ( skill) {
			    skill = skill.trim();
			    url = url + "skill="+skill;
		   }
		   
			window.location.href = url;
		   
		});
	}
	//document.getElementById("searchskill").addEventListener(type, listener, useCapture)

	String.prototype.trim=function(){
		return this.replace(/^\s+|\s+$/g, '');
	};

	$('.calculateAmount').keypress(function(event) {
	    if (event.which == 8 || event.keyCode == 9 || event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46) {
		return true;
	    }else if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	    }
	});
	
	$('.calculateAmount').change(function(event){
		var total=0;
		if (parseFloat($('#ScriptWriterAmount').val()) > 0) {
		    total+=parseFloat($('#ScriptWriterAmount').val());
		}
		if (parseFloat($('#ProducerAmount').val()) > 0) {
		    total+=parseFloat($('#ProducerAmount').val());
		}
		if (parseFloat($('#DirectorAmount').val()) > 0) {
		    total+=parseFloat($('#DirectorAmount').val());
		}
		if (parseFloat($('#TalentAmount').val()) > 0) {
		    total+=parseFloat($('#TalentAmount').val());
		}
		if (parseFloat($('#DoPAmount').val()) > 0) {
		    total+=parseFloat($('#DoPAmount').val());
		}
		if (parseFloat($('#EditorAmount').val()) > 0) {
		    total+=parseFloat($('#EditorAmount').val());
		}
		if (parseFloat($('#TalentExpensesAmount').val()) > 0) {
		    total+=parseFloat($('#TalentExpensesAmount').val());
		}
		if (parseFloat($('#ProductionStaffAmount').val()) > 0) {
		    total+=parseFloat($('#ProductionStaffAmount').val());
		}
		if (parseFloat($('#PropsAmount').val()) > 0) {
		    total+=parseFloat($('#PropsAmount').val());
		}
		if (parseFloat($('#SpecialEffectsAmount').val()) > 0) {
		    total+=parseFloat($('#SpecialEffectsAmount').val());
		}
		if (parseFloat($('#WardrobeAmount').val()) > 0) {
		    total+=parseFloat($('#WardrobeAmount').val());
		}
		if (parseFloat($('#HairMakeUpAmount').val()) > 0) {
		    total+=parseFloat($('#HairMakeUpAmount').val());
		}
		if (parseFloat($('#CameraRentalAmount').val()) > 0) {
		    total+=parseFloat($('#CameraRentalAmount').val());
		}
		if (parseFloat($('#SoundAmount').val()) > 0) {
		    total+=parseFloat($('#SoundAmount').val());
		}
		if (parseFloat($('#LightingAmount').val()) > 0) {
		    total+=parseFloat($('#LightingAmount').val());
		}
		if (parseFloat($('#TransportationAmount').val()) > 0) {
		    total+=parseFloat($('#TransportationAmount').val());
		}
		if (parseFloat($('#CrewExpensesAmount').val()) > 0) {
		    total+=parseFloat($('#CrewExpensesAmount').val());
		}
		if (parseFloat($('#LocationAmount').val()) > 0) {
		    total+=parseFloat($('#LocationAmount').val());
		}
		if (parseFloat($('#OthersAmount').val()) > 0) {
		    total+=parseFloat($('#OthersAmount').val());
		}
		
		$('#TotalGrantAmount').html(total.toFixed(2));
	});
	
	if ( $("#referralForm").length > 0 ) {

		$( "#referralForm" ).validate({
			rules: {
				field: {
					required: true
				},
				refer:{
					required: true
				}
			}
		});
	}

	if ( $("#myVideoSave").length > 0 ) {
		$("#myVideoSave").live("click", function() {
			var $checkBoxes = $(".industry");
			var checkCount = 0;

			$checkBoxes.each(function(){ if( this.checked ) checkCount++; });

			if( checkCount > 1 ) {
				$checkBoxes.removeAttr("checked");
				alert( 'Please select one Industry in Tags' );
				return false;
			} 
		});
	}

	if ( $(".multifile").length > 0 ) {

		$('.multifile').multifile();

	}
});

