{include file=$oView->getTemplateFile('header','/shared') pageTitle="MOFILM - "|cat:$oMap->getDescription()}
{include file=$oView->getTemplateFile('menu','/shared')}
	
	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<p>
				Static pages are pages whose content rarely changes. If you are seeing this page, you
				either requested a page that does not exist, or did not request a page.
			</p>

			{assign var=pages value=$oModel->getStaticPages()}
			{if $pages->getArrayCount() > 0}
				<p>The following pages have been configured on this site:</p>
				<p>{foreach key=uriLink item=title from=$pages}
				<a href="/static/{$uriLink}" title="{$title}">{$title}</a><br />
				{/foreach}</p>
			{/if}

		</div>
	</div>
	
{include file=$oView->getTemplateFile('footer','/shared')}