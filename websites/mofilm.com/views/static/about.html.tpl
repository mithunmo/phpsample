{include file=$oView->getTemplateFile('header','/shared') pageTitle="{t}About The Company{/t}"}
{include file=$oView->getTemplateFile('menu','/shared')}

	<div id="body">
		<div class="container">
			<div class="pgHead">
				<h1 class="floatLeft">{t}About MOFILM{/t}</h1>

				<div class="navigation floatRight">
					{include file=$oView->getTemplateFile('aboutMenu', '/static')}
				</div>

				<div class="clearBoth"></div>
			</div>

			<div class="sidebar floatRight spacer">
				<h2 class="alignCenter">{t}Our Clients{/t}</h2>
				<img src="{$themeimages}/about-logos.jpg" alt="Brand Logos" />
			</div>

			<div class="main">
				<div class="alignJustify">
					<p>
						MOFILM inspires film-makers to create videos for big brands and social causes. Our revolutionary
						new process is transforming the video creation industry by connecting brands more directly with
						film-makers and eliminating multiple layers of beauracracy and administration which waters down
						creativity and inflates costs in traditional processes.
					</p>
					<p>
						MOFILM events have becoming legendary among the film-making community as the place to meet brands,
						fellow film-makers and film's most important visionaries like Kevin Spacey, Jesse Eisenberg, Robert
						Redford, Isabella Rossellini, Terry Gilliam and Spike Lee. Jon Landau, producer of the two
						top-grossing movies of all time, Titanic and Avatar, has joined MOFILM's advisory board.
					</p>
					<p>
						MOFILM runs multiple major video competitions every year, offering millions of dollars in prizes to
						filmmakers plus the chance to travel to unforgettable destinations around the world, including
						London Film Festival, Rio, Lollapalooza, Taj Mahal, GSM Barcelona, Tribeca Film Festival, Consumer
						Electronics Show Las Vegas, Cannes Lions and many others.
					</p>
					<p>
						MOFILM is dedicated to helping filmmakers get discovered, get famous and get well paid for their
						talent while providing brands with innovative advertising and video content. The MOFILM community
						is proudly displayed on our profile pages to maximise visibility and even help out our competitors
						who are always trying to recruit MOFILMers!
					</p>
					<p>
						MOFILM winners become members of our Pro community who receive paid work from brands outside the
						contests. MOFILM music is now set to revolutionize music by providing famous tracks to brands with-out
						the inflated fees tacked on by middlemen. MOFILM music will also discover new musical talent for
						brands interested in a fresh sound at a reasonable fee.
					</p>
					<p>
						It is our company mission to help a previously inknown film-maker to one day win an Oscar or a grammy.
					</p>
					<p>
						MOFILM is a privately held company with offices in London, Bangalore and Los Angeles.
					</p>
				</div>
			</div>

			<div class="clearBoth"></div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer','/shared')}