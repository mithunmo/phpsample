{assign var=oSubControllers value=$oController->getSubControllers()}
{if $oSubControllers->getArrayCount() > 0}
	{foreach $oSubControllers as $oCont}
	{if $oController->hasAuthority($oCont->getName()|cat:"Controller.view")}
	<div class="content">
		<div class="title"><a href="{$oCont->getUriPath()}" title="{$oCont->getDescription()}"><img src="{$themeicons}/32x32/{$oCont->getName()}.png" alt="{$oCont->getDescription()}" class="icon" /></a> <a href="{$oCont->getUriPath()}">{$oCont->getDescription()}</a></div>
		<div class="body">
			{if $oCont->getSubControllers()}
				{foreach $oCont->getSubControllers() as $oCont2}
				{if $oController->hasAuthority($oCont2->getName()|cat:"Controller.view")}
				<div class="controller"><a href="{$oCont2->getUriPath()}" title="{$oCont2->getDescription()}"><img src="{$themeicons}/32x32/{$oCont2->getName()}.png" alt="{$oCont2->getDescription()}" class="icon" /></a> <a href="{$oCont2->getUriPath()}">{$oCont2->getDescription()}</a></div>
				{/if}
				{/foreach}
			{/if}
		</div>
		<div class="clearBoth"></div>
	</div>
	{/if}
	{/foreach}
{/if}