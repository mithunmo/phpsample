{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Reply to Message - {/t}'|cat:$oModel->getMessage()->getSubject()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h2>{t}Reply to - {/t} {$oModel->getMessage()->getSubject()}</h2>
			
			<form id="pmSend" action="{$sendUri}" method="post" accept-charset="utf-8">
				<div class="hidden">
					<input type="hidden" name="Recipient[0]" value="{$oModel->getMessage()->getFromUserID()}" />
					<input type="hidden" name="MessageAction" value="reply" />
					<input type="hidden" name="MessageID" value="{$oModel->getMessage()->getMessageID()}" />
					{if $oModel->getMessage()->isAttachedToMovie()}
					<input type="hidden" name="MovieID" value="{$oModel->getMessage()->getAttachedMovie()->getID()}" />
					{/if}
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
									<td>{$oModel->getMessage()->getSender()->getFullname()|xmlstring}</td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th>{t}Subject{/t}</th>
									<td><input type="text" name="Subject" value="{if stripos($oModel->getMessage()->getSubject()|xmlstring, 'Re:') !== 0}Re: {/if}{$oModel->getMessage()->getSubject()|xmlstring}" class="long" /></td>
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
									<th class="valignTop">{t}Original Message{/t}</th>
									<td>
<pre>{t}On {$oModel->getMessage()->getCreateDate()|date_format:'%d-%m-%Y'}, {$oModel->getMessage()->getSender()->getFullname()} wrote:{/t}
-----------------------------------------------------------------------
{$oModel->getMessage()->getMessage()|xmlstring|wordwrap:72}</pre>
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