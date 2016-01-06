{assign var=objects value=$oModel->solrSearch()}
{assign var=totalObjects value=$objects->getTotalResults()}
<mofilm>
	
	<request>
		<totalResults>{$totalObjects}</totalResults>
		<count>{$objects->getTotalResults()}</count>
		<resultCount>{$objects->getTotalResults()}</resultCount>
		<offset>{$objects->getSearchInterface()->getStart()|default:0}</offset>
		<limit>10</limit>
		<eventID>cc</eventID>
	</request>
	<data>
	{if $totalObjects > 0}
		{foreach $objects as $oObject}
		<source>
			<id>{$oObject->s_id}</id>
			<name>{htmlspecialchars($oObject->s_track)}</name>
			<url>{htmlspecialchars($oObject->s_url)}</url>
			<aname>{htmlspecialchars($oObject->s_artist)}</aname>
			<duration>{$oObject->s_duration}</duration>
			<description>{htmlspecialchars($oObject->s_description)}</description>
                        <musicsource>{$oObject->s_source}</musicsource>
		</source>
		{/foreach}
	{/if}
	</data>
</mofilm>