<h3><a href="#">{t}Movie Properties{/t}</a></h3>
<div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Uploaders IP{/t}</th>
				<td>
					<input type="hidden" name="Data[{mofilmDataname::DATA_USER_IP}]" value="{$oMovie->getDataSet()->getProperty(mofilmDataname::DATA_USER_IP)}" />
					{$oMovie->getDataSet()->getProperty(mofilmDataname::DATA_USER_IP)}
					{if $oMovie->getDataSet()->getUserCountryCode()}
						<img src="/themes/shared/flags/{$oMovie->getDataSet()->getUserCountryCode()}.png" alt="{$oMovie->getDataSet()->getUserCountryCode()}" title="{$oMovie->getDataSet()->getUserCountryCode()}" class="alignMiddle" />
					{/if}
				</td>
			</tr>
		</tbody>
	</table>
</div>