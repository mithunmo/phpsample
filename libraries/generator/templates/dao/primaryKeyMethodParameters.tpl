{strip}
{if count($primaryKey) > 0}
	{foreach $primaryKey as $seqID => $field}
		$in{$textUtil->CamelText($field->getColumnName())}
		{if $setNull == true} = null{/if}
		{if !$field@last}, {/if}
	{/foreach}
{/if}
{/strip}