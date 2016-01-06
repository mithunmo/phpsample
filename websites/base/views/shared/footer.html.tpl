		
		<script type="text/javascript" src="/libraries/core_js/core.js"></script>
		<script type="text/javascript" src="/libraries/jquery/jquery.min.js"></script>
{foreach $oView->getResourcesByType('js') as $oResource}
		{$oResource->toString()}
{/foreach}
		<script type="text/javascript" src="/libraries/mofilm/lang/{$currentLanguage|default:'en'}.js"></script>
		<script type="text/javascript" src="/libraries/mofilm/mofilm.js"></script>
	</body>
</html>