	<body>
		<div id="sbmain">
			<div id="toplogo"><img src="{$themeimages}/logo.png" alt="logo" width="233" height="28"  /></div>

			<div id="sbsecond">
				<div id="secondcontent">
					<div id="paintlogo">
						<a href="/" title="Home"><img align="left" src="{$themeimages}/sblogo.png" alt="logo" /></a>
						<img align="right" src="{$themeimages}/paint.png" alt="logopaint" />
					</div>

					<a href="/static/team"><img align="right" src="{$themeimages}/team.png" alt="team" style="margin-right: 30px;" /></a>
				</div>
			</div>
		</div>

		<div id="sbcontent">
			<div id="menu">
				<ul>
					<li {if $selected =='home'}class="current"{/if}><a href="/">HOME</a></li>
					<li {if $selected =='blog'}class="current"{/if}><a href="/blog">BLOG</a></li>
					<li {if $selected =='gallery'}class="current"{/if}><a href="/gallery">GALLERY</a></li>
					<li {if $selected =='faq'}class="current"{/if}><a href="/static/faq">FAQ</a></li>
					<li {if $selected =='upload'}class="current"{/if}><a href="http://www.mofilm.com/account/upload?sourceID=138" target="_blank">UPLOAD</a></li>
					<li {if $selected =='terms'}class="current"{/if}><a href="/static/terms">T's&amp;C's</a></li>
				</ul>
			</div>
		