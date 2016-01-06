{include file=$oView->getTemplateFile('header','/shared') pageTitle="SOUTHBYTES the official video voice of SXSW - Page Not Found"}
{include file=$oView->getTemplateFile('menu','/shared')}


	<div id="sbcontentleft">
		<div class="fullcontent">
			<h2>Page Not Found</h2>
			<p>
				Whoops, we couldn't find the page you requested. Instead here is the list of available pages:
			</p>

			{assign var=pages value=$oModel->getStaticPages()}
			{if $pages->getArrayCount() > 0}
				<p>{foreach key=uriLink item=title from=$pages}
				<a href="/static/{$uriLink}" title="{$title}">{$title}</a><br />
				{/foreach}</p>
			{/if}
		</div>
	</div>


{include file=$oView->getTemplateFile('footer','/shared')}