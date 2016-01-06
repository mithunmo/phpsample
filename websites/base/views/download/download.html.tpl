{include file=$oView->getTemplateFile('header','/shared') pageTitle="File Download"}

		<h1>{t}Downloading...{/t}</h1>
		
		<div class="downloadContainer">
			<div class="image"><img src="/resources/downloading.png" border="0" alt="Your download is ready"></div>
			
			<div class="details">
				<h2>{t}Your download will begin shortly.{/t}</h2>
				{include file=$oView->getTemplateFile('fileInfo', 'download')}
			</div>

			<div class="clearBoth"></div>
		</div>
		
{include file=$oView->getTemplateFile('footer','/shared')}