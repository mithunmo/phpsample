{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=newsletterType value=2}
{assign var=objects value=$oModel->getCCANLObjectList($offset, $limit, $newsletterType)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">&nbsp;</th>
				<th>{t}CCA Email{/t}</th>
				<th>{t}Scheduled Date{/t}</th>
				<th>{t}Attachemnt{/t}</th>
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
		</tfoot>
		<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
		{foreach $objects as $oObject}
			<tr class="{cycle values="alt,"}">
				<td>{$oObject@iteration+$offset}</td>
				<td>
					{assign var=nlName value=$oModel->getNewsletterDetails($oObject->getID())}
					{$nlName}<br />
					<em>{$oModel->getEmailById($oObject->getEmailName())}</em>
				</td>
				<td>{$oObject->getScheduledDate()}</td>
				<td class="alignCenter"><a href="{$displayAttachmentURI}?path={$oObject->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_ATTACH)}" title="{t}Click to download and preview file{/t}"> File </a></td>
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
