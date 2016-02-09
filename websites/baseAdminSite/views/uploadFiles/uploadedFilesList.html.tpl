{assign var=offset value=$pagingOffset|default:0}
{assign var=limit value=30}
{assign var=totalObjects value=$oResults->getTotalResults()}

{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Uploaded Files List{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
	<div class="container">
	    		<h2>{t}Uploaded Files{/t}</h2>

			<form name="uploadedFilesSearch" action="{$daoUriView}" method="get">
				<div class="filters">
					<button type="submit" name="buttonname" value="{t}Search{/t}" class="floatRight">
						<img src="{$themeicons}/32x32/search.png" alt="search" class="icon" />
						{t}Search{/t}
					</button>
					<p style="margin: 0; margin-bottom: 5px;">
						{eventSelect id="eventList" name='EventID' selected=$searchEventID class="valignMiddle string" user=$oUser}
						{if $searchEventID}
							{sourceSelect id="eventListSources" name='SourceID' selected=$searchSourceID eventID=$searchEventID class="valignMiddle string" user=$oUser}
						{else}
							{sourceDistinctSelect id="eventListSources" name='SourceID' selected=$searchSourceID class="valignMiddle string" user=$oUser}
						{/if}
						{movieStatusSelect name='Status' selected=$searchStatus class="valignMiddle"}
					</p>
					<div class="clearBoth"></div>
				</div>
			
			{if $oResults->getResults()->getArrayCount() > 0}
			<table class="data">
				<tfoot>
					{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=6}
				</tfoot>
				<thead>
					<tr>
						<th class="first">ID</th>
						<th style="width: 175px;">User</th>
						<th style="width: 75px;">Event</th>
						<th style="width: 75px;">Source</th>
						<th style="width: 99px;">Upload Type</th>
						<th style="width: 99px;">Uploaded File</th>
						<th style="width: 99px;">Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=6}
				{foreach $oResults->getResults() as $oObject}
					<tr class="{cycle values=",alt"}">
						<td {if $oObject@iteration % 2}class="alt"{/if}>{$oObject->getID()}</td>
						<td {if $oObject@iteration % 2}class="alt"{/if}><a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'users/'}{$oObject->getUserID()}{'?token='}{$accessToken}">{$oObject->getUser()->getFullname()}</a></td>
						<td align="center" {if $oObject@iteration % 2}class="alt"{/if}><img src="/resources/client/events/logo/{$oObject->getSource()->getEvent()->getLogoName()}.png" width="50" height="28" alt="{$oObject->getSource()->getEvent()->getName()}" title="{$oObject->getSource()->getEvent()->getName()}" /></td>
						<td align="center" {if $oObject@iteration % 2}class="alt"{/if}><img src="/resources/client/sources/logo/{$oObject->getSource()->getLogoName()}.png" width="50" height="28" alt="{$oObject->getSource()->getName()}" title="{$oObject->getSource()->getName()}" /></td>
						<td align="center" {if $oObject@iteration % 2}class="alt"{/if}>{$oObject->getUploadType()}</td>
						<td align="center" {if $oObject@iteration % 2}class="alt"{/if}>
							<a href="/download/generalDownloads?url={$oObject->getDownloadFileName()}" target="_blank">
								<img src="{$themeicons}/32x32/downloadFiles.png" alt="search" class="icon" />
							</a>
						</td>
						<td align="center" {if $oObject@iteration % 2}class="alt"{/if}><div id="uploadStatusDisplay{$oObject->getID()}">{$oObject->getStatus()}</div></td>
						<td {if $oObject@iteration % 2}class="alt"{/if}>
							<div id="displayButtons{$oObject->getID()}">
								{if $oObject->getStatus() == 'Pending'}
									<button type="button" name="approve{$oObject->getID()}" value="{$oObject->getID()}" class="uploadedFilesApproved">Approve</button>
									 | 
									<button type="button" name="reject{$oObject->getID()}" value="{$oObject->getID()}" class="uploadedFilesRejected">Reject</button>
								{/if}

								{if $oObject->getStatus() == 'Approved'}
									<button type="button" name="resend{$oObject->getID()}" value="{$oObject->getID()}" class="uploadedFilesReSend">Re-Send</button>
								{/if}
							</div>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
			{else}
				<p>No objects found in system.</p>
			{/if}
			</form>
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}