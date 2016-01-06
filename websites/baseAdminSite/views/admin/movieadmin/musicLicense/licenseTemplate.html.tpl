<table class="data">
	<thead>
		<tr>
			<th style="width: 100px;">{t}License ID{/t}</th>
			<th style="width: 100px;">{t}Track Name{/t}</th>
			<th style="width: 80px;">{t}Status{/t}</th>
			<th style="width: 40px;">{t}Source{/t}</th>
		</tr>	
	</thead>	
	<tbody id="licenseContent">
		{if is_object($oLicenseSet)}
			{foreach $oLicenseSet as $oLicense}
				<tr>
					<td>{$oLicense->getLicenseID()}</td>
					<td>{$oLicense->getTrackName()}</td>
					<td>{if $oLicense->isValidLicense()}Valid{else}Expired{/if}</td>
					
				</tr>
			{/foreach}
		{/if}
	</tbody>	
</table>			
