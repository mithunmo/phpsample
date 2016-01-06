{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}eventImages{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
				<h2>{t}Event Image Manager - Edit - {/t}{$oEvent->getID()} - {$oEvent->getName()}</h2>

				<form id="eventImageUpload" name="eventImageUpload" method="post" action="{$doEditURI}/{$oEvent->getID()}" enctype="multipart/form-data">
					<div class="content">
						<div id="adminActions" class="body">
							<div class="daoAction">
								<button type="submit" name="Save" value="{t}Save{/t}" class="save">
									<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save record{/t}" class="icon" />
									{t}Save{/t}
								</button>
							</div>
							<div class="daoAction">
								<button type="reset" name="Reset" value="{t}Reset{/t}" class="reset">
									<img src="{$themeicons}/32x32/action-undo.png" alt="{t}Undo changes{/t}" class="icon" />
									{t}Reset{/t}
								</button>
							</div>
							<div class="daoAction">
								<a href="{$viewURI}" title="{t}Cancel edit record{/t}" class="cancel">
									<img src="{$themeicons}/32x32/action-cancel.png" alt="{t}Cancel edit record{/t}" class="icon" />
									{t}Cancel{/t}
								</a>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>

					<div class="content">
						<table class="data">
							<thead>
								<tr>
									<th>{t}Current Image{/t}</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<img src="{$clientEventFolder}/{$oEvent->getLogoName()}.jpg" alt="event" style="width: 150px; border: 1px solid #000;" />
										<br />
										<img src="{$adminEventFolder}/{$oEvent->getLogoName()}.jpg" alt="event" style="border: 1px solid #000;" />
									</td>
									<td style="vertical-align: top;">
										<em>{$oEvent->getName()}</em><br />
										<input type="file" name="EventImage" class="long" />
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="content">
						<h3>{t}Source Images{/t}</h3>
						<table class="data">
							<thead>
								<tr>
									<th>{t}Current Image{/t}</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								{foreach $oEvent->getSourceSet() as $oSource}
								<tr>
									<td>
										<img src="{$clientSourceFolder}/{$oSource->getLogoName()}.jpg" alt="brand" style="width: 150px; border: 1px solid #000;" />
										<br />
										<img src="{$adminSourceFolder}/{$oSource->getLogoName()}.jpg" alt="brand" style="border: 1px solid #000;" />
									</td>
									<td style="vertical-align: top;">
										<em>{$oSource->getName()}</em><br />
										<input type="file" name="Source[{$oSource->getID()}]" class="long" />
									</td>
								</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				</form>

				<br class="clearBoth" />
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}