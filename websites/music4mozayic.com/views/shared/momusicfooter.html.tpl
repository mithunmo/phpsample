<!-- Footer ###################### Starts -->
<div id="footerwrap">
	<div id="footer"><!--footer Frame Starts --> 
		<div id="footerframe">
			<div style="height:8px;"> </div>
			<div class="footer_block1">
				<div style="float:left;width:400px;height:100px;padding-left: 20px;">
					<div style="height:25px;"> </div>
					<div style="color:#e85e02;font-weight: bold;padding-left: 80px;font-size: 18px;"> <span style="font-size: 20px;color:#009be3;"> 10000 </span> Tracks </div>
					
					<div style="height:20px;font-size: 10px;padding-top:10px;padding-left: 15px;">© MOMUSIC 2007-2013 All Rights Reserved  </div>
					
				</div>
<!--
                            <div style="float:left;width:190px;height:100px;border-left: 1px solid #114885;padding-left: 10px;"> 
					<div style="height:20px;font-size: 14px;font-weight: bold;color:#114885;padding-left: 20px;"> Our Clients </div>
					<div style="height:5px;"> </div>
					<div style="height:20px;font-size: 12px;color:#114885;padding-left: 40px;">   
						<a href="/music/reel">Show Reel</a> <br /> 
						<a target="_blank" href="http://www.mofilm.com/competitions/">Competitions</a> <br /> 
					</div>
					
					
				</div>
				<div style="float:left;width:190px;height:100px;border-left: 1px solid #114885;padding-left: 10px;"> 
					<div style="height:20px;font-size: 14px;font-weight: bold;color:#114885;padding-left: 20px;"> Our Music </div>
					<div style="height:5px;"> </div>
					<div style="height:20px;font-size: 12px;color:#114885;padding-left: 40px;"> 
						<a href="/music/result">Latest Releases</a> <br /> 
						<a href="/music/submitMusic">Submit New Music</a> <br /> 
						
					</div>

				</div>
-->             		<div style="float:left;width:250px;height:100px;border-left: 1px solid #114885;padding-left: 10px;"> 
					<div style="height:20px;font-size: 14px;font-weight: bold;color:#114885;padding-left: 20px;"> About us </div>
					<div style="height:5px;"> </div>
					<div style="height:20px;font-size: 12px;color:#114885;padding-left: 40px;"> 
                                                <a href="/music/contact">Contact us</a> <br /> 
						
					</div>									
				</div>
	
				<div style="float:left;width:250px;height:100px;border-left: 1px solid #114885;padding-left: 10px;"> 
					<div style="height:20px;font-size: 14px;font-weight: bold;color:#114885;padding-left: 20px;"> Find Your Music </div>
					<div style="height:5px;"> </div>
					<div style="height:20px;font-size: 12px;color:#114885;padding-left: 40px;"> 
						<a href="/music/tips">Search Tips</a><br /> 
						<a href="/music/terms">T & C</a> <br /> 
						<a href="/music/help">FAQs</a>
						
					</div>									
				</div>
				

			</div>
		</div>

	</div><!--footer Frame Ends --> 
</div><!-- Footer ###################### Ends -->
	<script src="http://cdn.jotfor.ms/min/g=feedback2" type="text/javascript">
	new JotformFeedback({
	formId:'30621187014949',
	base:'http://jotformpro.com/',
	windowTitle:'MOMUSIC Feedback Form',
	background:'#FFA500',
	fontColor:'#FFFFFF',
	type:false,
	height:500,
	width:700
	});
	</script>

<script type="text/javascript" src="/libraries/core_js/core.js"></script>
<script type="text/javascript" src="/libraries/jquery/jquery.min.js"></script>
<script type="text/javascript" src="/libraries/jquery-validate/jquery-validate.min.js"></script>
<script type="text/javascript" src="/libraries/jquery-ui/jquery-ui.min.js"></script>		

{foreach $oView->getResourcesByType('js') as $oResource}
	{$oResource->toString()}
{/foreach}
<script type="text/javascript" src="/libraries/momusic/jquery.ba-hashchange.min.js"> </script>
<script type="text/javascript" src="/libraries/momusic/jquery.nicescroll.min.js"> </script>
<script type="text/javascript" src="/libraries/mofilm/lang/{$currentLanguage|default:'en'}.js?{mofilmConstants::JS_VERSION}"></script>
<script type="text/javascript" src="/libraries/mofilm/momusic.js?{mofilmConstants::JS_VERSION}"></script>
</body>
</html>
