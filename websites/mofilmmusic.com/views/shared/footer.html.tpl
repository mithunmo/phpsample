<div id="footerwrap">
	<div id="footer"><!--footer Frame Starts --> 
		<div id="footerframe">
			<div style="height:8px;"> </div>
			<div class="footer_block1">
				<div style="float:left;width:280px;height:100px;padding-left: 20px;">
					 
					<div style="color:#ff9933;width:180px; font-weight: bold;margin:50px auto 0 auto;font-size: 23px; text-align:center;"> <span style="font-size: 23px;color:#0066cc;"> {if $oModel} {$oModel->getTotalActiveObjects()} {/if}  <br/></span> MOMUSIC Tracks </div>	
				</div>
				
				
				<div style="float:left;width:240px;height:100px;padding-left:10px; margin-top:20px;"> 
					<div style="height:20px;font-size: 20px;font-weight: bold;color:#525252;"> For Brands</div>
					<div style="height:15px;"> </div>
					<div style="height:20px;font-size: 16px;">   
						<a style="color:#1a263f;" href="/music/reel">MOMUSIC Services</a> <br /> 
						<a style="color:#1a263f;" target="http://mofilm.com" href="#">MOFILM</a>  			
		     		</div>
					 </div>
				<div style="float:left;width:240px;height:100px;padding-left:10px; margin-top:20px;"> 
					<div style="height:20px;font-size: 20px;font-weight: bold;color:#525252;"> Our Music</div>
					<div style="height:15px;"> </div>
					<div style="height:20px;font-size: 16px;">   
						<a style="color:#1a263f;" href="/music/tips">Search Tips</a> <br /> 
						<a style="color:#1a263f;" target="_blank" href="/music/result">Latest Releases</a>  <br /> 
						<a style="color:#1a263f;" target="_blank" href="/music/submitMusic">Submit Music</a>  		
				</div>
				</div>
					<div style="float:left;width:220px;height:100px;padding-left:10px; margin-top:20px;"> 
				<div style="height:20px;font-size: 20px;font-weight: bold;color:#525252;"> About Us</div>
					<div style="height:15px;"> </div>
					<div style="height:20px;font-size: 16px;">   
						<a style="color:#1a263f;" href="/music/contact">Contact Us</a> <br /> 
						<a style="color:#1a263f;" target="_blank" href="/music/help">FAQs</a>  <br /> 							
                                                <a style="color:#1a263f;" target="_blank" href="/music/terms">T&Cs</a>  		
						
					</div>									
				</div>
				

			</div>
		</div>

	</div><!--footer Frame Ends --> 
</div><!-- Footer ###################### Ends -->

<script type="text/javascript" src="/libraries/core_js/core.js"></script>
<script type="text/javascript" src="/libraries/jquery/jquery.min.js"></script>
<script type="text/javascript" src="/libraries/jquery-validate/jquery-validate.min.js"></script>
<script type="text/javascript" src="/libraries/jquery-ui/jquery-ui.min.js"></script>		

{foreach $oView->getResourcesByType('js') as $oResource}
	{$oResource->toString()}
{/foreach}
<script type="text/javascript" src="/libraries/momusic/jquery.ba-hashchange.min.js"> </script>
<script type="text/javascript" src="/libraries/mofilm/lang/{$currentLanguage|default:'en'}.js?{mofilmConstants::JS_VERSION}"></script>
<script type="text/javascript" src="/libraries/mofilm/momusic.js?{mofilmConstants::JS_VERSION}"></script>
{if $isProduction}
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-4081693-12']);
  _gaq.push(['_setDomainName', 'momusic.com']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
{/if}	
</body>
</html>
