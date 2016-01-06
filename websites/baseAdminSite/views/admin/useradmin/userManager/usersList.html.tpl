{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=clientID value=$oController->getSearchParameter('ClientID', 0)}
{assign var=emailAddress value=$oController->getSearchParameter('Email', '')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $clientID, $emailAddress)}
{assign var=totalObjects value=$objects->getTotalResults()}

{if $oUser->getClientID() == mofilmClient::MOFILM || $oUser->getPermissions()->isRoot()}
	<div class="filters">
		{clientSelect name='ClientID' selected=$clientID class="valignMiddle" onchange="this.form.submit()"}
		<input type="text" name="Email" value="{$emailAddress|default:'Search by email address'}" class="medium" onfocus="this.select()" />
	</div>
{/if}

{if $objects->getTotalResults() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first" style="width: 160px;">{t}Full Name{/t}</th>
				<th style="width: 200px;">{t}Email Address{/t}</th>
				<th>{t}Permission Group{/t}</th>
				<th>{t}Enabled{/t}</th>
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
		</tfoot>
		<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
		{foreach name=list item=oObject from=$objects}
			<tr class="{cycle values="alt,"}">
				<td>{$oObject->getFirstname()} {$oObject->getSurname()}</td>
				<td><a href="mailto:{$oObject->getEmail()}">{$oObject->getEmail()}</a></td>
				<td>{$oObject->getPermissionGroup()->getDescription()}</td>
				<td class="alignCenter {if $oObject->getEnabled() == 'Y'}enabled{else}disabled{/if}">{$oObject->getEnabled()}</td>
				<td class="actions">
					{if $oObject->getID() != $oUser->getID()}
						{include file=$oView->getTemplateFile('daoObjectListDefaultActions', '/shared')}
					{/if}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
