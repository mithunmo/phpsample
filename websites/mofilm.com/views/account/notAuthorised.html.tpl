{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Access Denied{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft accountInfo">
				<h2>{t}Access Denied{/t}</h2>
				<p>{t}Sorry, but you are not authorised to access this resource.{/t}</p>
				<p><a href="/home">{t}Back to Dashboard{/t}</a></p>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}