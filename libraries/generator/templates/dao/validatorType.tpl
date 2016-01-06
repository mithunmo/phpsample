{strip}
	{if $oField->getPhpType() == 'integer'}
		number
	{elseif $oField->getPhpType() == 'float'}
		float
	{elseif $oField->getPhpType() == 'string' && !$oField->getValues()}
		string
	{elseif $oField->getPhpType() == 'string' && $oField->getValues()}
		inArray
	{elseif $oField->getPhpType() == 'boolean'}
		boolean
	{elseif $oField->getPhpType() == 'datetime'}
		dateTime
	{elseif $oField->getPhpType() == 'timestamp'}
		dateTime
	{elseif $oField->getPhpType() == 'date'}
		date
	{elseif $oField->getPhpType() == 'time'}
		time
	{else}
		string
	{/if}
{/strip}