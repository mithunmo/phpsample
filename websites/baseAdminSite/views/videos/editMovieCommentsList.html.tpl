<table class="data" id="movieCommentHistory">
	<thead>
		<tr>
			<th style="width: 120px;">{t}Reviewer{/t}</th>
			<th>{t}Comments{/t}</th>
		</tr>
	</thead>
	<tbody>
		{if $oMovie->getCommentSet()->getCount() > 0}
			{foreach $oMovie->getCommentSet() as $oComment}
			<tr class="{cycle values='alt,'}">
				<td class="valignTop">{$oComment->getUser()->getFullname()|xmlstring}</td>
				<td class="valignTop">
					<em>{t}Posted on:{/t} {$oComment->getCreateDate()|date_format:'%d/%m/%Y @ %H:%M'}</em><br />
					{$oComment->getComment()|xmlstring|nl2br}
				</td>
			</tr>
			{/foreach}
		{else}
			<tr>
				<td colspan="2">{t}There are no comments yet for this video.{/t}</td>
			</tr>
		{/if}
	</tbody>
</table>