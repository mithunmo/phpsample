{include file=$oView->getTemplateFile('header','/shared') pageTitle="{t}Business - Introduction{/t}"}
{include file=$oView->getTemplateFile('menu','/shared')}

	<div id="body">
		<div class="container">
			<div class="pgHead">
				<h1 class="floatLeft">{t}Welcome to MOFILM{/t}</h1>

				<div class="navigation floatRight">
					{include file=$oView->getTemplateFile('businessMenu', '/static')}
				</div>

				<div class="clearBoth"></div>
			</div>

			<div class="sidebar floatRight spacer">

			</div>

			<div class="main">
				<p>
					MOFILM is a pioneer in mobile entertainment, representing a global community of filmmakers
					and content, giving the best experience on mobile. MOFILMs available include content by
					Oscar winners as well as ordinary filmmakers from MOFILM competitions and festivals around
					the world.
				</p>
				<p>
					MOFILM are the market leaders in creating film content for mobile having worked with leading
					industry legends including Robert Redford, Kevin Spacey, Spike Lee and Isabella Rossellini
					as well as leading film festivals. MOFILM also host the &quot;world's biggest mobile film
					festival&quot; every year in Barcelona, Spain to showcase emerging talent from around the
					globe.
				</p>

				<hr />

				<div>
					<img src="{$themeimages}/business/business-partners.jpg" alt="partners" class="floatLeft spacer" />

					<h2>Partners</h2>
					<p>
						MOFILM is partnered with the World's biggest names to ensure the best support, visibility,
						knowledge and experience for our community of filmmakers.
					</p>
					<p><a href="/business/partners/">Read More</a></p>
				</div>

				<hr />

				<div>
					<img src="{$themeimages}/business/business-platform.jpg" alt="platform" class="floatLeft spacer" />

					<h2>Platform</h2>
					<p>
						We provide a mobile platform that can showcase the constant stream of high quality short
						films that we receive as part of our global film festival, brand incentives, partners and
						individual filmmakers.
					</p>
					<p><a href="/business/platform/">Read More</a></p>
				</div>

				<hr />

				<div>
					<img src="{$themeimages}/business/business-distribution.jpg" alt="distribution" class="floatLeft spacer" />

					<h2>Distribution</h2>
					<p>
						We are one of the worlds fastest growing film making communities, currently exceeding
						100,000 creative individuals, studios and agencies.
					</p>
					<p><a href="/business/distribution/">Read More</a></p>
				</div>
			</div>

			<div class="clearBoth"></div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer','/shared')}