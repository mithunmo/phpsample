{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Forward Message - {/t}'|cat:$oModel->getMessage()->getSubject()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h2>{t}Forward Message - {/t} {$oModel->getMessage()->getSubject()}</h2>
			
			<form id="pmSend" action="{$sendUri}" method="post" accept-charset="utf-8">
				<div class="hidden">
					<input type="hidden" name="MessageAction" value="forward" />
					<input type="hidden" name="MessageID" value="{$oModel->getMessage()->getMessageID()}" />
				</div>
			
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
								<tr class="{cycle values="alt,"}">
									<th class="pmSender">{t}To{/t}</th>
									<td>
										<select id="pmRecipient" name="Recipient[]" size="1">
											{if $oUser->getClientID() != mofilmClient::MOFILM}<option value="0" selected="selected">MOFILM Support</option>{/if}
										</select>
									</td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th>{t}Subject{/t}</th>
									<td><input type="text" name="Subject" value="Fwd: {$oModel->getMessage()->getSubject()}" class="long" /></td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th class="valignTop">
										{t}Message{/t}<br />
										<br />
										<em>{t}HTML is not allowed and will be stripped. Carriage returns will be left intact.{/t}</em>
									</th>
									<td>
										<textarea name="Message" rows="15" cols="72" class="pmMessage">


{t}Forwarded message from {$oModel->getMessage()->getSender()->getFullname()} on {$oModel->getMessage()->getCreateDate()|date_format:'%d-%m-%Y'}:{/t}
-----------------------------------------------------------------------
{$oModel->getMessage()->getMessage()|wordwrap:72}</textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</form>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}