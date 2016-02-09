{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Your Inbox{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			<h2>{t}Your Inbox{/t}</h2>
			
			{include file=$oView->getTemplateFile('messageFolderActions', '/account')}
			
			<div class="content">
				<div class="body">
					{assign var=totalObjects value=$oModel->getTotalInboxMessages()}
					{assign var=objects value=$oModel->getInboxMessages($offset, $limit)}
					{if $objects->getArrayCount() > 0}
						<table class="data">
							<thead>
								<tr>
									<th>{t}Received{/t}</th>
									<th>{t}From{/t}</th>
									<th>{t}Status{/t}</th>
									<th>{t}Subject{/t}</th>
									<th></th>
								</tr>
							</thead>
							<tfoot>
								{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
							</tfoot>
							<tbody>
								{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
							{foreach name=list item=oObject from=$objects}
								<tr class="{cycle values='alt,'}">
									<td class="pmDate">{$oObject->getCreateDate()|date_format:'%d %B %Y @ %H:%M'}</td>
                                                                        <td class="pmSender"><a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'users/'}{$oObject->getFromUserID()}{'?token='}{$accessToken}" target="_blank">{$oObject->getSender()->getFullname()}</a></td>
									<td class="pmStatus">
										{if $oObject->getStatus() == 'New' && $oObject->getCreateDate()|date_format:'%Y%m%d' == $smarty.now|date_format:'%Y%m%d'}
											<img src="{$themeicons}/32x32/mail-status-unread-new.png" alt="{t}Unread New{/t}" title="{t}Unread New{/t}" class="icon" />
										{elseif $oObject->getStatus() == 'New'}
											<img src="{$themeicons}/32x32/mail-status-unread.png" alt="{t}Unread{/t}" title="{t}Unread{/t}" class="icon" />
										{elseif $oObject->getStatus() == 'Replied'}
											<img src="{$themeicons}/32x32/mail-status-replied.png" alt="{t}Replied{/t}" title="{t}Replied{/t}" class="icon" />
										{else}
											<img src="{$themeicons}/32x32/mail-status-read.png" alt="{t}Read{/t}" title="{t}Read{/t}" class="icon" />
										{/if}
									</td>
									<td class="pmSubject">
										{if $oObject->isAttachedToMovie()}
											<a href="{adminMovieLink movieID=$oObject->getAttachedMovieID()}" title="{t}Click to see this video{/t}"><img src="{$themeicons}/16x16/user-list-videos.png" alt="Movie" class="smallIcon" /></a>
										{/if}
										<a href="{$readUri}/{$oObject->getMessageID()}" title="{t}Read{/t}" {if $oObject->getStatus() == 'New'}class="new"{/if}>{$oObject->getSubject()}</a>
									</td>
									<td class="last">
										<a href="{$readUri}/{$oObject->getMessageID()}" title="{t}Read{/t}"><img src="{$themeicons}/32x32/mail-mark-read.png" alt="{t}Read{/t}" class="icon" /></a>
										<a href="{$deleteUri}/{$oObject->getMessageID()}" title="{t}Delete{/t}" class="deletePm"><img src="{$themeicons}/32x32/action-delete-object.png" alt="{t}Delete{/t}" class="icon" /></a>
									</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					{else}
						<p>{t}There are no messages in your inbox.{/t}</p>
					{/if}
				</div>
			</div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}