{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=10}
{assign var=newsletterType value=1}
{assign var=objects value=$oModel->getMarketingNLObjectList($offset, $limit, $newsletterType)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{if $objects->getArrayCount() > 0}
	<p>
		{t}To send a newsletter, first create a new send request using the "New" link above.{/t}
		{t}Use the wizard to create a list of users and select the newsletter to send.{/t}
		{t}When you save the send request, it will be sent automatically.{/t}
	</p>
	<p>
		{t}Manually re-send a newsletter by clicking the Send icon. This should only be done after a newsletter has been sent.{/t}
	</p>

	<table class="data">
		<thead>
			<tr>
				<th class="first">{t}Id{/t}</th>
				<th>{t}Newsletter Title{/t}</th>
				<th>{t}Scheduled For{/t}</th>
				<th>{t}Status{/t}</th>
				<th>&nbsp;</th>
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=4}
		</tfoot>
		<tbody>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=4}
			{foreach $objects as $oObject}
				<tr class="{cycle values="alt,"}">
					<td>{$oObject->getID()}</td>
					<td>
						{assign var=nlName value=$oModel->getNlNameById($oObject->getNewsletterID())}
						{$nlName}<br />
						<em>{$oModel->getEmailById($oObject->getEmailName())}</em>
					</td>
					<td>{$oObject->getScheduledDate()}</td>
					<td class="alignCenter">{if $oObject->getStatus() == 0}{t}Not sent{/t}{else}{t}Sent{/t}{/if}</td>
					<td class="alignCenter">
						{if $oController->hasAuthority($oController->getClassName()|cat:'.send')}
							<a href="{$sendl}/{$oObject->getPrimaryKey()}/{$status}" class="sendnl" title="{t}Send this newsletter{/t}">
								<img src="{$themeicons}/32x32/sendnewsletter.png" alt="Send" title="{t}Send this newsletter{/t}" class="icon" />
							</a>
						{/if}
					</td>
					<td class="actions">
						{include file=$oView->getTemplateFile('daoObjectListDefaultActions', '/shared')}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
