{strip}
{if $oField->getIsNull()}
	null
{elseif is_array($oField->getDefault())}
	array()
{elseif is_float($oField->getDefault())}
	0
	{if $oField->getPrecision() > 0}
		.{string_repeat char=0 repeat=$oField->getPrecision()|default:1}
	{/if}
{elseif is_numeric($oField->getDefault())}
	0
{elseif is_string($oField->getDefault())}
	{if count($oField->getValues()) > 0}
		'{$oField->getDefault()}'
	{elseif $oField->getDefault()}
		{if $oField->getPhpType() == 'date' || $oField->getPhpType() == 'datetime' || $oField->getPhpType() == 'timestamp'}
			new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue())
		{else}
			{if strpos($oField->getDefault(), 'date') !== false}
				{$oField->getDefault()}
			{else}
				'{$oField->getDefault()}'
			{/if}
		{/if}
	{else}
		''
	{/if}
{else}
	false
{/if}
{/strip}