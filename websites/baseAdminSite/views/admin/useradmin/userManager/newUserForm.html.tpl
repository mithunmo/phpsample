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
					<h2>{t}Add a New Admin User{/t}</h2>
					<div class="content">
						<div id="adminActions" class="body">
							{include file=$oView->getActionsMenuView()}
						</div>
						<div class="clearBoth"></div>
					</div>

					<div class="content">
						<div class="body">
							<div id="userFormAccordion">
								<h3><a href="#">{t}Provide the email address for the new user{/t}</a></h3>
								<div>
									<table class="data">
										<tbody>
											{include file=$oView->getTemplateFile('usersFormComEmail')}
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