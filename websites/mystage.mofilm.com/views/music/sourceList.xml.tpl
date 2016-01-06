{assign var=objects value=$oModel->doSearch()}
{assign var=totalObjects value=$objects->getResultCount()}
<mofilm>
	<request>
		<totalResults>{$totalObjects}</totalResults>
		<count>{$objects->getTotalResults()}</count>
		<resultCount>{$objects->getResultCount()}</resultCount>
		<offset>{$objects->getSearchInterface()->getOffset()|default:0}</offset>
		<eventID>cc</eventID>
	</request>
	<data>
	{if $objects->getResultCount() > 0}
		{foreach $objects as $oObject}
		<source>
			<id>{$oObject->getID()}</id>
			<name>{$oObject->getTrackName()}</name>
			<url>{$oObject->getPath()}</url>
			<aname>{$oObject->getArtistID()}</aname>
			<duration>{$oObject->getDuration()}</duration>
		</source>
		{/foreach}
	{/if}
	</data>
</mofilm>