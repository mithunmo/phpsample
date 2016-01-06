{assign var=objects value=$oModel->getValidLicense()}
{assign var=total value=$objects->getArrayCount()}
<mofilm>
	<count>{$total} licenses found</count>	
	{foreach $objects as $oObject}
		<data>
			<license>{$oObject->getLicenseID()}</license>
			<trackName>{$oObject->getTrackName()|xmlstring}</trackName>
			<status>{$oObject->getStatus()}</status>
			<source>{$oObject->getMusicSource()}</source>
		</data>	
	{/foreach}
</mofilm>