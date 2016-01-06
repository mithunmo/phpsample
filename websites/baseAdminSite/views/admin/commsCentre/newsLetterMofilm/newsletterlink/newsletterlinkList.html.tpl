{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=nlID value=$oController->getSearchParameter('newslettertri')|default:1}
{assign var=objects value=$oModel->getNewsletterLinks($nlID,$offset, $limit)}
{assign var=count value=$objects->getArrayCount()}


<h3>{t}Select the newsletter{/t}</h3>
<select name="newslettertri" id="newslettertrack" onChange="this.form.submit()">
	<option>{t}select the newsletter{/t}</option>
	{foreach $newslettersent as $oNl}
		{if $oNl->getNlid() == $nlID}
			<option value="{$oNl->getNlid()}" selected> {$oNl->getName()} </option>
		{else}
			<option value="{$oNl->getNlid()}"> {$oNl->getName()} </option>
		{/if}
	{/foreach}
</select>
<br>
<br>
	<table class="data">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>{t}Unique Link click count{/t}</th>
				<th>{t}Link Name{/t}</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=1}
		</tfoot>
		<tbody>
	    	{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=1}
    		{foreach $objects as $contact}
			    <tr class="{cycle values="alt,"}">
					<td>{$contact@iteration+$offset}</td>
				{foreach $contact as $key => $value}
				    <td>{$value}</td>
				{/foreach}
			    </tr>
			{/foreach}
		</tbody>
	</table>
