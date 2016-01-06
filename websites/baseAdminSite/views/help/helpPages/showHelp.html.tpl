<div id="help">
	<div class="container">
		<div class="floatLeft main">
			<div class="content">
				<h2>{t}Help - {$oHelpPage->getTitle()|xmlstring}{/t}</h2>
				{$oHelpPage->getContent()}

				{if !$oHelpPage->getID() && $oController->hasAuthority('helpPagesController.newObject') && $oController->hasAuthority('helpPagesController.doNewObject')}
					{assign var=oHelpMap value=$oModel->getMapPath($oHelpPage->getReference())}
					<p><a href="/help/helpPages/newObject?Reference={$oHelpPage->getReference()|xmlstring}&amp;Title={$oHelpMap->getDescription()|xmlstring} Controller">{t}Why not create a new help page?{/t}</a></p>
				{/if}
			</div>
		</div>
		<div class="floatLeft sideBar">
			<div class="title">
				<h3>{t}Related Help Pages{/t}</h3>
				<ul style="margin: 0; padding-left: 15px;">
				{foreach $oHelpPage->getRelatedSet() as $oRelatedPage}
					<li><a href="/help/helpPages/showHelp?height=650&amp;width=850&amp;Reference={$oRelatedPage->getReference()}" class="thickbox">{$oRelatedPage->getTitle()|xmlstring}</a></li>
				{/foreach}
				</ul>
			</div>
			
			<br />

			<div class="title">
				<h3>{t}Tagged With:{/t}</h3>
				<ul style="margin: 0; padding-left: 15px;">
				{foreach $oHelpPage->getTagSet() as $oTag}
				    <li><a href="/help/helpPages/showHelpSelection?height=650&amp;width=850&amp;tagID={$oTag->getID()}&amp;tagName={$oTag->getTag()|xmlstring}" class="thickbox">{$oTag->getTag()|xmlstring}</a></li>
				{/foreach}
				</ul>
			</div>
		</div>
	</div>
</div>