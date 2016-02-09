{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Sent Items{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			<h2>{t}Sent Items{/t}</h2>
			
			{include file=$oView->getTemplateFile('messageFolderActions', '/account')}
			
			<div class="content">
				<div class="body">
					{assign var=totalObjects value=$oModel->getTotalSentMessages()}
					{assign var=objects value=$oModel->getSentMessages($offset, $limit)}
					{if $objects->getArrayCount() > 0}
						<table class="data">
							<thead>
								<tr>
									<th>{t}Sent{/t}</th>
									<th>{t}To{/t}</th>
									<th>{t}Subject{/t}</th>
									<th></th>
								</tr>
							</thead>
							<tfoot>
								{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=2}
							</tfoot>
							<tbody>
								{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=2}
							{foreach name=list item=oObject from=$objects}
								<tr class="{cycle values='alt,'}">
									<td class="pmDate">{$oObject->getSentDate()|date_format:'%d %B %Y @ %H:%M'}</td>
                                                                        <td class="pmSender"><a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'users/'}{$oObject->getToUserID()}{'?token='}{$accessToken}" target="_blank">{$oObject->getRecipient()->getFullname()}</a></td>
									<td class="pmSubject"><a href="{$readSentUri}/{$oObject->getMessageID()}" title="{t}Read{/t}">{$oObject->getSubject()}</a></td>
									<td class="last">
										<a href="{$readSentUri}/{$oObject->getMessageID()}" title="{t}Read{/t}"><img src="{$themeicons}/32x32/mail-mark-read.png" alt="{t}Read{/t}" class="icon" /></a>
										<a href="{$deleteSentUri}/{$oObject->getMessageID()}" title="{t}Delete{/t}" class="deletePm"><img src="{$themeicons}/32x32/action-delete-object.png" alt="{t}Delete{/t}" class="icon" /></a>
									</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					{else}
						<p>{t}There are no messages in your sent items.{/t}</p>
					{/if}
				</div>
			</div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}