<h3><a href="#">{t}Rating History{/t}</a></h3>
<div>
	<table class="data">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>{t}Reviewer{/t}</th>
				<th>{t}Client / Brand{/t}</th>
				<th>{t}Rating{/t}</th>
			</tr>
		</thead>
		<tbody>
			{if $oMovie->getRatings()->getCount() > 0}
				{foreach $oMovie->getRatings() as $oRating}
				<tr class="{cycle values='alt,'}">
					<td>{$oRating@iteration}</td>
					<td>{$oRating->getUser()->getFullname()|xmlstring}</td>
					<td>{$oRating->getUser()->getClient()->getCompanyName()|xmlstring}</td>
					<td>{$oRating->getRating()}/10</td>
				</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="4">{t}There are no ratings yet for this video.{/t}</td>
				</tr>
			{/if}
		</tbody>
	</table>
</div>