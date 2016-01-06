<table class="data" id="movieAwardsHistory">
	<thead>
		<tr>
			<th style="width: 80px;">{t}Year{/t}</th>
			<th style="width: 100px;">{t}Type{/t}</th>
			<th style="width: 80px;">{t}Placed{/t}</th>
			<th>{t}Event{/t}</th>
		</tr>
	</thead>
	<tbody>
		{if $oMovie->getAwardSet()->getCount() > 0}
			{foreach $oMovie->getAwardSet() as $oAward}
			<tr class="{cycle values='alt,'}">
				<td class="valignTop">{$oAward->getEventYear()}</td>
				<td class="valignTop">{$oAward->getType()}</td>
				<td class="valignTop">{$oAward->getEventPosition()}</td>
				<td class="valignTop">{$oAward->getEventDescription()}</td>
			</tr>
			{/foreach}
		{else}
			<tr>
				<td colspan="4">{t}There are no awards linked to this video.{/t}</td>
			</tr>
		{/if}
	</tbody>
</table>