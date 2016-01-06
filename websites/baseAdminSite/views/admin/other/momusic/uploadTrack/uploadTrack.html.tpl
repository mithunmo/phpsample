{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}uploadTrack{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body">
	<div class="container">
		{include file=$oView->getTemplateFile('statusMessage', '/shared')}

		<div class="floatLeft main">
			<h2>{t}Upload a new Track{/t}</h2>
			<form method="post" action="/admin/other/momusic/uploadTrack/doUpload" enctype="multipart/form-data" name="musicform"> 
			<table class="data">
				<tbody>
					<tr>
						<th>{t} Song Name{/t}</th>
						<td><input type="text" name="SongName" value="" /></td>
					</tr>
					<tr>
						<th>{t} Artist Name{/t}</th>
						<td><input type="text" name="ArtistName" value="" /></td>
					</tr>
					<tr>
						<th>{t} Album Name{/t}</th>
						<td><input type="text" name="AlbumName" value="" /></td>

					</tr>
					<tr>
						<th>{t}Upload{/t}</th>
						<td> 
							<input type="file" style="width:200px" name="Files">
						</td>
					</tr>
					<tr>
						<th>{t}Save{/t}</th>
						<td> 
							<input type="submit" name="save" value="Save" />
						</td>
					</tr>
					
					
				</tbody>
			</table>

		</form>

		</div>

		<br class="clearBoth" />
	</div>
</div>

{include file=$oView->getTemplateFile('footer', 'shared')}