/**
 * Mofilm JS Resource
 *
 * @author Mithun Mohan
 * @copyright Mofilm Ltd
 * @package mofilm
 * @subpackage websites_mofilm.com_libraries
 * @version $Rev: 393 $
 */
jQuery(document).ready(function(){
	
	var messageBox = function(status, message) {
		$('body').append('<div class="messageBox '+status+'"><p>'+message+'</p></div>');
		$('body div.messageBox').delay(10000).slideUp(200);
	};
	
	var formChangedWarningBox = function() {
		if ( $('#formWarningBox').length == 0 ) {
			$('#body div.container').append('<div id="formWarningBox" class="messageBox warning"><p>'+mofilm.lang.messages.formContentsChanged+'</p></div>');
			$('#body div.container div.messageBox').delay(8500).slideUp(200);
		}
	};
	
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
	
	$.validator.addMethod("valueNotEquals", function(value, element, arg){
		return arg != value;
	}, "Value must not equal arg.");
	
	
	/*
	 * Add validation to register form
	 * validate only if mofilm username and password is being used, it will bypass if one is using facebook login
	 */
	
	if ( $('#registerForm').length > 0 ) {
		$('.signup').click(function() {
			$('#registerForm').validate({
				rules: {
					username: "required",
					password: "required",
					territory:  {
						valueNotEquals: 0
					}

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
					},
					territory: {
						valueNotEquals: "Please select an item!"
					}
					
				}
			});
		});
	}
	
	
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


	if ( $('.addAudio').length > 0 ) {
		$(".addAudio").live("click", function() {	
			//$('.addAudio').click(function () {
			//var duration = $(this).parent().next().next().next().text();
			//var url = $(this).parent().prev().find("a").attr("href");
			//var label = $(this).parent().next().next().text();
			$(this).html("<img src='/themes/mofilm/images/added.png' height=16px width=16px>");
			var duration = $(this).parent().prev().prev().text();
			var url = $(this).parent().next().next().next().next().next().next().find("a").attr("href");
			var label = $(this).parent().next().next().next().next().next().next().text();
			$.post(
				"/music/uploadAudio",
				{
					duration: duration,
					url: url,
					name: label
				},
				function(data) {
					if ( data == "false" ) {
						window.location = "/account/authorise?redirect=/music";
					} else {
						moviemasher().evaluate('browser.parameters.group=audio');
					}	
				}		    
				);

		});		

	}

	$('#nSearch.searchsync').click(function() {
		window.scrollTo(0, 1000);
	});

	$('#nSearch').click(function() {
		$("#off").val(0);
	});


	//$('#prevSearch').click(function() {
	$("#prevSearch").live("click", function() {		
		var val = parseInt($("#off").val()) - 20;
		$("#off").val(val);
	});

	//$('#nextSearch').click(function() {
	$("#nextSearch").live("click", function() {	
		var val = parseInt($("#off").val()) + 20;
		$("#off").val(val);
	});

	
	//	$('.searchGo').click(function() {
	$(".searchGo").live("click", function() {
		
		//alert("offset"+$("#off").val());
		$("#musicContent").empty();
		$("#musicContent").append("<div align='center'> <img align='middle' src='/themes/mofilm/images/loader.gif'></div>");
		$.get(
			'/music/searchXML/as.xml',
			{
				keyword: $("#search").val(),
				Offset : $("#off").val(),
				Limit : 20
			},
			function(data,textStatus,jqXHR) {
								
				var htmloptions = '';
				var i =0;
				var count = $(data).find('count').text();
				
				var textoffset = parseInt($("#off").val());
				var textend = textoffset + 20;
				var resultcount = parseInt($(data).find('resultCount').text());
				var textoffsetdis = textoffset+1;
				$("#totalb").text("Viewing "+textoffsetdis+"-"+resultcount+ " of "+count+ " results");
				//alert(count);
				//var offset = 0;
				var offset = parseInt($(data).find('offset').text());
				var limit = 20;
				
				$(data).find('source').each(function() {
					//htmlOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';
					
					if ( i % 2 == 0 ) {
						//htmloptions = htmloptions + ' <div class="bordersecondrow"> ';
						htmloptions = htmloptions + '<div style=" width:1000px; height:34px; vertical-align:middle; padding:1px; background-color:#b8b7b7">';
					} else {
						//htmloptions = htmloptions + ' <div class="bordersecondrow"> ';
						htmloptions = htmloptions + '<div style=" width:1000px; height:34px; vertical-align:middle; padding:1px; background-color:#b8b7b7">';
					}
						
					htmloptions = htmloptions + ' <div class="textproperties1"><a style="color:black;" href="/music/license/'+$(this).find('id').text()+'"><img src="/themes/mofilm/images/cart.gif"></a></div>';
					htmloptions = htmloptions + ' <div style="float:right; width:1px;">&nbsp;</div>';
					htmloptions = htmloptions + ' <div class="textproperties2">'+ $(this).find('duration').text()+'</div> ';
					htmloptions = htmloptions + ' <div style="float:right; width:1px;">&nbsp;</div>';
					htmloptions = htmloptions + ' <div class="textproperties3"><div class="addAudio"><img src="/themes/mofilm/images/add.png" width=16px height=16px /></div></div>';
					htmloptions = htmloptions + ' <div style="float:right; width:1px;">&nbsp;</div>';
					htmloptions = htmloptions + ' <div class="textproperties4">'+$(this).find('aname').text()+'</div>';
					htmloptions = htmloptions + ' <div style="float:right; width:1px;">&nbsp;</div>';
					htmloptions = htmloptions + ' <div class="textproperties6 tooltips"> <img src="/themes/mofilm/images/mm/info.png" width=16px height=16px /> <span class=".tooltip-style2">'+$(this).find('description').text()+'</span></div>';
					
					
					htmloptions = htmloptions + ' <div style="float:right; width:1px;">&nbsp;</div>';
					//htmloptions = htmloptions + ' <div class="textproperties5"><a style="color:black; margin-bottom:5px;" href="'+$(this).find('url').text()+ '" class="sm2_button"></a> '+$(this).find('name').text()+ '</p></div>';
					//htmloptions = htmloptions + ' <div class="textproperties5"> <object type="application/x-shockwave-flash" data="/libraries/dewplayer/dewplayer.swf?mp3='+$(this).find('url').text()+ '" width="100" height="20" id="dewplayer"><param name="wmode" value="transparent" /><param name="movie" value="dewplayer.swf?mp3='+$(this).find('url').text()+ '" /></object>'+$(this).find('name').text()+ '</div>';
					htmloptions = htmloptions + ' <div class="textproperties5"> <a class="musicPlay" href="'+$(this).find('url').text()+ '"><img style="padding-right:10px;" src="/themes/mofilm/images/mm/play.png" height="16px;"></a>'+$(this).find('name').text()+ '</div>';
					htmloptions = htmloptions + ' </div>';

					i++;
						
						
				});
				if ( offset-limit >= 0 ) {
					//$("#off").val(offset-limit);
					htmloptions = htmloptions + '<div style="width:50px;float:left;padding-left:350px;"><a id="prevSearch" class="searchGo" title="Previous"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a></div>';		
				} else {
					htmloptions = htmloptions + '<div style="width:50px;float:left;padding-left:350px;"><a class="searchGo" title="Previous"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a></div>';		
				}
				if ( resultcount == 20 ) {
					htmloptions = htmloptions + ' <span id="totalb"><strong>Viewing '+textoffsetdis+'-'+textend+' of '+count+' Results</strong></span>';
				} else {
					resultcount = textoffset+resultcount;
					htmloptions = htmloptions + ' <span id="totalb"><strong>Viewing '+textoffsetdis+'-'+resultcount+' of '+count+' Results</strong></span>';
				}
						
				if ( offset+limit < count ) {
					//var val = offset + limit;
					//$("#off").val(val);
					htmloptions = htmloptions + '<div style="width:50px;float:right;padding-right:350px;"> <a id="nextSearch" class="searchGo" title="Next"><img src="/themes/mofilm/images/icons/22x22/next.png"></a></div>';
				} else {
					htmloptions = htmloptions + '<div style="width:50px;float:right;padding-right:350px;"> <a  class="searchGo" title="Next"><img src="/themes/mofilm/images/icons/22x22/next.png"></a></div>';
				}
				htmloptions = htmloptions + ' <div style="clear:both;"> </div> ';	
				$('#musicContent').html(htmloptions);
			},'xml');
		return false;	
	});


	$('#clear').click(function() {
		$( '<div id="dialog" title="Basic dialog">This clears all the uploaded video and selected audio.</div>' ).dialog({
			title: "Confirmation",
			modal: true,
			buttons: {
				"Clears all items": function() {
							
					$.post(
						"/music/clear",
						{
						},
						function(data) {
						//$('#body div.container').append('<div id="messageBox" class="messageBox success"><p><strong>'+"Workspace Cleared"+'</strong></p></div>');
						//$('#body div.container div.messageBox').delay(8500).slideUp(200);
						}		    
						);
					moviemasher().evaluate('browser.parameters.group=video');
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}						
		});
			

	});


	$('#uploadlogin').click(function() {
		window.location.href="/account/login?redirect=/music";
	});
		
	if ( $("#mashMusic").length > 0 ) {
		$("#mashload").append("<div style='padding-top:10px;padding-bottom-5px;width:200px;height:40px;display: block;' align='left'><strong>Loading...</strong> <img align='middle' style='padding-left:10px;position:absolute;'src='/themes/mofilm/images/mm/mashloader.gif'></div>");
		setTimeout(function(){
			moviemasher().evaluate($("#expression").text());
			$("#mashload").empty();
		},10000)
			
	}
	
	if ( $('#jquery_jplayer_1').length > 0 ) {

		$("#jquery_jplayer_1").jPlayer({
			ready: function (event) {
			
				$(this).jPlayer("setMedia", {
					mp3:"http://ftmedia.s3.amazonaws.com/chriswinham/fliktraxinhouse/traces.mp3"
				});
		
			},
			pause: function (event) {
				$(".musicPause").html("<img style='padding-right:10px' src='/themes/mofilm/images/mm/rpause.png'>");
				$(".musicPause").attr("class","musicRenew");
				
			},
			play: function (event) {
				$(".musicRenew").html("<img style='padding-right:10px' src='/themes/mofilm/images/mm/pause.png'>");
				$(".musicRenew").attr("class","musicPause");				
			},
			solution: "flash,html",
			swfPath: "/libraries/jplayer/js/Jplayer.swf",
			supplied: "mp3",
			wmode: "window"
		});
	
	
		//$('.musicPlay').click( function() {
		$(".musicPlay").live("click", function() {
			var play = $(this);
			$("#jquery_jplayer_1").jPlayer("clearMedia");
		
		
			$.post(
				'/music/musicURL/as.json',
				{
					id: $(this).attr("href")
				},
				function(data, textStatus, XMLHttpRequest){
					
					
					$("#jquery_jplayer_1").jPlayer("setMedia", {
						mp3: data.murl
					});
		
					$("#jquery_jplayer_1").jPlayer("load");
		
					$("#jquery_jplayer_1").jPlayer("play");
					
					$(".musicPlay").html("<img style='padding-right:10px;' src='/themes/mofilm/images/mm/play.png' height=16px;>");
					$(".musicPause").html("<img style='padding-right:10px;' src='/themes/mofilm/images/mm/play.png' height=16px;>");
					$(".musicRenew").html("<img style='padding-right:10px;' src='/themes/mofilm/images/mm/play.png' height=16px;>");
					$(".musicPause").attr("class","musicPlay");
					$(".musicRenew").attr("class","musicPlay");

					play.html("<img style='padding-right:10px' src='/themes/mofilm/images/mm/pause.png'>");
					play.attr("class","musicPause");

					
				},
				"json"
				);
		
			return false;
			
		});
		
		
		$(".musicPause").live("click", function() {	
			$("#jquery_jplayer_1").jPlayer("pause");
			$(this).html("<img style='padding-right:10px' src='/themes/mofilm/images/mm/rpause.png'>");
			$(this).attr("class","musicRenew");
		
			return false;
		});


		$(".musicRenew").live("click", function() {	
			$("#jquery_jplayer_1").jPlayer("play");
			$(this).html("<img style='padding-right:10px' src='/themes/mofilm/images/mm/pause.png'>");
			$(this).attr("class","musicPause");
		
			return false;
		});


		
	}
	
	
	$(".tooltips").live({
		mouseenter:
		function()
		{
			$(this).contents("span:last-child").css({
				display: "block"
			});
		},
		mouseleave:
		function()
		{
			$(this).contents("span:last-child").css({
				display: "none"
			});
			   
		},
		mousemove:
		function(e) 
		{
			var mousex = e.pageX + 10;
			var mousey = e.pageY + 5;
			$(this).contents("span:last-child").css({
				top: mousey, 
				left: mousex
			});
			
		}
	}
	);
		


	$("#prevSearch1").live("click", function() {		
		var val = parseInt($("#off").val()) - 20;
		$("#off").val(val);
	});

	//$('#nextSearch').click(function() {
	$("#nextSearch1").live("click", function() {
		var val = parseInt($("#off").val()) + 20;
		$("#off").val(val);
	});


		
	$(".searchGo1").live("click", function() {
		
		//alert("offset"+$("#off").val());
		$("#musicContent").empty();
		$("#musicContent").append("<div align='center'> <img align='middle' src='/themes/mofilm/images/loader.gif'></div>");
				
		$.get(
			'/music/solrSearch/as.xml',
			{
				keyword: $("#search").val(),
				genre: getUrlVars()["genre"],
				mood: getUrlVars()["mood"],
				Offset : $("#off").val(),
				Limit : 10
			},
			function(data,textStatus,jqXHR) {
				var htmloptions = '';
				var i = 0;
				var count = $(data).find('count').text();
				
				var textoffset = parseInt($("#off").val());
				var textend = textoffset + 10;
				//var resultcount = parseInt($(data).find('resultCount').text());
				var resultcount = 10;
				
				var textoffsetdis = textoffset+1;
				$("#totalb").text("Viewing "+textoffsetdis+"-"+resultcount+ " of "+count+ " results");
				var offset = parseInt($(data).find('offset').text());
				var limit = 10;
				//alert(offset);
				//alert("lim"+limit);
				
				$(data).find('source').each(function() {
					
					if ( i % 2 == 0 ) {
						htmloptions = htmloptions + '<div style=" width:1000px; height:34px; vertical-align:middle; padding:1px; background-color:#b8b7b7">';
					} else {
						htmloptions = htmloptions + '<div style=" width:1000px; height:34px; vertical-align:middle; padding:1px; background-color:#b8b7b7">';
					}
						
					htmloptions = htmloptions + ' <div class="textproperties1"><a style="color:black;" href="/music/license/'+$(this).find('id').text()+'"><img src="/themes/mofilm/images/cart.gif"></a></div>';
					htmloptions = htmloptions + ' <div style="float:right; width:1px;">&nbsp;</div>';
					htmloptions = htmloptions + ' <div class="textproperties2">'+ $(this).find('duration').text()+'</div> ';
					htmloptions = htmloptions + ' <div style="float:right; width:1px;">&nbsp;</div>';
					htmloptions = htmloptions + ' <div class="textproperties3"><div class="addAudio"><img src="/themes/mofilm/images/add.png" width=16px height=16px /></div></div>';
					htmloptions = htmloptions + ' <div style="float:right; width:1px;">&nbsp;</div>';
					htmloptions = htmloptions + ' <div class="textproperties4">'+$(this).find('aname').text()+'</div>';
					htmloptions = htmloptions + ' <div style="float:right; width:1px;">&nbsp;</div>';
					htmloptions = htmloptions + ' <div class="textproperties6 tooltips"> <img src="/themes/mofilm/images/mm/info.png" width=16px height=16px /> <span class=".tooltip-style2">'+$(this).find('description').text()+'</span></div>';
					
					
					htmloptions = htmloptions + ' <div style="float:right; width:1px;">&nbsp;</div>';
					htmloptions = htmloptions + ' <div class="textproperties5"> <a class="musicPlay" href="'+$(this).find('url').text()+ '"><img style="padding-right:10px;" src="/themes/mofilm/images/mm/play.png" height="16px;"></a>'+$(this).find('name').text()+ '</div>';
					htmloptions = htmloptions + ' </div>';

					i++;
						
						
				});
				
				
				if ( offset-limit >= 0 ) {
					htmloptions = htmloptions + '<div style="width:50px;float:left;padding-left:350px;"><a id="prevSearch1" class="searchGo1" title="Previous"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a></div>';		
				} else {
					htmloptions = htmloptions + '<div style="width:50px;float:left;padding-left:350px;"><a class="searchGo1" title="Previous"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a></div>';		
				}
				if ( resultcount == 10 ) {
					htmloptions = htmloptions + ' <span id="totalb"><strong>Viewing '+textoffsetdis+'-'+textend+' of '+count+' Results</strong></span>';
				} else {
					resultcount = textoffset+resultcount;
					htmloptions = htmloptions + ' <span id="totalb"><strong>Viewing '+textoffsetdis+'-'+resultcount+' of '+count+' Results</strong></span>';
				}
						
				if ( offset+limit < count ) {
					htmloptions = htmloptions + '<div style="width:50px;float:right;padding-right:350px;"> <a id="nextSearch1" class="searchGo1" title="Next"><img src="/themes/mofilm/images/icons/22x22/next.png"></a></div>';
				} else {
					htmloptions = htmloptions + '<div style="width:50px;float:right;padding-right:350px;"> <a  class="searchGo1" title="Next"><img src="/themes/mofilm/images/icons/22x22/next.png"></a></div>';
				}
				htmloptions = htmloptions + ' <div style="clear:both;"> </div> ';	
				$('#musicContent').html(htmloptions);
			},'xml');
		return false;	
	});
	
	

	/*  ======  */


	$(".searchmomusic").live("click", function() {
		
		$("#musicContent").empty();
		$("#musicContent").append("<div align='center'> <img align='middle' src='/themes/mofilm/images/loader.gif'></div>");
		
		var key;
		if ( $("#search").val() ) {
			var key = $("#search").val();
		} else {
			var key = getUrlVars()["keyword"];
		}
		
		var hashfrag;
				
		if (getUrlVars()["keyword"]) {
			//window.location.hash = "keyword="+key+"&Offset="+$("#off").val()+"&Limit="+10;
			hashfrag = "keyword="+key;
		}
		
		
		if (getUrlVars()["genre"]) {
			if ( getUrlVars()["genre"].indexOf("#") > 0  ) {
				var gen = getUrlVars()["genre"].substring(0,getUrlVars()["genre"].indexOf('#') );
			} else {
				var gen = getUrlVars()["genre"];
			}
			hashfrag = "genre="+gen;
		}


		if (getUrlVars()["style"]) {
			if ( getUrlVars()["style"].indexOf("#") > 0  ) {				
				var gen = getUrlVars()["style"].substring(0,getUrlVars()["style"].indexOf('#') );
			} else {
				var gen = getUrlVars()["style"];
			}
			hashfrag = "style="+gen;
		}

		
		if (getUrlVars()["instrument"]) {
			if ( getUrlVars()["instrument"].indexOf("#") > 0  ) {
				var gen = getUrlVars()["instrument"].substring(0,getUrlVars()["instrument"].indexOf('#') );
			} else {
				var gen = getUrlVars()["instrument"];
			}
			hashfrag = "instrument="+gen;
		}

		if (getUrlVars()["mood"]) {
			if ( getUrlVars()["mood"].indexOf("#") > 0  ) {
				var gen = getUrlVars()["mood"].substring(0,getUrlVars()["mood"].indexOf('#') );
			} else {
				var gen = getUrlVars()["mood"];
			}
			hashfrag = "mood="+gen;
		}
		
		if (getUrlVars()["artist"]) {
			if ( getUrlVars()["artist"].indexOf("#") > 0  ) {
				var gen = getUrlVars()["artist"].substring(0,getUrlVars()["artist"].indexOf('#') );
			} else {
				var gen = getUrlVars()["artist"];
			}
			//window.location.hash = "artist="+gen+"&Offset="+$("#off").val()+"&Limit="+10;
			hashfrag = "artist="+gen;
		}
		
		if (getUrlVars()["tempo"]) {
			if ( getUrlVars()["tempo"].indexOf("#") > 0  ) {
				var gen = getUrlVars()["tempo"].substring(0,getUrlVars()["tempo"].indexOf('#') );
			} else {
				var gen = getUrlVars()["tempo"];
			}
			//window.location.hash = "tempo="+gen+"&Offset="+$("#off").val()+"&Limit="+10;
			hashfrag = "tempo="+gen;
		}


		if (getUrlVars()["filterq"] ) {
			if ( getUrlVars()["filterq"].indexOf("#") > 0  ) {
				var gen = getUrlVars()["filterq"].substring(0,getUrlVars()["filterq"].indexOf('#') );
			} else {
				var gen = getUrlVars()["filterq"];
			}
			
			var frag = "&filterq="+gen
			
			
			if ( getUrlVars()["category"].indexOf("#") > 0  ) {
				var gen = getUrlVars()["category"].substring(0,getUrlVars()["category"].indexOf('#') );
			} else {
				var gen = getUrlVars()["category"];
			}
			var frag = frag + frag+"&category="+gen;
		//window.location.hash = frag+"&category="+gen+"&Offset="+$("#off").val()+"&Limit="+10;
			
		}
		
		if ( frag != undefined ) {
			hashfrag = hashfrag + frag + "&Offset="+$("#off").val()+"&Limit="+20;
		} else {
			hashfrag = hashfrag + "&Offset="+$("#off").val()+"&Limit="+20;
		}
		window.location.hash = hashfrag;
		if (window.location.hash != "") {
			return false;
		}
		
	/*		
		$.get(
			'/music/solrSearch/as.xml',
			{
				keyword: key,
				genre: getUrlVars()["genre"],
				artist: getUrlVars()["artist"],
				mood: getUrlVars()["mood"],
				style: getUrlVars()["style"],
				tempo: getUrlVars()["tempo"],
				filterq: getUrlVars()["filterq"],
				category: getUrlVars()["category"],				
				instrument: getUrlVars()["instrument"],
				Offset : $("#off").val(),
				Limit : 10
			},
			function(data,textStatus,jqXHR) {
				var htmloptions = '';
				var htmloptionsnav = '';
				var i = 0;
				var count = $(data).find('count').text();
				
				var textoffset = parseInt($("#off").val());
				var textend = textoffset + 10;
				//var resultcount = parseInt($(data).find('resultCount').text());
				var resultcount = 10;
				
				var textoffsetdis = textoffset+1;
				$("#totalb").text("Viewing "+textoffsetdis+"-"+resultcount+ " of "+count+ " results");
				var offset = parseInt($(data).find('offset').text());
				var limit = 10;
				//alert(offset);
				//alert("lim"+limit);
				
				htmloptions = htmloptions + '<table width="740" height="50" border="0" align="center" cellpadding="0" cellspacing="3">';
			
				$(data).find('source').each(function() {
					
					htmloptions = htmloptions + '<tr> ';
					htmloptions = htmloptions +	'<td width="20px"><a class="musicPlay" href="'+$(this).find('url').text()+ '"><img style="padding-right:10px;" src="/themes/mofilm/images/mm/play.png" height="16px;"></a> </td>';
					htmloptions = htmloptions + '<td width="150px">'+$(this).find('name').text()+ '<br /><a href="/music/solrSearch?artist='+$(this).find('aname').text()+'"><strong> '+$(this).find('aname').text()+'</strong></a> </td>';										
					htmloptions = htmloptions + '<td width="420px">'+$(this).find('description').text()+ '</td>';
					htmloptions = htmloptions + '<td style="text-align:right;" width="40px">'+$(this).find('duration').text()+ '</td>';
					htmloptions = htmloptions + '<td width="40px" align="center" valign="middle"><a class="cart" style="color:black;" href="'+$(this).find('id').text()+'"><img src="/themes/mofilm/images/cart.gif"></a></td>';
					htmloptions = htmloptions + '</tr> ';
					htmloptions = htmloptions + '<tr> ';
					htmloptions = htmloptions + '<td height="1px"  style="background-color:#fff"colspan="5"></td>';
					htmloptions = htmloptions + '</tr> ';
			
					i++;
						
						
				});
				
				$("#navlink").empty();
								
				if ( offset-limit >= 0 ) {
					htmloptionsnav = htmloptionsnav + '<div style="float:left;width:300px;""><a style="cursor: pointer;" id="prevSearch1" class="searchmomusic" title="Previous"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a></div>';
				} else {
					htmloptionsnav = htmloptionsnav + '<div style="float:left;width:300px;"><a style="cursor: pointer;" title="Previous"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a></div>';		
				}
				
				if ( offset+limit >  count ) {
					htmloptionsnav = htmloptionsnav + ' <span id="totalb">Viewing '+textoffsetdis+'-'+count+' of '+count+' Results</span>';
				} else {
					resultcount = textoffset+resultcount;
					htmloptionsnav = htmloptionsnav + ' <span id="totalb">Viewing '+textoffsetdis+'-'+resultcount+' of '+count+' Results</span>';
				}
						
				if ( offset+limit < count ) {
					htmloptionsnav = htmloptionsnav + '<div style="float:right;width:250px;padding-right:5px;text-align:right;"> <a style="cursor: pointer;" id="nextSearch1" class="searchmomusic" title="Next"><img src="/themes/mofilm/images/icons/22x22/next.png"></a></div>';
				} else {
					htmloptionsnav = htmloptionsnav + '<div style="float:right;width:250px;padding-right:5px;text-align:right;"> <a  style="cursor: pointer;" title="Next"><img src="/themes/mofilm/images/icons/22x22/next.png"></a></div>';
				}
				
				//htmloptions = htmloptions + ' <div style="clear:both;"> </div> ';	
				$('#musicContent').html(htmloptions);
				$('#navlink').html(htmloptionsnav);
			},'xml');
		return false;	
*/		
	});
	



	/*  ====   */
	
	
	$(".searchsync").live("click", function() {
		
		$("#musicContent").empty();
		$("#musicContent").append("<div align='center'> <img align='middle' src='/themes/mofilm/images/loader.gif'></div>");
		
		var key;
		if ( $("#search").val() ) {
			var key = $("#search").val();
		} else {
			var key = getUrlVars()["keyword"];
		}


		$.get(
			'/music/solrSearch/as.xml',
			{
				keyword: key,
				genre: getUrlVars()["genre"],
				mood: getUrlVars()["mood"],
				Offset : $("#off").val(),
				Limit : 20
			},
			function(data,textStatus,jqXHR) {
				var htmloptions = '';
				var htmloptionsnav = '';
				var i = 0;
				var count = $(data).find('count').text();
				
				var textoffset = parseInt($("#off").val());
				var textend = textoffset + 20;
				//var resultcount = parseInt($(data).find('resultCount').text());
				var resultcount = 20;
				
				var textoffsetdis = textoffset+1;
				$("#totalb").text("Viewing "+textoffsetdis+"-"+resultcount+ " of "+count+ " results");
				var offset = parseInt($(data).find('offset').text());
				var limit = 20;
				//alert(offset);
				//alert("lim"+limit);
				
				htmloptions = htmloptions + '<table width="1030px;" height="50" border="0" align="center" cellpadding="0" cellspacing="3">';
			
				$(data).find('source').each(function() {
					
					htmloptions = htmloptions + '<tr> ';
					htmloptions = htmloptions +	' <td width="20px"><a rel="nofollow" class="musicPlay" href="'+$(this).find('id').text()+ '"><img style="padding-right:10px;" src="/themes/mofilm/images/mm/play.png" height="16px;"></a> </td>';
					htmloptions = htmloptions + '<td width="150px">'+$(this).find('name').text()+ '<br />'+$(this).find('aname').text()+'</td>';										
					htmloptions = htmloptions + '<td width="420px">'+$(this).find('description').text()+ '</td>';
					htmloptions = htmloptions + '<td style="text-align:right;" width="40px">'+$(this).find('duration').text()+ '</td>';
					//htmloptions = htmloptions + '<td class="addAudio" style="text-align:right;" width="20px"><img src="/themes/mofilm/images/add.png" height=16px width=16px /></td>';
					//htmloptions = htmloptions + '<td width="40px" align="center" valign="middle"><a style="color:black;" href="'+$(this).find('id').text()+'"><img src="/themes/mofilm/images/cart.gif"></a></td>';
					htmloptions = htmloptions + '<td width="40px" align="center" valign="middle"><a class="cart" style="color:black;" href="'+$(this).find('id').text()+'"><img src="/themes/mofilm/images/cart.gif"></a></td>';
					htmloptions = htmloptions + '</tr> ';
					htmloptions = htmloptions + '<tr> ';
					htmloptions = htmloptions + '<td height="1px"  style="background-color:#fff"colspan="5"></td>';
					htmloptions = htmloptions + '</tr> ';
			
					i++;
						
						
				});
				
				$("#navlink").empty();
				if ( count > 0 ){
					if ( offset-limit >= 0 ) {
						htmloptionsnav = htmloptionsnav + '<div style="float:left;width:450px; "><a style="cursor: pointer;" id="prevSearch1" class="searchsync" title="Previous"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a></div>';
					} else {
						htmloptionsnav = htmloptionsnav + '<div style="float:left;width:450px; "><a style="cursor: pointer;" title="Previous"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a></div>';		
					}
					if ( offset+limit >  count ) {
						htmloptionsnav = htmloptionsnav + ' <span id="totalb" style="width:100%;align:center;">Viewing '+textoffsetdis+'-'+count+' of '+count+' Results</span>';
					} else {
						resultcount = textoffset+resultcount;
						htmloptionsnav = htmloptionsnav + '<span id="totalb" style="width:100%;align:center;">Viewing '+textoffsetdis+'-'+resultcount+' of '+count+' Results</span>';
					}

					if ( offset+limit < count ) {
						htmloptionsnav = htmloptionsnav + '<div style="float:right;width:250px;padding-right:5px;text-align:right;"><a style="cursor: pointer;" id="nextSearch1" class="searchsync" title="Next"><img src="/themes/mofilm/images/icons/22x22/next.png"></a></div>';
					} else {
						htmloptionsnav = htmloptionsnav + '<div style="float:right;width:250px;padding-right:5px;text-align:right;"><a style="cursor: pointer;" title="Next"><img src="/themes/mofilm/images/icons/22x22/next.png"></a></div>';
					}
				}
				//htmloptions = htmloptions + ' <div style="clear:both;"> </div> ';	
				$('#musicContent').html(htmloptions);
				$('#navlink').html(htmloptionsnav);
				window.scrollTo(400,1000 );			
			},'xml');
		return false;	
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

	function getHashUrlVars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('#') + 1).split('&');
		for(var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}


	if ( $('.syncAudio').length > 0 ) {
		$(".syncAudio").live("click", function() {	
			$(this).html("<img src='/themes/mofilm/images/added.png' height=16px width=16px>");
			
			var duration = $(this).prev().text();
			var url = $(this).prev().prev().prev().prev().find("a").attr("href");
			var label = $(this).prev().prev().prev().text();
			$.post(
				"/music/uploadAudio",
				{
					duration: duration,
					url: url,
					name: label
				},
				function(data) {
					if ( data == "false" ) {
						window.location = "/account/authorise?redirect=/music";
					} else {
						moviemasher().evaluate('browser.parameters.group=audio');
					}	
				}		    
				);

		});		

	}

	$.get(
		"/cart/add"+"/as.xml",
		null,			
		function(data) {
			console.log(data);
			var count = $(data).find('count').text();
			$(".noti_bubble").text(count);
					
		},
		'xml'		    
		);


	if ( $('.cart').length > 0 ) {

		$(".cart").live("click", function() {			
			var ID = $(this).parent().find("a").attr("href");
			$.get(
				"/cart/add"+"/as.xml",
				{
					id:ID
				},			
				function(data) {
					var login = $(data).find('login').text();
					if ( login == "false" ) {
						window.location = "/account/authorise?redirect="+encodeURIComponent(window.location.href);
					} else {
						var count = $(data).find('count').text();
						$(".noti_bubble").text(count);
					}	
				},
				'xml'		    
				);
					
					
			var duration = $(this).parent().prev().text();
			
			var id = $(this).parent().prev().prev().prev().prev().find("a").attr("href");
			
			var label = $(this).parent().prev().prev().prev().text();
			
			$.post(
				"/music/uploadAudio",
				{
					duration: duration,
					id: id,
					name: label
				},
				function(data) {
					if ( data == "false" ) {
						window.location = "/account/authorise?redirect="+encodeURIComponent(window.location.href);
					} else {
						moviemasher().evaluate('browser.parameters.group=audio');
						window.scrollTo(0, 0);
					}	
				}		    
				);	
			
			return false;		
		});		

	}

	$(window).hashchange( function(e) {
            
		if ( location.hash != "" ) {
			load_content();
		} else {
			window.location.href = location.href;
		}
	

	});

			$('#artist_slider').everslider({
				mode: 'carousel',
				moveSlides: 1,
				maxSlides:6,
				slideEasing: 'easeInOutCubic',
				slideDuration: 1000,
				navigation: false,
				keyboard: true,
				ticker: true,
				tickerAutoStart: true,
				tickerHover: false,
				tickerTimeout: 2000,
				itemWidth:140,
				itemHeight:198,
				maxWidth: '95%',                   // max container width, px or %  
 				 itemMargin:15
			});
 var es1 = $('#artist_slider').data('everslider');
 $('.bx-next1').click(function(){ es1.slideNext(); });
 $('.bx-prev1').click(function(){ es1.slidePrevious(); });
                        
			$('#logo_slider').everslider({
				mode: 'carousel',
				moveSlides: 1,
				slideEasing: 'easeInOutCubic',
				slideDuration: 1000,
				navigation: false,
				keyboard: true,
				ticker: true,
				tickerAutoStart: true,
				tickerHover: true,
				tickerTimeout: 2000,
				itemWidth:139,
				itemHeight:139,
				maxWidth: '98%',                   // max container width, px or %  
 				itemMargin:15
			});
 var es = $('#logo_slider').data('everslider');
 $('.bx-next2').click(function(){ es.slideNext(); });
 $('.bx-prev2').click(function(){ es.slidePrevious(); });
                        

	function load_content() {
		
		var hashurl = location.href.split('#');
		$("#musicContent").empty();
		$("#musicContent").append("<div align='center'> <img align='middle' src='/themes/mofilm/images/loader.gif'></div>");
		
		var key;
		if ( $("#search").val() ) {
			var key = $("#search").val();
		} else {
			var key = getHashUrlVars()["keyword"];
		}
		
		$.get(
			'/music/solrSearch/as.xml',
			{
				keyword: key,
				genre: getHashUrlVars()["genre"],
				artist: getHashUrlVars()["artist"],
				mood: getHashUrlVars()["mood"],
				style: getHashUrlVars()["style"],
				tempo: getHashUrlVars()["tempo"],
				filterq: getHashUrlVars()["filterq"],
				category: getHashUrlVars()["category"],								
				instrument: getHashUrlVars()["instrument"],
				Offset : getHashUrlVars()["Offset"],
				Limit : 20,
				Hash : hashurl[1]
			},
			function(data,textStatus,jqXHR) {
				var htmloptions = '';
				var htmloptionsnav = '';
				var i = 0;
				var count = $(data).find('count').text();
				
				var textoffset = parseInt(getHashUrlVars()["Offset"]);
				var textend = textoffset + 20;
				//var resultcount = parseInt($(data).find('resultCount').text());
				var resultcount = 20;
				
				var textoffsetdis = textoffset+1;
				$("#totalb").text("Viewing "+textoffsetdis+"-"+resultcount+ " of "+count+ " results");
				var offset = parseInt(getHashUrlVars()["Offset"]);
				var limit = 20;
				//alert(offset);
				//alert("lim"+limit);
				
				htmloptions = htmloptions + '<table width="740" height="50" border="0" align="center" cellpadding="0" cellspacing="3">';
			
				$(data).find('source').each(function() {
					
					htmloptions = htmloptions + '<tr> ';
					htmloptions = htmloptions +	'<td width="20px"><a rel="nofollow" class="musicPlay" href="'+$(this).find('id').text()+ '"><img style="padding-right:10px;" src="/themes/mofilm/images/mm/play.png" height="16px;"></a> </td>';
					htmloptions = htmloptions + '<td width="150px">'+$(this).find('name').text()+ '<br /><a href="/music/solrSearch?artist='+$(this).find('aname').text()+'"><strong> '+$(this).find('aname').text()+'</strong></a> </td>';										
                                        htmloptions = htmloptions + '<td width="420px">'+$(this).find('description').text()+ '</td>';
					
                                        
					htmloptions = htmloptions + '<td style="text-align:right;" width="40px">'+$(this).find('duration').text()+ '</td>';
					htmloptions = htmloptions + '<td width="40px" align="center" valign="middle"><a class="cart" style="color:black;" href="'+$(this).find('id').text()+'"><img src="/themes/mofilm/images/cart.gif"></a></td>';
					htmloptions = htmloptions + '</tr> ';
					htmloptions = htmloptions + '<tr> ';
					htmloptions = htmloptions + '<td height="1px"  style="background-color:#fff"colspan="5"></td>';
					htmloptions = htmloptions + '</tr> ';
			
					i++;
						
						
				});
				
				$("#navlink").empty();
								
				if ( offset-limit >= 0 ) {
					htmloptionsnav = htmloptionsnav + '<div style="float:left;width:300px;""><a style="cursor: pointer;" id="prevSearch1" class="searchmomusic" title="Previous"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a></div>';
				} else {
					htmloptionsnav = htmloptionsnav + '<div style="float:left;width:300px;"><a style="cursor: pointer;" title="Previous"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a></div>';		
				}
				
				if ( offset+limit >  count ) {
					htmloptionsnav = htmloptionsnav + ' <span id="totalb">Viewing '+textoffsetdis+'-'+count+' of '+count+' Results</span>';
				} else {
					resultcount = textoffset+resultcount;
					htmloptionsnav = htmloptionsnav + ' <span id="totalb">Viewing '+textoffsetdis+'-'+resultcount+' of '+count+' Results</span>';
				}
						
				if ( offset+limit < count ) {
					htmloptionsnav = htmloptionsnav + '<div style="float:right;width:250px;padding-right:5px;text-align:right;"> <a style="cursor: pointer;" id="nextSearch1" class="searchmomusic" title="Next"><img src="/themes/mofilm/images/icons/22x22/next.png"></a></div>';
				} else {
					htmloptionsnav = htmloptionsnav + '<div style="float:right;width:250px;padding-right:5px;text-align:right;"> <a  style="cursor: pointer;" title="Next"><img src="/themes/mofilm/images/icons/22x22/next.png"></a></div>';
				}
				
				//htmloptions = htmloptions + ' <div style="clear:both;"> </div> ';	
				$('#musicContent').html(htmloptions);
				$('#navlink').html(htmloptionsnav);
			},'xml');





	}

	$(".thisdiv").niceScroll();
        
        
                        var flag = true;
        		$(".momusicsong").live("click", function() {	                            
                            var es1 = $('#artist_slider').data('everslider');                            
                                es1.tickerPause();
                                flag= false;
                        });
                        
        		$(".sm2_playing").live("click", function() {	                            
                            var es1 = $('#artist_slider').data('everslider');                            
                                es1.tickerPlay();
                                flag= true;
                        });
                        
        
        
				});
