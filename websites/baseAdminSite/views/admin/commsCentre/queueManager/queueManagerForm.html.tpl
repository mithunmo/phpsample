{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<p>Message queue items cannot be created or edited - except via Application Request.</p>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}