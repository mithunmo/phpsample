{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Sent Message - {/t}'|cat:$oModel->getMessage()->getSubject()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h2>{t}Sent Message - {/t} {$oModel->getMessage()->getSubject()}</h2>
			
			<div class="content">
				<div id="adminActions" class="body">
					{include file=$oView->getTemplateFile('daoActionsMenu', '/shared')}
				</div>
				<div class="clearBoth"></div>
			</div>

			<div class="content">
				<div class="body">
					<table class="data">
						<tbody>
							<tr class="{cycle values="alt,''"}">
								<th class="pmSender">{t}Sent{/t}</th>
								<td>{$oModel->getMessage()->getSentDate()|date_format:'%d %B %Y @ %H:%M'}</td>
							</tr>
							<tr class="{cycle values="alt,''"}">
								<th class="pmSender">{t}To{/t}</th>
								<td>{$oModel->getMessage()->getRecipient()->getFullname()}</td>
							</tr>
							<tr class="{cycle values="alt,''"}">
								<th>{t}Subject{/t}</th>
								<td>{$oModel->getMessage()->getSubject()|xmlstring}</td>
							</tr>
							<tr class="{cycle values="alt,''"}">
								<th class="valignTop">{t}Message{/t}</th>
								<td>{$oModel->getMessage()->getMessage()|xmlstring|nl2br}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}