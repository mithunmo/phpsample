<h3>{t}User Stats{/t}</h3>
<ul class="stats">
	<li>
		{$oStats->getTotalUsers()} <a href="/users">{t}Total Users{/t}</a>
		{if $oStats->getOptionsSet()->getCount() > 0}
		<ul>
			{foreach $oStats->getOptionsSet() as $stat => $value}
			<li>
				<span><strong>{$value}</strong> {$stat}</span>{if $value@iteration != $oStats->getOptionsSet()->getCount()},{/if}
			</li>
			{/foreach}
		</ul>
		{/if}
	</li>
</ul>