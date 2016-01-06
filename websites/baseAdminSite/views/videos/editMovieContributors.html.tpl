<h3><a href="#">{t}Contributors{/t}</a></h3>
<div>
	<table id="contributors" class="data">
		<thead>
			<tr>
				<th>#</th>
				<th>Contributor Name</th>
				<th>Role</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach $oMovie->getContributorSet() as $oContributorMap}	
			{if $oModel->getValidUser($oContributorMap->getContributor()->getName())}	
			<tr>
				<td><input type="hidden" name="Contributors[{$index = $oContributorMap@iteration}{$index}][ID]" value="{$oContributorMap->getContributor()->getID()}" /><span class="recordNumber">{$index}</span></td>
				<td><input placeholder="name" readonly="readonly" type="text" class="contributorUser string" name="Contributors[{$index}][Name]" value="{$oModel->getUserName($oContributorMap->getContributor()->getName())}" /></td>
				<td><div class="ui-widget"><input type="text" readonly="readonly" class="contributorRole small" name="Contributors[{$index}][Role]" value="{$oContributorMap->getRole()->getDescription()}" /></div></td>
				<td><input type="checkbox" name="Contributors[{$index}][Remove]" value="1"  /></td>
			</tr>
			{/if}
			{/foreach}
			<tr>
				<td><input type="hidden" name="Contributors[{$index+1}][ID]" value="0" /><span class="recordNumber">{$index+1}</span></td>
				<td><input type="text" class="contributorUser string" name="Contributors[{$index+1}][Name]" value="" /></td>
				<td><div class="ui-widget"><input type="text" class="contributorRole small" name="Contributors[{$index+1}][Role]" value="" /></div></td>
				<td><input class="addRemoveControl" type="checkbox" name="Contributors[{$index+1}][Remove]" value="1"  title="{t}Tick box to remove contributor{/t}" /></td>
				<td><input type="hidden" name="Contributors[{$index+1}][roleID]" value="0"</td>
			</tr>
		</tbody>
	</table>
	<div class="controls hidden">
		<div class="floatRight">
			<div class="addContributor formIcon ui-state-default floatLeft" title="{t}Add New Contributor{/t}"><span class="ui-icon ui-icon-plusthick"></span></div>
			<div class="removeContributor formIcon ui-state-default floatLeft" title="{t}Remove Last Contributor{/t}"><span class="ui-icon ui-icon-minusthick"></span></div>
		</div>
		<div class="clearBoth"></div>
	</div>
</div>