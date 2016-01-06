{strip}
array(
	{if $oField->getPhpType() == 'string' && $oField->getSize() > 0}
		'min' => 1,
		'max' => {$oField->getSize()},
	{/if}
	{if count($oField->getValues()) > 0}
		'values' => array({foreach $oField->getValues() as $key => $value}self::{$oField->getField()|upper}_{$value|upper|regex_replace:'/\W/':'_'}{if !$value@last}, {/if}{/foreach}),
	{/if}
)
{/strip}