{include file=$oView->getTemplateFile('header', 'shared') pageTitle=$oMap->getDescription()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
				<form id="adminFormData" name="formData" method="post" action="/help/helpPages/doMultiUpload" accept-charset="utf-8" enctype="multipart/form-data">
					<h2>{$oMap->getDescription()} - {$daoUriAction|replace:'Object':''|capitalize}</h2>
					<div class="content">

						<div id="adminActions" class="body">
                        	<div class="daoAction">
                            	<button class="save" value="doUpload" name="Save" type="submit">
                                	<img class="icon" alt="Upload" src="/themes/mofilm/images/icons/32x32/action-do-new-object.png">
                                    Save
                                </button>
                            </div>
						</div>
					</div>

					<div class="content">
						<div class="body" id="selectedFiles" style="border: 3px solid rgb(153, 153, 153); padding: 10px;">
                        	<input type="file" name="helpImageUpload[]" value="" class="long multi" id="selectFile" />
						</div>
					</div>
				</form>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}