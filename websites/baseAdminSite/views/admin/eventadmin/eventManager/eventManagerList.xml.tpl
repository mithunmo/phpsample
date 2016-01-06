{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=30}
{assign var=CorporateID value=$oController->getSearchParameter('CorporateID')}
{assign var=BrandID value=$oController->getSearchParameter('BrandID')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, '','',$CorporateID,$BrandID)}
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
		<event>
			<id>0</id>
			<name>Any Project</name>
		</event>
	{if $objects->getArrayCount() > 0}
		{foreach $objects as $oObject}
		<event>
			<id>{$oObject->getID()}</id>
			<name>{$oObject->getName()|xmlstring}</name>
		</event>
		{/foreach}
	{/if}
	</data>
</mofilm>