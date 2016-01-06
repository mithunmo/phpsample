<h3 id="bookmarkedEvents">{t}Your Bookmarked Events{/t}</h3>
{if $oFavourites && $oFavourites->getCount() > 0}
	{foreach $oFavourites as $oEvent}
		{if $oUser->hasEvent($oEvent->getID())}
			{include file=$oView->getTemplateFile('eventListDetail', '/admin/eventadmin/eventManager') oEvent=$oEvent collapseOldEvents=false}
		{/if}
	{/foreach}
{/if}

<h3 id="availableEvents">{t}Your Events{/t}</h3>
{foreach $events as $oEvent}
	{if $oUser->hasEvent($oEvent->getID())}
		{include file=$oView->getTemplateFile('eventListDetail', '/admin/eventadmin/eventManager') oEvent=$oEvent}
	{/if}
{/foreach}