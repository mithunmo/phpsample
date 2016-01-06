{assign var=objects value=$oModel->getMovieDetails()}
{assign var=total value=$objects->getCount()}
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