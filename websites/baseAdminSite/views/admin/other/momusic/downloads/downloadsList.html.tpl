{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">{t}Downloads{/t}</th>
				<th>{t}Track ID{/t}</th>
				<th>{t}Track Name{/t}</th>
				<th>{t}Music Source{/t}</th>
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
				<td>{$oObject->getArrayValue("downloads")}</td>
				<td>{$oObject->getArrayValue("trackID")}</td>
				<td>{$oObject->getArrayValue("trackName")}</td>
				<td>{$oObject->getArrayValue("musicSource")}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
