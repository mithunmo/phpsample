{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}"/></div>
<table class="data">
	<tbody>
	<tr>
		<th>{t}Newsletter Name{/t}</th>
		<td><input type="text" name="Name" value="{$oObject->getName()}" class="long" /></td>
	</tr>
	<tr>
		<th>{t}Outbound Type{/t}</th>
		<td>
			<input type="hidden" name="OutboundTypeID" value="2" />
			Email
		</td>
	</tr>
	<tr>
		<th>{t}Language{/t}</th>
		<td>{languageSelect name='Language' useISO=true selected=$oObject->getLanguage()}</td>
	</tr>
	<tr>
		<th>{t}Message Subject{/t}</th>
		<td><input type="text" name="Messageheader" value="{$oObject->getMessageSubject()}" class="long" /></td>
	</tr>
	<tr>
		<th>{t}Newsletter Template{/t}</th>
		<td>
			<select id ="nlTemplate" value="">
				<option>{t}Select the template>{/t}</option>
				{foreach $nlTemplate as $fileall}
					<option value="{$fileall->getID()}"> {$fileall->getName()} </option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<th>{t}Message Body{/t} </th>
		<td>
			<textarea id="newsletterMessageBody" name="MessageBody" rows="15" cols="80" style="width: 75%;height: 500px" class="tinymce">{$oObject->getMessageBody()|escape:'htmlall':'UTF-8'}</textarea>
		</td>
	</tr>
	<tr>
		<th>{t}Message Body Plain Text{/t}</th>
		<td>
			<textarea name="MessageText" id="newsletterMessageText" rows="15" cols="80" >{$oObject->getMessageText()}</textarea>
		</td>
	</tr>
	<tr>
		<th>{t}Is Newsletter HTML?{/t}</th>
		<td>{booleanSelect name="Ishtml" selected=$oObject->getIsHtml() true="Yes" false="No"}</td>
	</tr>
	<tr>
		<th>{t}Newsletter Type{/t}</th>
		<td>
			<select name ="NewsletterType" value="">
				<option value="1">{t}Marketing Newsletter{/t}</option>
				<option value="2">{t}Shortlisted{/t}</option>
				<option value="3">{t}Not Shortlisted{/t}</option>
				<option value="4">{t}Non Winners{/t}</option>
			</select>
		</td>
	</tr>

	</tbody>
</table>
{elseif $oController->getAction() == 'deleteObject'}
<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}