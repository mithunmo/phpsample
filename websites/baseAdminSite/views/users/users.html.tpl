{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Users - Search{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<h2>Users - Search</h2>
			<form name="userSearch" action="{$doSearchUri}" method="get">
				<div class="filters">
					<button type="submit" name="search" value="{t}Search{/t}" class="floatRight">
						<img src="{$themeicons}/32x32/search.png" alt="search" class="icon" />
						{t}Search{/t}
					</button>
					
					{t}Users with an Upload{/t} <input type="checkbox" name="UploadedMovie" value="1" {if $searchUploadedMovie}checked="checked"{/if}/>
					&nbsp;
					{t}Only Finalists{/t} <input type="checkbox" name="Finalists" value="1" {if $searchFinalists}checked="checked"{/if}/>
					&nbsp;
					{t}Keywords Search Only Email Addresses{/t} <input type="checkbox" name="OnlyEmail" value="1" {if $searchOnlyEmail}checked="checked"{/if}/>
					<br />
					
					<input type="text" placeholder="Search for name" name="Keywords" value="{$searchKeywords|escape:'htmlall':'UTF-8'}" class="valignMiddle string" onfocus="this.select()" />
					
					{* DR: 2010-07-16 Commented out for now
					{if $oController->hasAuthority('usersController.canSearchByClient')}
						{clientSelect name='ClientID' selected=$searchClientID class="valignMiddle string"}
					{/if}
					*}
					
					{if $oController->hasAuthority('usersController.canSearchByCountry')}
						{territorySelect name='CountryID' selected=$searchCountryID class="valignMiddle string" title='Select Country'}
					{/if}
					{if $oController->hasAuthority('usersController.canSearchByStatus')}
					<select name="Status" size="1" class="valignMiddle">
						<option value="Y" {if $searchStatus == 'Y'}selected="selected"{/if}>{t}Active Users{/t}</option>
						<option value="N" {if $searchStatus == 'N'}selected="selected"{/if}>{t}In-Active Users{/t}</option>
						<option value="" {if $searchStatus == ''}selected="selected"{/if}>{t}All Users{/t}</option>
					</select>
					{/if}
					
					<div class="clearBoth"></div>
				</div>
			</form>
			
			{assign var=offset value=$oResults->getSearchInterface()->getOffset()|default:0}
			{assign var=limit value=$oResults->getSearchInterface()->getLimit()}
			{assign var=totalObjects value=$oResults->getTotalResults()}
			
			<table class="data">
				<thead>
					<tr>
						<th class="first" style="width: 30%">{t}Users Name{/t}</th>
						<th style="width: 30%">{t}Email Address{/t}</th>
						<th>{t}Country{/t}</th>
						<th><span title="{t}Total movies for the user, Approved / Rejected{/t}">{t}Movies{/t}</span></th>
						<th class="last">{t}Registered{/t}</th>
					</tr>
				</thead>
				<tfoot>
					{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
				</tfoot>
				<tbody>
					{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
					{foreach $oResults as $oUserResult}
					<tr class="{cycle values="alt,"}">
						<td>
							{if $oController->hasAuthority('usersController.edit')}
								<a href="{$editUri}/{$oUserResult->getID()}" title="{t}Edit user details{/t}">
									<img src="{$themeicons}/16x16/action-edit-object.png" alt="{t}Edit user details{/t}" class="smallIcon" />
									{if strlen($oUserResult->getFullname()) > 3}{$oUserResult->getFullname()}{else}{$oUserResult->getEmail()}{/if}
								</a>
							{else}
								{if strlen($oUserResult->getFullname()) > 3}{$oUserResult->getFullname()}{else}{$oUserResult->getEmail()}{/if}
							{/if}
							
							{if ($oUser->getClientID() == mofilmClient::MOFILM || $oUser->getPermissions()->isRoot()) && $oUserResult->getPermissions()->isAuthorised('canLogin')}
								<img src="/themes/shared/icons/user_suit.png" alt="adm" title="{t}This user has admin access{/t}" class="smallIcon floatRight" style="margin: 2px 4px;" />
							{/if}
						</td>
						<td>
							{if $oUserResult->getParamSet()->getParam('Phone')}
								<a href="callto://{$oUserResult->getParamSet()->getParam('Phone')|formatPhoneNumber}"><img src="{$themeicons}/16x16/skype.png" alt="skype" class="smallIcon" /></a>
							{/if}
							{if $oController->hasAuthority('usersController.message')}
								<a href="{$messageUri}/{$oUserResult->getID()}" title="{t}Send a message to this user{/t}">
									<img src="{$themeicons}/16x16/action-send.png" alt="{t}Send a message{/t}" class="smallIcon" />
									{$oUserResult->getEmail()}
								</a>
							{else}
								{$oUserResult->getEmail()}
							{/if}
						</td>
						<td>{$oUserResult->getTerritory()->getCountry()}</td>
						<td class="alignCenter">
							<a href="/videos/doSearch?UserID={$oUserResult->getID()}" title="{t}Display all videos for this user{/t}">{$oUserResult->getStats()->getTotalApproved()}</a> ({$oUserResult->getStats()->getTotalRejected()})
						</td>
						<td>{$oUserResult->getRegistered()|date_format:'%d-%m-%Y'}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}