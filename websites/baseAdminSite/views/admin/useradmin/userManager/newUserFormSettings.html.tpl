{include file=$oView->getTemplateFile('header', 'shared') pageTitle=$oMap->getDescription()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
				<form id="adminFormData" name="formData" method="post" action="{$formAction}" accept-charset="utf-8">
					<h2>{t}Add a New Admin User: Settings{/t}</h2>
					<div class="content">
						<div id="adminActions" class="body">
							{include file=$oView->getActionsMenuView()}
						</div>
						<div class="clearBoth"></div>
					</div>

					<div class="content">
						<div class="body">
							<div class="hidden">
								<input type="hidden" name="Email" value="{$oModel->getEmail()|xmlstring}" />
							</div>
							<div id="userFormAccordion">
								<h3><a href="#">{t}Please complete the following information{/t}</a></h3>
								<div>
									<table class="data">
										<tbody>
											<tr>
												<th>{t}Username{/t}</th>
												<td>{$oModel->getEmail()}</td>
											</tr>
											{include file=$oView->getTemplateFile('usersFormComName') oObject=$oModel}
											{include file=$oView->getTemplateFile('usersFormComClient')}
											{include file=$oView->getTemplateFile('usersFormComUserGroup')}
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
				</form>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}