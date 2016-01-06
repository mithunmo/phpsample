<h3><a href="#">{t}Movie Music Selection{/t}</a></h3>
<div>
	<table class="data">
		<tbody>
			{if $oMovie->getSource()->getTrackSet()->getCount() > 0}
			<tr>
				<th>{t}Music Track{/t}</th>
				<td>
					{sourceTrackSelect name="TrackID" sourceID=$oMovie->getSource() selected=$oMovie->getTrackSet()->getObjectIDs() class="string"}
				</td>
			</tr>
			{/if}
			<tr>
				<th>{t}Alt. Track Source{/t}</th>
				<td><input type="text" name="Data[{mofilmDataname::DATA_OTHER_TRACK_SOURCE}]" value="{$oMovie->getDataSet()->getProperty(mofilmDataname::DATA_OTHER_TRACK_SOURCE)}" class="long" /></td>
			</tr>
		</tbody>
	</table>
</div>