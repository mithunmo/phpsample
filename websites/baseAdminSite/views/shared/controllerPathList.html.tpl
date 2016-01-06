{if $path}
	{foreach name=path item=oPath from=$path}
		<div class="title"><a href="{$oPath->getUriPath()}" title="{$oPath->getDescription()}"><img src="{$themeicons}/32x32/{$oPath->getName()}.png" alt="{$oPath->getDescription()}" class="icon" /></a> {$oPath->getDescription()}</div>
		<div class="module">
			{if $oPath->hasSubControllers()}
				{foreach item=oMap from=$oPath->getSubControllers()}
				{if $oUser->isAuthorised($oMap->getName()|cat:"Controller.view")}
				<p><a href="{$oMap->getUriPath()}" title="{$oMap->getDescription()}"><img src="{$themeicons}/32x32/{$oMap->getName()}.png" alt="{$oMap->getDescription()}" class="icon" /></a> <a href="{$oMap->getUriPath()}">{$oMap->getDescription()}</a></p>
				{/if}
				{/foreach}
			{/if}
		</div>
	{/foreach}
{/if}