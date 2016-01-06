			<div class="clear"></div>
		</div>

		<div id="footerTop"></div>
		<div id="footer">
			<div id="ftlogo">
				<h3>Our Sponsors</h3>
				<a href="http://www.chevrolet.com/" target="_blank"><img src="{$themeimages}/ft-chev.png" /></a>
				<a href="http://www.mofilm.com/" target="_blank"><img src="{$themeimages}/ft-mofilm.png" /></a>
				<a href="http://www.pepsi.com" target="_blank"><img src="{$themeimages}/ft-pepsi.png" /></a>
				<a href="http://www.webtrends.com/" target="_blank"><img src="{$themeimages}/ft-webtrends.png" /></a>
			</div>

			<div id="coinfor">
				<p class="tag">MOFILM 2010 - {$smarty.now|date_format:'%Y'} &copy; All Rights Reserved &nbsp;&nbsp;&nbsp; No. 5.09 271 Regent Street &nbsp;&nbsp;&nbsp; London  &nbsp;&nbsp; W1B 2ES</p>

				<div class="footerelement">
					<ul>
						<li><a href="/">Southbyte</a></li>
						{*<li><a href="/gallery">Gallery</a></li>*}
						<li><a href="http://my.mofilm.com/account/register">Register</a></li>
					</ul>
				</div>

				<div class="footerelement">
					<ul>
						<li><a href="http://www.mofilm.com/" target="_blank">Filmmakers</a></li>
						<li><a href="/static/faq">FAQ</a></li>
						<li><a href="mailto:community@mofilm.com">Support</a></li>
					</ul>
				</div>

				<div class="footerelement">
					<a target="_blank" href="http://www.twitter.com/southbytes"><img src="{$themeimages}/facebook_icon.png" alt="facebook" /></a>
				</div>
				
				<div class="footerelement">
					<a href="http://www.twitter.com/southbytes" target="_blank"><img src="{$themeimages}/twitter.png" alt="twitter" /></a>
				</div>
			</div>

			<div class="clear"></div>
		</div>

{*		<script type="text/javascript" src="/libraries/core_js/core.js"></script>
		<script type="text/javascript" src="/libraries/jquery/jquery.min.js"></script>*}
{foreach $oView->getResourcesByType('js') as $oResource}
		{$oResource->toString()}
{/foreach}
{if $isProduction}
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-4081693-11']);
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