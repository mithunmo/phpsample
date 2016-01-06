{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=30}
{assign var=CorporateID value=$oController->getSearchParameter('CorporateID')}
{assign var=EventID value=$oController->getSearchParameter('EventID')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $CorporateID,$EventID)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
<mofilm>
	<request>
		<totalResults>{$totalObjects}</totalResults>
		<resultCount>{$objects->getArrayCount()}</resultCount>
		<corporateID>{$CorporateID}</corporateID>
		<offset>{$offset}</offset>
		<limit>{$limit}</limit>
	</request>
	<data>
		<brand>
			<id>0</id>
			<name>Select Brands</name>
		</brand>
	{if $objects->getArrayCount() > 0}
		{foreach $objects as $oObject}
		<brand>
			<id>{$oObject->getID()}-{{$oObject->getName()|xmlstring}}</id>
			<name>{$oObject->getName()|xmlstring}</name>
		</brand>
		{/foreach}
	{/if}
	</data>
</mofilm>