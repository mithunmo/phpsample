{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<p>{t}You cannot edit or create queue items. Please use the report system.{/t}</p>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}