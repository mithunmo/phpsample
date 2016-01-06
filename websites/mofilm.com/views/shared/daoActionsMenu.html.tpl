{if $actions->getItemCount() > 0}
	{foreach $actions->getItem() as $oItem}
		<div class="daoAction">
			{if $oItem->getSendFormData()}
			<button type="{if $oItem->getUriPath() == 'reset'}reset{else}submit{/if}" name="{$oItem->getLabel()}" value="{$oItem->getLabel()}">
				<img src="{$themeicons}/32x32/{$oItem->getActionImage()}.png" alt="{$oItem->getDescription()}" class="icon" />
				{$oItem->getLabel()}
			</button>
			{else}
			<a href="{$oItem->getUriPath()}" title="{$oItem->getDescription()}">
				<img src="{$themeicons}/32x32/{$oItem->getActionImage()}.png" alt="{$oItem->getDescription()}" class="icon" />
				{$oItem->getLabel()}
			</a>
			{/if}
		</div>
	{/foreach}
{/if}