{*
Notes: Any link needs to be prefixed with either:
{$mofilmWwwUri} - Use www.mofilm site
{$mofilmMyUri} - Use my.mofilm site
*}
<body>
	<div style="background: #000a3e; width:auto; height:62px;" >
		<div style="float:right; width:10%; padding-right: 20px; padding-top: 10px; text-align: right;">{if $oLogged}Welcome {$oName}{/if}</div>
		<div style="width:1000px;background: #000a3e;margin: 0 auto; height:62px; background-image:url(/themes/mofilm/images/momusic/w1.jpg); background-repeat:no-repeat;">
			

			<div style="float:left; width:250px; height:30px; padding-top:5px;"><img src="/themes/mofilm/images/momusic/mo_music_logo.png" border="0px;" /></div>
				<div id="stylefive" style="width:420px; padding-top: 30px; float:right;height:30px;">
					  <ul>
						<li><a href="http://momusic.com" class="current">HOME</a></li>
						<li><a href="http://www.mofilm.com/">MOFILM</a></li>
						<li><a href="/music/help">FAQ</a></li>
						<li><a href="/music/myWork">MY WORK</a></li>
						{if $oLogged}
						<li><a href="/account/logout">LOGOUT</a></li>	
						{else}	
						<li><a href="/account/login?redirect=/">LOGIN</a></li>
						{/if}
					  </ul>
				</div>
		</div>
	</div>