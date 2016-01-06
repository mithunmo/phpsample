{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
<!-- Content Starts --> 
<div style="height:700px;background-image:url(/themes/momusic/images/page_back_border.gif)">
	<div style="width:740px;float:left; height:inherit;">

		{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="floatLeft accountInfo">
				<h2>{t}Logout Successful{/t}</h2>
				<p>{t}You have been successfully logged out. Please wait to be redirected.{/t}</p>
				<p>{t}If you are not re-directed, <a href="{$metaRedirect}">please click to continue</a>.{/t}</p>
			</div>

			<div class="floatLeft accountForm">
				
			</div>
			
			<br class="clearBoth" />
	</div>
</div>
</div> <!-- Content Ends -->
</div></div>
{include file=$oView->getTemplateFile('footer','/shared') pageTitle="momusic"}
