{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<div id="userFormAccordion">
		<h3><a href="#">{t}Client Details{/t}</a></h3>
		<div>
			<table class="data">
				<tbody>
					<tr>
						<th>{t}Company Name{/t}</th>
						<td><input type="text" name="CompanyName" value="{$oObject->getCompanyName()}" class="string" /></td>
					</tr>
					{if $oObject->hasLogo()}
					<tr>
						<th>{t}Current Logo{/t}</th>
						<td>
							<em>{$oObject->getLogoName()}</em><br />
							<img src="{$oObject->getLogoWebLocation()}" alt="{$oObject->getCompanyName()}" />
						</td>
					</tr>
					{/if}
					<tr>
						<th>{t}Upload Logo{/t}</th>
						<td>
							<input type="file" name="Logo" class="string" /><br />
							{t}Please note:{/t}<br />
							<em>
								{t}Logos will be uploaded using the company name without any punctuation or special characters.{/t}<br />
								{t}Logos will be resized to 150x150 pixels and converted to JPEG format.{/t} 
							</em>
						</td>
					</tr>
					{if $oObject->getID()}
						<tr>
							<th>{t}Disable all user logins{/t}</th>
							<td><input type="checkbox" name="DisableUsers" value="1" /></td>
						</tr>
					{/if}
				</tbody>
			</table>
		</div>
		
		{if $oController->hasAuthority('clientController.canEditSources')}
			<h3><a href="#">{t}Client Sources{/t}</a></h3>
			<div>
				<table class="data">
					<tbody>
						{foreach $events as $oEvent}
							<tr class="alt">
								<th>{$oEvent->getName()|xmlstring}</th>
								<td>&nbsp;</td>
								<td></td>
							</tr>
							{foreach $oEvent->getSourceSet() as $oSource}
							<tr>
								<td></td>
								<td>{$oSource->getName()|xmlstring}</td>
								<td><input type="checkbox" name="Sources[]" value="{$oSource->getID()}" {if $oObject->getSourceSet()->getObjectByID($oSource->getID())}checked="checked"{/if} /></td>
							</tr>
							{/foreach}
						{/foreach}
					</tbody>
				</table>
			</div>
		{/if}
	</div>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oObject->getCompanyName()}&quot;?{/t}</p>
{/if}