<div id="help">
	<div class="container">
		<h2>{t}Help Topics result for Tag : {$helpTag}{/t}</h2>
	</div>
	<div class="floatLeft main">
		<ul style="margin: 0; padding-left: 15px;">
			{foreach $oHelpPageSelections as $oHelpPageSelection}
			    <li><a href="/help/helpPages/showHelp?height=650&amp;width=850&amp;Reference={$oHelpPageSelection->getReference()}" class="thickbox">{$oHelpPageSelection->getTitle()|xmlstring}</a></li>
			{/foreach}
		</ul>
	</div>
</div>