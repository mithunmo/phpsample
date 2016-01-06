{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=movieID value=$oController->getSearchParameter('MovieID')}
{assign var=type value=$oController->getSearchParameter('Type')}
{assign var=filename value=$oController->getSearchParameter('Filename')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $movieID, $type, $filename)}
{assign var=totalObjects value=$oModel->getTotalObjects()}

<div class="filters">
	<input type="text" name="MovieID" value="{$movieID|default:'MovieID'}" class="integer" title="{t}Search by Movie ID{/t}" onfocus="this.select()" />
	<select name="Type" size="1">
		<option>{t}Any Type{/t}</option>
		{foreach mofilmMovieAsset::getTypes() as $cType}
		<option value="{$cType}" {if $cType == $type}selected="selected"{/if}>{$cType}</option>
		{/foreach}
	</select>
	<input type="text" name="Filename" value="{$filename|default:'Search by filename'}" class="medium" title="{t}Search by Filename{/t}" onfocus="this.select()" />
</div>

{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first"></th>
				<th>{t}Movie{/t}</th>
				<th>{t}Type{/t}</th>
				<th>{t}Filename{/t}</th>
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
				<td>{$oObject@iteration+$offset}</td>
				<td><a href="{adminMovieLink movieID=$oObject->getMovieID()}" title="View Movie">{$oObject->getMovieID()}</a></td>
				<td>{$oObject->getType()}</td>
				<td>{$oObject->getFileBasename()}</td>
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
