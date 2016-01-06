{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden">
		<input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" />
		<input type="hidden" name="DateModified" value="{$oObject->getDateModified()}" />
	</div>
	
	<p>
		<strong>{t}Please note:{/t}</strong><br />
		{t}Updates to files can only be made once the file record has been created.{/t}<br />
		{t}If uploading a file for the first time, the filename will be automatically completed for you, however you must reload the page to see the changes.{/t}
	</p>
	
	<div id="userFormAccordion">
		<h3><a href="#">{t}Basic Details{/t}</a></h3>
		<div>
			<table class="data">
				<tbody>
					<tr>
						<th>{t}Description{/t}</th>
						<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="string" /></td>
					</tr>
					<tr>
						<th>{t}Filetype{/t}</th>
						<td>
							<select name="Filetype" size="1">
								<option value="">Not selected</option>
								{foreach $fileTypes as $fType}
								<option value="{$fType}" {if $fType == $oObject->getFiletype()}selected="selected"{/if}>{$fType|capitalize}</option>
								{/foreach}
							</select>
						</td>
					</tr>
					<tr>
						<th>{t}Filename{/t}</th>
						<td><input type="text" name="Filename" value="{$oObject->getFilename()}" class="long" /></td>
					</tr>
					<tr>
						<th>{t}Language{/t}</th>
						<td>
							{languageSelect name='Language' useISO=true selected=$oObject->getLang()}
						</td>
					</tr>
					{if $oController->getAction() == 'editObject'}
					<tr>
						<th>{t}Re-upload File{/t}</th>
						<td><input type="file" id="FileUpload" name="Files" class="long" /></td>
					</tr>
					{/if}
					<tr>
						<th>{t}Last Modified{/t}</th>
						<td>{$oObject->getDateModified()}</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<h3><a href="#">{t}Extended Properties{/t}</a></h3>
		<div>
			<table class="data">
				<thead>
					<tr>
						<th>{t}Property{/t}</th>
						<th>{t}Value{/t}</th>
					</tr>
				</thead>
				<tbody>
					{foreach $oObject->getParamSet() as $paramName => $paramValue}
					<tr>
						<th>{$paramName}</th>
						<td><input type="text" name="Properties[{$paramName}]" value="{$paramValue}" class="string" /></td>
					</tr>
					{/foreach}
					<tr>
						<th><input type="text" name="NewProperty[Name]" value="" class="" /></th>
						<td><input type="text" name="NewProperty[Value]" value="" class="string" /></td>
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
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}