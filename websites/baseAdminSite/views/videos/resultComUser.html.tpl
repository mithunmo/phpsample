{strip}
{if $oVideoResult->getUser()->getID() > 0}
<a href="{$daoUriView}?UserID={$oVideoResult->getUser()->getID()}" title="{t}Search for other movies by this user{/t}">
	<img src="{$themeicons}/16x16/user-list-videos.png" alt="user videos" class="smallIcon" />
</a>
{/if}
{/strip}
{if $oController->hasAuthority('usersController.edit')}
	{if $oVideoResult->getUser()->getID() > 0}
		<a href="/users/edit/{$oVideoResult->getUser()->getID()}" title="{t}Edit user{/t} {$oVideoResult->getUser()->getFullname()|xmlstring}">{$oVideoResult->getUser()->getFullname()|truncate:20:'..'|xmlstring}</a>
	{else}
		{$oVideoResult->getUser()->getFullname()|truncate:20:'..'|xmlstring}
	{/if}
{else}
	<span title="{$oVideoResult->getUser()->getFullname()|xmlstring}">{$oVideoResult->getUser()->getFullname()|truncate:20:'..'|xmlstring}</span>
{/if}