{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
<form id="ajaxFormData" name="ajaxFormData" method="post" action="">
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>ID</th>
				<td><input type="text" name="ID" value="{$oObject->getID()}" /></td>
			</tr>
			<tr>
				<th>User ID</th>
				<td><input type="text" name="UserID" value="{$oObject->getUserID()}" /></td>
			</tr>
			<tr>
				<th>Grant ID</th>
				<td><input type="text" name="GrantID" value="{$oObject->getGrantID()}" /></td>
			</tr>
			<tr>
				<th>Movie ID</th>
				<td><input type="text" name="MovieID" value="{$oObject->getMovieID()}" /></td>
			</tr>
			<tr>
				<th>Film Concept</th>
				<td><input type="text" name="FilmConcept" value="{$oObject->getFilmConcept()}" /></td>
			</tr>
			<tr>
				<th>Film Title</th>
				<td><input type="text" name="FilmTitle" value="{$oObject->getFilmTitle()}" /></td>
			</tr>
			<tr>
				<th>Duration</th>
				<td><input type="text" name="Duration" value="{$oObject->getDuration()}" /></td>
			</tr>
			<tr>
				<th>Usage Of Grants</th>
				<td><input type="text" name="UsageOfGrants" value="{$oObject->getUsageOfGrants()}" /></td>
			</tr>
			<tr>
				<th>Requested Amount</th>
				<td><input type="text" name="RequestedAmount" value="{$oObject->getRequestedAmount()}" /></td>
			</tr>
			<tr>
				<th>Script</th>
				<td><input type="text" name="Script" value="{$oObject->getScript()}" /></td>
			</tr>
			<tr>
				<th>Moderated</th>
				<td><input type="text" name="Moderated" value="{$oObject->getModerated()}" /></td>
			</tr>
			<tr>
				<th>Moderator ID</th>
				<td><input type="text" name="ModeratorID" value="{$oObject->getModeratorID()}" /></td>
			</tr>
			<tr>
				<th>Moderator Comments</th>
				<td><input type="text" name="ModeratorComments" value="{$oObject->getModeratorComments()}" /></td>
			</tr>
			<tr>
				<th>Moderation Status</th>
				<td><input type="text" name="ModerationStatus" value="{$oObject->getModerationStatus()}" /></td>
			</tr>
			<tr>
				<th>Moderated Grants</th>
				<td><input type="text" name="ModeratedGrants" value="{$oObject->getModeratedGrants()}" /></td>
			</tr>
		</tbody>
	</table>
</form>
{elseif $oController->getAction() == 'deleteObject'}
<p>Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?</p>
{/if}