{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
<!-- Content Starts --> 
<div style="height:350px;background-image:url(/themes/momusic/images/page_back_border.gif)">
	<div style="width:740px;float:left; height:inherit;">

		{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<form action="{$doLogoutUri}" method="post" name="loginForm">
				<div style="padding-left:20px;">
					<h2>{t}Logout{/t}</h2>
					<p>{t}Are you sure you wish to logout?{/t}</p>
					<div>
						<input type="submit" name="submit" value="{t}Yes{/t}" />
					</div>
				</div>

				<br class="clearBoth" />
			</form>
			<br class="clearBoth" />
	</div>
</div>
</div> <!-- Content Ends -->
</div></div>
{include file=$oView->getTemplateFile('footer','/shared') pageTitle="momusic"}
