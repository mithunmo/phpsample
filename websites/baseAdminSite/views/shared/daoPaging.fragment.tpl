{if $totalObjects >= $limit && $offset >= 0}
	<tr>
		<td class="alignLeft">
			{if $offset > 0}
			<a href="{$daoUriView}?Offset=0&amp;Limit={$limit}{if $daoSearchQuery}&amp;{$daoSearchQuery}{/if}" title="{t}First Page{/t}"><img src="{$themeicons}/32x32/result-set-first.png" alt="{t}First Page{/t}" class="icon" /></a>
			{if ($offset-$limit) >= 0}
			<a href="{$daoUriView}?Offset={$offset-$limit}&amp;Limit={$limit}{if $daoSearchQuery}&amp;{$daoSearchQuery}{/if}" title="{t}Previous{/t}"><img src="{$themeicons}/32x32/result-set-previous.png" alt="{t}Previous{/t}" class="icon" /></a>
			{/if}
			{else}
			<img src="{$themeicons}/32x32/result-set-first-disabled.png" alt="{t}First Page{/t}" class="icon" />
			<img src="{$themeicons}/32x32/result-set-previous-disabled.png" alt="{t}Previous{/t}" class="icon" />
			{/if}
		</td>
		<td colspan="{$colspan}" class="alignCenter">{t}Showing {$offset+1} to {if $offset+$limit < $totalObjects}{$offset+$limit}{else}{$totalObjects}{/if} of {$totalObjects} objects{/t}</td>
		<td class="alignRight">
			{if $offset+$limit < $totalObjects}
			<a href="{$daoUriView}?Offset={$offset+$limit}&amp;Limit={$limit}{if $daoSearchQuery}&amp;{$daoSearchQuery}{/if}" title="{t}Next{/t}"><img src="{$themeicons}/32x32/result-set-next.png" alt="{t}Next{/t}" class="icon" /></a>
			<a href="{$daoUriView}?Offset={$oModel->getLastPageOffset($limit)}&amp;Limit={$limit}{if $daoSearchQuery}&amp;{$daoSearchQuery}{/if}" title="{t}Last Page{/t}"><img src="{$themeicons}/32x32/result-set-last.png" alt="{t}Last Page{/t}" class="icon" /></a>
			{else}
			<img src="{$themeicons}/32x32/result-set-next-disabled.png" alt="{t}Next{/t}" class="icon" />
			<img src="{$themeicons}/32x32/result-set-last-disabled.png" alt="{t}Last Page{/t}" class="icon" />
			{/if}
		</td>
	</tr>
{/if}