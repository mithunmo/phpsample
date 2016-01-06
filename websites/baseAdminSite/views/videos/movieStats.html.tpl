<h3>{t}Video Stats{/t}</h3>
<ul class="stats">
	<li>
		{$oStats->getTotalMovies()} <a href="/videos">{t}Total Videos{/t}</a>
		{if $oStats->getOptionsSet()->getCount() > 0}
		<ul>
			{foreach $oStats->getOptionsSet() as $stat => $value}
			<li>
				<span>{$value} <a href="/videos/doSearch?Status={$stat|escape:'url'}">{$stat}</a></span>{if $value@iteration != $oStats->getOptionsSet()->getCount()},{/if}
			</li>
			{/foreach}
		</ul>
		{/if}
	</li>
</ul>