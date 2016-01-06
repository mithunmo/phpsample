{include file=$oView->getTemplateFile('header','/shared') pageTitle="{t}Upload Document{/t}"}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
		<h1>{t}Upload Document{/t}</h1>

		<div class="downloadContainer">
		{if $source->isOpen() && $source->getID() > 0}
			<div class="details">
					<form id="documentsUpload" name="documentsUpload" method="post" action="/uploadFiles/doUploadAction" enctype="multipart/form-data">
					<div class="content">
						<div id="adminActions" class="body">
							<div class="daoAction">
								<button type="submit" name="Upload" value="{t}Upload{/t}" class="save">
									<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Upload Document{/t}" class="icon" />
									{t}Upload{/t}
								</button>
							</div>
							<div class="daoAction">
								<button type="reset" name="Reset" value="{t}Reset{/t}" class="reset">
									<img src="{$themeicons}/32x32/action-undo.png" alt="{t}Undo changes{/t}" class="icon" />
									{t}Reset{/t}
								</button>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>

					<div class="content">
					    
						<h3>{t}Selected Event/Brand{/t}</h3>
						<div><strong>{t}Event{/t}</strong> : {$source->getEvent()->getName()}</div>
						<div><strong>{t}Source{/t}</strong> : {$source->getName()}</div>
						<div><strong>{t}Upload Type{/t}</strong> : NDA</div>
						
						<h3>{t}Select File to Upload{/t}</h3>
						<input type="file" name="uploadDocuments" class="long" />
						<input type="hidden" name="SourceID" value="{$source->getID()}" />
					</div>
				</form>

				<div class="clearBoth"></div>
			</div>

			<div class="clearBoth"></div>
		{else}
			<h3>{t}No Uploads are allowed after the competition is closed{/t}.</h3>
		{/if}
		</div>
		</div>
	</div>
		
{include file=$oView->getTemplateFile('footer','/shared')}