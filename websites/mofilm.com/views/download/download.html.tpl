{include file=$oView->getTemplateFile('header','/shared') pageTitle="File Download"}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			<h1>{t}Downloading...{/t}</h1>
		
			<div class="downloadContainer">
				<div class="image"><img src="{$themeimages}/downloading.png" border="0" alt="{t}Your download is ready{/t}"></div>
				
				<div class="details">
					<h2>{t}Your download will begin shortly.{/t}</h2>
					{include file=$oView->getTemplateFile('fileInfo', 'download')}
				</div>
	
				<div class="clearBoth"></div>
			</div>
			
			{if $oObject->getFiletype() == 'brief' && $isProduction}
			<script type="text/javascript" src="//ah8.facebook.com/js/conversions/tracking.js"></script>
			<script type="text/javascript">
				try {
				  FB.Insights.impression({
				     'id' : 6002859209037,
				     'h' : '92c8d427fd',
				     'value' : 2// you can change this dynamically
				  });
				} catch (e) {}
			</script>
			{/if}
		</div>
	</div>
		
{include file=$oView->getTemplateFile('footer','/shared')}