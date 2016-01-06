<h3><a href="#">{t}Messages{/t} ({$oMovie->getMessageSet()->getCount()})</a></h3>
<div>
	<table class="data">
		<thead>
			<tr>
				<th style="width: 18px;">&nbsp;</th>
				<th style="width: 80px;">{t}Date{/t}</th>
				<th style="width: 100px;">{t}From{/t}</th>
				<th>{t}Message{/t}</th>
			</tr>
		</thead>
		<tbody>
			{if $oMovie->getMessageSet()->getCount() > 0}
				{foreach $oMovie->getMessageSet() as $oMessage}
				<tr class="{cycle values='alt,'}">
					<td class="valignTop alignCentre">{strip}
						{if $oMessage->getMessage()->getStatus() == 'New'}
							<img src="/themes/shared/icons/star.png" alt="New" title="{t}This message has not been read by the recipient{/t}" class="smallIcon" />
						{/if}
					{/strip}</td>
					<td class="valignTop">{$oMessage->getSendDate()|date_format:'%d/%m/%Y'}</td>
					<td class="valignTop">{$oMessage->getSender()->getFullname()}</td>
					<td class="valignTop">{$oMessage->getMessage()->getMessage()|nl2br}</td>
				</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="4">{t}There are no messages linked to this video.{/t}</td>
				</tr>
			{/if}
		</tbody>
	</table>
</div>