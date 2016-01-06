{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<div id="userFormAccordion">
		<h3><a href="#">Territory Details</a></h3>
		<div>
			<table class="data">
				<tbody>
					<tr>
						<th>{t}Country{/t}</th>
						<td><input type="text" name="Country" value="{$oObject->getCountry()}" class="string" /></td>
					</tr>
					<tr>
						<th>{t}Short Name{/t}</th>
						<td><input type="text" name="ShortName" value="{$oObject->getShortName()}" class="short" /></td>
					</tr>
					<tr>
						<th class="valignTop">{t}Languages{/t}</th>
						<td>{languageSelect name='Languages[]' selected=$oObject->getLanguageSet()->getObjectIDs() size='10' multiple='multiple'}</td>
					</tr>
				</tbody>
			</table>
		</div>
	
		<h3><a href="#">Counties / States / Provinces</a></h3>
		<div>
			<table id="stateData" class="data">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Abbreviation</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					{foreach $oObject->getStateSet() as $oState}
					<tr>
						<td><input type="hidden" name="States[{$index = $oState@iteration}{$index}][ID]" value="{$oState->getID()}" /><span class="recordNumber">{$index}</span></td>
						<td><input type="text" class="stateName long" name="States[{$index}][Name]" value="{$oState->getDescription()}" /></td>
						<td><input type="text" class="stateAbbr short" name="States[{$index}][Abbr]" value="{$oState->getAbbreviation()}" /></td>
						<td><input type="checkbox" name="States[{$index}][Remove]" value="1" class="addRemoveControl" /></td>
					</tr>
					{/foreach}
					<tr>
						<td><input type="hidden" name="States[{$index+1}][ID]" value="0" /><span class="recordNumber">{$index+1}</span></td>
						<td><input type="text" class="stateName long" name="States[{$index+1}][Name]" value="" /></td>
						<td><input type="text" class="stateAbbr short" name="States[{$index+1}][Abbr]" value="" /></td>
						<td><input type="checkbox" name="States[{$index+1}][Remove]" value="1" class="addRemoveControl" title="{t}Tick box to remove state{/t}" /></td>
					</tr>
				</tbody>
			</table>
			<div class="controls hidden">
				<div class="floatRight">
					<div class="addState formIcon ui-state-default floatLeft" title="{t}Add New State{/t}"><span class="ui-icon ui-icon-plusthick"></span></div>
					<div class="removeState formIcon ui-state-default floatLeft" title="{t}Remove Last State{/t}"><span class="ui-icon ui-icon-minusthick"></span></div>
				</div>
				<div class="clearBoth"></div>
			</div>
		</div>
	</div>
	
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}