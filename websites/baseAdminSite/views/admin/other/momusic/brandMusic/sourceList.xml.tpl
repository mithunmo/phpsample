{assign var=eventID value=$oModel->getEventID()}
{assign var=objects value=$oModel->getEventSources()}
{assign var=totalObjects value=$objects->getCount()}
<mofilm>
	<request>
		<totalResults>{$totalObjects}</totalResults>
		<resultCount>{$objects->getCount()}</resultCount>
		<eventID>{$eventID}</eventID>
	</request>
	<data>
		<source>
			<id></id>
			<name>Select Source</name>
		</source>
	{if $objects->getCount() > 0}
		{foreach $objects as $oObject}
		{if $oObject->getStatus() == "open" || $oObject->getEventID() == 4 || $oObject->getEventID() == 21 || $oObject->getEventID() == 22 || $oObject->getEventID() == 47}	
		<source>
			<id>{$oObject->getID()}</id>
			<name>{$oObject->getName()|xmlstring}</name>
		</source>
		{/if}
		{/foreach}
	{/if}
	</data>
</mofilm>