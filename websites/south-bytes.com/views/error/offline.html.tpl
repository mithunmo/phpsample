{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Offline{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="sbcontentleft">
		<div class="sbcontent">
			<h2>{t}Offline{/t}</h2>
			<p>{t}The site is currently offline for routine maintenance.{/t}</p>
			<p>{t}We'll be back soon! Please bare with us.{/t}</p>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}