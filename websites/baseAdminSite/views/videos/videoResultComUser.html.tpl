{strip}
{if $oMovieObj->getUser()->getID() > 0}
<a href="{$daoUriView}?UserID={$oMovieObj->getUser()->getID()}" title="{t}Search for other movies by this user{/t}">
	<img src="{$themeicons}/16x16/user-list-videos.png" alt="user videos" class="smallIcon" />
</a>
{/if}
{/strip}
{if $oController->hasAuthority('usersController.edit')}
	{if $oMovieObj->getUser()->getID() > 0}
		<a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'users/'}{$oMovieObj->getUser()->getID()}{'?token='}{$accessToken}" title="{t}Edit user{/t} {$oMovieObj->getUser()->getFullname()|xmlstring}">{$oMovieObj->getUser()->getFullname()|truncate:20:'..'|xmlstring}</a>
	{else}
		{$oMovieObj->getUser()->getFullname()|truncate:20:'..'|xmlstring}
	{/if}
{else}
	<span title="{$oMovieObj->getUser()->getFullname()|xmlstring}">{$oMovieObj->getUser()->getFullname()|truncate:20:'..'|xmlstring}</span>
{/if}