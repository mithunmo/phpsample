<h3>{$title|default:'{t}Your Profile{/t}'}</h3>
<dl class="userProfile">
	<dt><img src="/themes/shared/icons/user.png" alt="user" class="smallIcon"/></dt>
	<dd>
		{if $oController->hasAuthority('usersController.edit') && $oUser->getID() > 0}
			<a href="/users/edit/{$oUser->getID()}" title="{t}Edit User Details{/t}">{$oUser->getFullname()|xmlstring}</a>
		{else}
			{$oUser->getFullname()|xmlstring}
		{/if}
	</dd>
	
	{if $oUser->getTerritoryID()}
		<dt><img src="/themes/shared/flags/{$oUser->getTerritory()->getShortName()|lower}.png" alt="{$oUser->getTerritory()->getShortName()}" class="valignMiddle"/></dt>
		<dd>{$oUser->getTerritory()->getCountry()}</dd>
	{/if}
	
	{if $oController->hasAuthority('communicate') && $oUser->getEmail()}
		<dt><img src="{$themeicons}/16x16/action-send.png" alt="{t}Send a message{/t}" class="smallIcon" /></dt>
		<dd><a href="/users/message/{$oUser->getID()}" title="{t}Send a message to this user{/t}">{$oUser->getEmail()}</a></dd>
	{/if}
	
	{if $oController->hasAuthority('communicate') && $oUser->getParamSet()->getParam('Phone')}
		<dt><img src="{$themeicons}/16x16/skype.png" alt="skype" class="smallIcon" /></dt>
		<dd><a href="callto://{$oUser->getParamSet()->getParam('Phone')|formatPhoneNumber}">{$oUser->getParamSet()->getParam('Phone')|formatPhoneNumber}</a></dd>
	{/if}

	
	<dt><img src="/themes/shared/icons/date.png" alt="date" class="smallIcon"/></dt>
	<dd>{t}Registered on {$oUser->getRegistered()|date_format:"%d/%m/%y"}{/t}</dd>
</dl>