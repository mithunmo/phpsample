{if $messageCount > 0}
    <h3>Unread Messages</h3>
    <p>
		{t}You have <strong>{$messageCount}</strong> new private message{if $messageCount > 1}s{/if}.{/t}
		<a href="/account/pm">{t}Read your messages{/t}</a>
	</p>
{/if}