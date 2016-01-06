{include file=$oView->getTemplateFile('header','/shared') pageTitle="{t}Business - Platform{/t}"}
{include file=$oView->getTemplateFile('menu','/shared')}

	<div id="body">
		<div class="container">
			<div class="pgHead">
				<h1 class="floatLeft">{t}Platform{/t}</h1>

				<div class="navigation floatRight">
					{include file=$oView->getTemplateFile('businessMenu', '/static')}
				</div>

				<div class="clearBoth"></div>
			</div>

			<div class="sidebar floatRight spacer">

			</div>

			<div class="main">
				<img src="{$themeimages}/business/business-platform-layers.gif" width="299" height="302" alt="Content Distribution" class="floatLeft spacer" />
				<p>
					MOFILM have gone to great lengths to provide a mobile platform that can showcase the constant
					stream of high quality short films that we receive as part of our global film festival, brand
					incentives, partners and individual filmmakers.
				</p>

				<h3>How to access the site</h3>
				<p>
					Simply type <a href="http://www.mofilm.com">www.mofilm.com</a> into your mobile browser. Our
					platform can determine the exact handset and optimises the delivery of MOFILM MOBILE based
					on your handset.
				</p>
				<p>
					If you have an iPhone, Android or Blackberry device you will be taken to the:- Applications
					Store, Android Market or the Blackberry Boutique.
				</p>

				<h3>Geographic targeting</h3>
				<p>
					MOFILM have gone one step further to personalising your experience. The MOFILM MOBILE
					platform can locate which country you are in and serve relevant promotional offers and
					advertising based on your location.
				</p>
				<p>
					As we develop, we aim to add localisation services to provide a MOFILM MOBILE service in
					multiple languages.
				</p>

				<h3>Java Icons</h3>
				<p>
					MOFILM provide Java Icons to most handsets, which act like the desktop icons and shortcuts
					that you have on your PC. These then link directly to our mobile Internet site.
				</p>
			</div>

			<div class="clearBoth"></div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer','/shared')}