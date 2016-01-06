{if $oFavourites && $oFavourites->getCount() > 0}
	<div class="graytitle">{t}Your Bookmarked Events{/t}</div>
	{foreach $oFavourites as $oEvent}
		{if $oUser->hasEvent($oEvent->getID())}
			{include file=$oView->getTemplateFile('eventListDetail', '/admin/eventadmin/eventManager') oEvent=$oEvent collapseOldEvents=false}
		{/if}
	{/foreach}
{/if}

<div class="graytitle">{t}Your Events{/t}</div>
{foreach $events as $oEvent}
	{if $oUser->hasEvent($oEvent->getID())}
		{include file=$oView->getTemplateFile('eventListDetail', '/admin/eventadmin/eventManager') oEvent=$oEvent}
	{/if}
{/foreach}