{if is_object($oMotd) && !$oUser->hasReadMotd($oMotd->getMotdID())}
	<div class="motd">
		<div class="closeable">
			<a href="/account/motd/{$oMotd->getMotdID()}" title="{t}Mark MOTD as Read{/t}" class="markAsRead"><img src="{$themeimages}/messagebox/close.png" alt="X" /></a>
		</div>
		<div class="label">
			{t}Message of the Day{/t}<br />
			{$oMotd->getCreateDate()|date_format:'%d %b %Y'}
		</div>
		<h3>{$oMotd->getTitle()|xmlstring}</h3>
		{$oMotd->getContent()}
	</div>
{/if}