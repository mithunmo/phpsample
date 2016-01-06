{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Apply Grants for :: {/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
	<div class="container">
		{if $oGrants->getID() > 0}
		<div class="floatLeft accountDetails">
			<h2>
				<div>{t}Apply Grants For - {$oSource->getEvent()->getName()} : {$oSource->getName()}{/t}</div>
			</h2>
			<div class="content">
				<form id="grantsApplyForm" class="userGrantsApplyForm" name="userGrantsForm" method="post" action="/account/grants/doApply">
					<div class="formFieldContainer">
						<h4>Please describe the concept of your film</h4>
						<p><textarea class="long required string" name="FilmConcept" cols="70" rows="5" /></textarea></p>
					</div>
					<div class="formFieldContainer">
						<h4>Title of your film</h4>
						<p><input class="long required string" type="text" name="FilmTitle" value="" /></p>
					</div>
					<div class="formFieldContainer">
						<h4>Duration</h4>
						<p><input class="small" type="text" name="Duration" value="" /></p>
					</div>
					<div class="formFieldContainer">
						<h4>Proposed use of grant funding</h4>
						<p><textarea class="long required string" name="UsageOfGrants" cols="70" rows="5" /></textarea></p>
					</div>
					<div class="formFieldContainer">
						<h4>Requested amount</h4>
						<p><b>{$oGrants->getCurrencySymbol()} </b> <input class="small required" type="text" name="RequestedAmount" value="" /></p>
					</div>
					<div class="formFieldContainer">
						<h4>If you have a script or other supporting information then please include it here </h4>
						<p><textarea class="long required string" name="Script" cols="70" rows="10" /></textarea></p>
					</div>
					<div>
						<input type="hidden" name="GrantID" value="{$oGrants->getID()}" />
						<input type="submit" name="submit" class="submit" value="Submit" />
					</div>
				</form>
			</div>
		</div>
		<div class="floatLeft accountStats">
			<div class="grantsLogoDisplay">
				<div style="display:inline"><img src="/resources/client/events/{$oSource->getEvent()->getLogoName()}.jpg" width="100" height="55" alt="{$oSource->getEvent()->getName()}" /></div>
				<div style="display:inline"><img src="/resources/client/sources/{$oSource->getLogoName()}.jpg" width="100" height="55" alt="{$oSource->getName()}" /></div>
			</div>
			<div class="">
				<p>{$oGrants->getDescription()}</p>
			</div>
		</div>
		{else}
			<h2>
				<div>{t}Grants not available{/t}</div>
			</h2>
		{/if}
		<br class="clearBoth">
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}