{if $oUser->getFavourites()->isFavourite($oMovie->getID())}
	{strip}<a href="/account/removeFromFavourites/{$oMovie->getID()}" title="{t}Remove from Favourites{/t}" class="removeFromFavourites">
		<img src="{$themeicons}/16x16/bookmark-marked.png" alt="*" title="{t}This movie is in your favourites, click to remove{/t}" class="smallIcon" />
		{if $textLabels}{t}Bookmarked{/t}{/if}
	</a>{/strip}
{else}
	{strip}<a href="/account/addToFavourites/{$oMovie->getID()}" title="{t}Add to Favourites{/t}" class="addToFavourites">
		<img src="{$themeicons}/16x16/bookmark.png" alt="*" title="{t}Add to Favourites{/t}" class="smallIcon" />
		{if $textLabels}&nbsp;{t}Add to Favourites{/t}{/if}
	</a>{/strip}
{/if}