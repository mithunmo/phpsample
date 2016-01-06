{include file='header.tpl'}

<div id="scaffoldBody">
	<h3>Error with form submission</h3>
	<p class="scaffoldError">
		An error occured when processing <strong>{$Action}</strong> on <strong>{$ObjectName}</strong>:<br />
		<em>{$Exception->getMessage()}</em>
	</p>
	<p>
		Please go back and correct this before continuing.
	</p>
	
	<input type="button" value="back" onclick="history.go(-1);" class="scaffoldButton" />
	<input type="button" name="action" value="retrieve" onclick="window.location='{$smarty.server.SCRIPT_NAME}?action=retrieve'" class="scaffoldActionRetrieve" />
</div>

{include file='footer.tpl'}