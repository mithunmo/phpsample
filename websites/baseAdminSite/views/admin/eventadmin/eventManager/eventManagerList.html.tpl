{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=active value=$oController->getSearchParameter('Active')}
{assign var=productid value=$oController->getSearchParameter('ProductID')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $active,$productid,'','')}
{assign var=totalObjects value=$oModel->getTotalObjects($active,$productid)}

<div class="filters">
	Only show Active {yesNoSelect name='Active' selected=$active}
        &nbsp;&nbsp;Product   {productDistinctSelect id="productList" name='ProductID' selected=$productid class="valignMiddle " }

</div>

{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">ID</th>
				<th>{t}Event Name{/t}</th>
				<th>{t}Start Date{/t}</th>
				<th>{t}End Date{/t}</th>
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
		</tfoot>
		<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
		{foreach $objects as $oObject}
			<tr class="{cycle values="alt,"}">
				<td>{$oObject->getID()}</td>
				<td>{$oObject->getName()}</td>
				<td>{$oObject->getStartDate()}</td>
				<td>{$oObject->getEndDate()}</td>
				<td class="actions">
					{include file=$oView->getTemplateFile('daoObjectListDefaultActions', '/shared')}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
