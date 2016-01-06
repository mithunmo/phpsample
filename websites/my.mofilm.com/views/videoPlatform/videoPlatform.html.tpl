{include file=$oView->getTemplateFile('header','/shared') pageTitle="videoPlatform"}
{include file=$oView->getTemplateFile('menu','/shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			{foreach $oResult as $value}
				{assign var=size value=$value->size}
				Size : {math equation="$size/1000000" format="%.2f"} MB
				<br />
				{assign var=er value=$value->encodingRate}
				Encodingrate :  {math equation="$er/1000000" format="%.2f"} Mbps
				<br />
				<a href="/download/movie/?url={$value->url}"> Download</a>
				<br/>
			{/foreach}
		</div>
	</div>

{include file=$oView->getTemplateFile('footer','/shared')}