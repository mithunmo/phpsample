{assign var=objects value=$oModel->doSearch()}
{assign var=totalObjects value=$objects->getResultCount()}
<mofilm>
	<request>
		<totalResults>{$totalObjects}</totalResults>
		<count>{$objects->getTotalResults()}</count>
		<resultCount>{$objects->getResultCount()}</resultCount>
		<offset>{$objects->getSearchInterface()->getOffset()|default:0}</offset>
		<limit>{$objects->getSearchInterface()->getLimit()}</limit>
		<eventID>cc</eventID>
	</request>
	<data>
	{if $objects->getResultCount() > 0}
		{foreach $objects as $oObject}
		<source>
			<id>{$oObject->getID()}</id>
			<name>{htmlspecialchars($oObject->getTrackName())}</name>
			<url>{htmlspecialchars($oObject->getPath())}</url>
			<aname>{htmlspecialchars($oObject->getArtistID())}</aname>
			<duration>{$oObject->getDuration()}</duration>
			<description>{htmlspecialchars($oObject->getDescription())}</description>
		</source>
		{/foreach}
	{/if}
	</data>
</mofilm>