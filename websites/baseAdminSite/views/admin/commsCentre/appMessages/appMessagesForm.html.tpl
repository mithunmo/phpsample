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
			<tr>
				<th>{t}Outbound Type{/t}</th>
				<td>
					<select name="OutboundTypeID" size="1">
						{foreach $types as $oType}
						<option value="{$oType->getOutboundTypeID()}" {if $oType->getOutboundTypeID() == $oObject->getOutboundTypeID()}selected="selected"{/if}>{$oType->getDescription()}</option>
						{/foreach}
					</select>
				</td>
			</tr>
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
					</select>
				</td>
			</tr>

			<tr>
				<th>{t}Message Body{/t}</th>
				<td><textarea name="MessageBody" id="MessageBodyId" rows="10" cols="50" class="tinymce">{$oObject->getMessageBody()|escape:'htmlall':'UTF-8'}</textarea></td>
			</tr>
			<tr>
				<th>{t}HTML?{/t}</th>
				<td>{booleanSelect name='IsHtml' selected=$oObject->getIsHtml() true='Yes' false='No'}</td>
			</tr>
			<tr>
				<th>{t}Currency{/t}</th>
				<td>{currencySelect name='CurrencyID' selected=$oObject->getCurrencyID()}</td>
			</tr>
			<tr>
				<th>{t}Charge{/t}</th>
				<td><input type="text" name="Charge" value="{$oObject->getCharge()}" /></td>
			</tr>
			<tr>
				<th>{t}Delay{/t}</th>
				<td><input type="text" name="Delay" value="{$oObject->getDelay()}" class="number" /></td>
			</tr>
			<tr>
				<th>{t}Message Order{/t}</th>
				<td><input type="text" name="MessageOrder" value="{$oObject->getMessageOrder()}" class="number" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}