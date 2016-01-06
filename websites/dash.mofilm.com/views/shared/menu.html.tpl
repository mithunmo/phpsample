	<body>
		<div id="topbar" class="transparent">
			<div id="title">{t}MOFILM Dash{/t}</div>
			<div id="leftnav">
				<a href="/home"><img src="{$themeimages}/ios/home.png" alt="{t}home{/t}" /></a>
			</div>

			<div id="rightnav">
				{if $oUser && $oUser->getID() > 0}
					<a href="/account/doLogout">{t}Logout{/t}</a>
				{else}
					<a href="{$loginUri}">{t}Login{/t}</a>
				{/if}
			</div>
		</div>
