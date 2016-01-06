<h3>{$title|default:'{t}Your Stats{/t}'}</h3>
<p>
	{if $oController->hasAuthority('videosController.doSearch')}
		<a href="/videos/doSearch?UserID={$oUser->getID()}" title="{t}Display all videos for this user{/t}">{$oUser->getStats()->getMovieCount()} {t}Movies Uploaded{/t}</a>
	{else}
		{$oUser->getStats()->getMovieCount()} {t}Movies Uploaded{/t}
	{/if}
	<br />
	{$oUser->getStats()->getTotalApproved()} {t}Approved{/t},
	{$oUser->getStats()->getTotalRejected()} {t}Rejected{/t},
	{$oUser->getStats()->getTotalAwaiting()} {t}Awaiting{/t}
</p>
<p>
	{if $oController->hasAuthority('grantsController.doSearch')}
		<a href="/grants/doSearch?UserID={$oUser->getID()}" title="{t}Display all grants for this user{/t}">{$oUser->getGrantStats()->getGrantsAppliedCount()} {t}Grants Application Submitted{/t}</a>
	{else}
		{$oUser->getGrantStats()->getGrantsAppliedCount()} {t}Grants Application Submitted{/t}
	{/if}
	<br />
	{$oUser->getGrantStats()->getTotalApproved()} {t}Approved{/t},
	{$oUser->getGrantStats()->getTotalRejected()} {t}Rejected{/t},
	{$oUser->getGrantStats()->getTotalPending()} {t}Pending{/t}
</p>