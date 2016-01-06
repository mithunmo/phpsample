{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Review the movie{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
	<div class="container">
		<div class="main">
			<div class="content">
				<b>{t}User Movie Confirmation Process{/t}</b>
				<br />
				{t}If you do not commit it then it is automatically rejected for the event{/t}
			</div>	
			<div class="content">
				<div class="mofilmMovieFrame">
					<div id="mofilmMoviePlayer"></div>
				</div>
			</div>	
			<div class="content">
				<table class="data">
					<tbody>
						<tr>
							<th>{t}Title{/t}</th>
							<td><input type="text" name="Name" value="{$oMovie->getTitle()}" class="long" /></td>
						</tr>
						<tr>
							<th>{t}Video Description{/t}</th>
							<td><textarea name="Description" rows="6" cols="60" class="long" >{$oMovie->getDescription()}</textarea></td>
						</tr>
						<tr>
							<th>{t}Credits{/t}</th>
							<td><input type="text" name="Name" value="{$oMovie->getCredits()}" class="long" /></td>
						</tr>					

					</tbody>	
				</table>	
			</div>
			<div class="content">
				<input type="button" id="commitUserMovie" value="Commit" class="fg-button ui-state-default ui-corner-all" />
				<input type="button" id="rejectUserMovie" value="Reject" class="fg-button ui-state-default ui-corner-all" />
				<input type="hidden" name="movieID" id="usermovieID" value="{$oMovie->getID()}">
			</div>
		</div>
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}