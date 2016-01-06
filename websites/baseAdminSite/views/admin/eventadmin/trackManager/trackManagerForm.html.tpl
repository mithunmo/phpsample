{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden">
		<input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" />
		<input type="hidden" name="Filetype" value="music" />
	</div>
	
	<div id="userFormAccordion">
		<h3><a href="#">{t}Track Details{/t}</a></h3>
		<div>
			<table class="data">
				<tbody>
					<tr>
						<th>{t}Description{/t}</th>
						<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="string" /></td>
					</tr>
					<tr>
						<th>{t}Filetype{/t}</th>
						<td>Music</td>
					</tr>
					<tr>
						<th>{t}Filename{/t}</th>
						<td><input type="text" name="Filename" value="{$oObject->getFilename()}" class="long" /></td>
					</tr>
					<tr>
						<th>{t}Artist{/t}</th>
						<td><input type="text" name="Artist" value="{$oObject->getArtist()}" class="long" /></td>
					</tr>
					<tr>
						<th>{t}Title{/t}</th>
						<td><input type="text" name="Title" value="{$oObject->getTitle()}" class="long" /></td>
					</tr>
					<tr>
						<th>{t}Supplier{/t}</th>
						<td>{supplierSelect name='SupplierID' selected=$oObject->getSupplierID()}</td>
					</tr>
					<tr>
						<th>{t}External Reference{/t}</th>
						<td><input type="text" name="ExternalReference" value="{$oObject->getExternalReference()}" class="string" /></td>
					</tr>
					<tr>
						<th>{t}Digital ISRC{/t}</th>
						<td><input type="text" name="DigitalISRC" value="{$oObject->getDigitalISRC()}" class="string" /></td>
					</tr>
					<tr>
						<th>{t}Created On{/t}</th>
						<td>{$oObject->getCreateDate()}</td>
					</tr>
					<tr>
						<th>{t}Updated On{/t}</th>
						<td>{$oObject->getUpdateDate()}</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<h3><a href="#">{t}Source Mappings{/t}</a></h3>
		<div>
			<div>
				{sourceSelect name='Sources[]' selected=$oObject->getSourceSet()->getObjectIDs() size=15 multiple="multiple"}
			</div>
		</div>
	</div>
	
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getTitle()}&quot;?{/t}</p>
{/if}