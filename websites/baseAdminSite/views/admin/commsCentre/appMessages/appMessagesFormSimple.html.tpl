{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	{if $oController->getAction() == "editObject"}
		{t}Select Language for translation{/t} {languageSelect id="appLanguage" name='appLanguage' useISO=true selected=$oObject->getLanguage()}
	{/if}
	<div class="hidden">
		<input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" />
		<input type="hidden" name="ApplicationID" value="0" />
	</div>

	<p>
		<strong>{t}Please note:{/t}</strong><br />
		{t}Only e-mail messages may contain HTML content.{/t}<br />
		{t}If the message group is marked (Email), the message type should be Email only.{/t}<br />
		{t}Outbound Type and Message Group are required.{/t}
	</p>

	<table class="data">
		<tbody>
			<input name="OutboundTypeID" type="hidden" value="2">
			<tr>
				<th>{t}Message Group{/t}</th>
				<td>
					<select name="MessageGroupID" size="1" id="appMessageGroupID">
						{foreach $groups as $oGroup}
						<option value="{$oGroup->getMessageGroupID()}" {if $oGroup->getMessageGroupID() == $oObject->getMessageGroupID()}selected="selected"{/if}>{$oGroup->getDescription()} ({$oGroup->getMessageType()})</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th>{t}Language{/t}</th>
				{if $oController->getAction() == "newObject"}
				<td>
					{languageSelect name='Language' useISO=true selected=$oObject->getLanguage()}
				</td>
				{elseif $oController->getAction() == "editObject"}
				<td>
					<input type="text" name="Language" value="{$oObject->getLanguage()}" readonly="readonly">
				</td>
				{/if}
			</tr>
			<tr>
				<th>{t}Message Subject{/t}</th>
				<td><input type="text" name="MessageHeader" value="{$oObject->getMessageHeader()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Properties{/t}</th>
				<td>
					<select id="appMessageDynamic">
						{foreach $objects as $object}
							<option value="{$object}">{$object}</option>>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th>{t}Message Body{/t}</th>
				<td><textarea id="MessageBodyId" name="MessageBody" rows="10" cols="50" class="tinymce">{$oObject->getMessageBody()|escape:'htmlall':'UTF-8'}</textarea></td>
			</tr>
				<input type ="hidden" name='IsHtml' value=1>
				<input type ="hidden" name='CurrencyID' value=0>
    				<input type ="hidden" name='Charge' value=0>
				<input type ="hidden" name='Delay' value=0>
				<input type ="hidden" name='MessageOrder' value=0>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}