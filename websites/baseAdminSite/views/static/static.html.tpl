{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Static Pages{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="main">
				<h2>{t}Static Pages{/t}</h2>
				<p>
					{t}Static pages are pages whose content rarely changes. If you are seeing this page, you either requested a page that does not exist, or did not request a page.{/t}
				</p>

				{assign var=pages value=$oModel->getStaticPages()}
				{if $pages->getArrayCount() > 0}
					<p>{t}The following pages have been configured on this site:{/t}</p>
					<p>
						{foreach key=uriLink item=title from=$pages}
						<a href="/static/{$uriLink}" title="{$title}">{$title}</a><br />
						{/foreach}
					</p>
				{/if}
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}