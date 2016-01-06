{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}New Message{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h2>{t}New Message{/t}</h2>
			
			<form id="pmSend" action="{$postUri}" method="post" accept-charset="utf-8">
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
										<input type="text" disabled="disabled" name="Recep" value="{$fname}" />
										<input type="hidden" name="Recipient[]" value="{$userID}" />
										{*
										<select id="pmRecipient" name="Recipient[]" size="1">
											{if $oUser->getClientID() != mofilmClient::MOFILM}<option value="0" selected="selected">MOFILM Support</option>{/if}
										</select>
										*}
									</td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th>{t}Subject{/t}</th>
									<td><input type="text" name="Subject" value="" class="long" /></td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th class="valignTop">
										{t}Message{/t}<br />
										<br />
										<em>{t}HTML is not allowed and will be stripped. Carriage returns will be left intact.{/t}</em>
									</th>
									<td>
										<textarea name="Message" rows="15" cols="72" class="pmMessage"></textarea>
									</td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th>{t}Send{/t}</th>
									<td><input type="submit"  value="Send" class="medium" /></td>
								</tr>
								
							</tbody>
						</table>
					</div>
				</div>
			</form>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}