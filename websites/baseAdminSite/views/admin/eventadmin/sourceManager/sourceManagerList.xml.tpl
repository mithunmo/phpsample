{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=30}
{assign var=eventID value=$oController->getSearchParameter('EventID')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $eventID)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
<mofilm>
	<request>
		<totalResults>{$totalObjects}</totalResults>
		<resultCount>{$objects->getArrayCount()}</resultCount>
		<eventID>{$eventID}</eventID>
		<offset>{$offset}</offset>
		<limit>{$limit}</limit>
	</request>
	<data>
		<source>
			<id>0</id>
			<name>Select Source</name>
		</source>
	{if $objects->getArrayCount() > 0}
		{foreach $objects as $oObject}
		<source>
			<id>{$oObject->getID()}</id>
			<name>{$oObject->getName()|xmlstring}</name>
		</source>
		{/foreach}
	{/if}
	</data>
</mofilm>