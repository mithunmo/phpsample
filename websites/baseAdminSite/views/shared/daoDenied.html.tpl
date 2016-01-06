{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Access Violation{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
				<h2>{t}Access Violation{/t}</h2>
				
				<div class="content">
					<div class="body">
						<p>{t}You have attempted an action you are not authorised for.{/t}</p>
						<p>{t}This has been logged and will be reported.{/t}</p>
						<p>{t}Continued attempts will cause your account to be disabled.{/t}</p>
						<p><a href="{$oMap->getUriPath()}">Back</a></p>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}