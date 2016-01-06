{assign var=objects value=$oModel->getObjectList()}
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th>{t}Object Methods{/t}</th>
			</tr>
		</thead>
		<tbody>
		{foreach name=list item=oObject from=$objects}
			<tr>
				<td {if $smarty.foreach.list.iteration % 2}class="alt"{/if}>{$oObject}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
