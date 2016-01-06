{if $statusMessage && is_array($statusMessage) && strlen($statusMessage.message) > 1}
	{if $statusMessage['level'] == 0}
		{assign var=level value='success'}
	{elseif $statusMessage['level'] == 1}
		{assign var=level value='critical'}
	{elseif $statusMessage['level'] == 2}
		{assign var=level value='error'}
	{elseif $statusMessage['level'] == 4}
		{assign var=level value='warning'}
	{else}
		{assign var=level value='info'}
	{/if}
	<div class="messageBox {$level}">
		<p>{$statusMessage.message|escape:'htmlall':'UTF-8'}</p>
	</div>
{/if}