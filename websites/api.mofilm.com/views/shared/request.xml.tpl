{if strpos($oController, 'getRequestArgs') !== false && $oController->getRequestArgs()->getArrayCount() > 0}
	<request>
		<uri>{$oRequest->getRequestUri()}</uri>
{foreach $oController->getRequestArgs() as $arg => $value}
{if $value && strlen($value) > 0}
{if strtolower($arg) == 'password'}
		<{$arg|xmlstring}>{assign var=len value=$value|strlen}{string_repeat char='x' repeat=$len}</{$arg|xmlstring}>
{else}
		<{$arg|xmlstring}>{$value|xmlstring}</{$arg|xmlstring}>
{/if}
{/if}
{/foreach}
	</request>
{/if}