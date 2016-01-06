{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}uploadTrack{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body">
	<div class="container">
		{include file=$oView->getTemplateFile('statusMessage', '/shared')}

		<div class="floatLeft main">
			<h2>{t}Add new featured artist{/t}</h2>
			<form method="post" action="/admin/other/momusic/featuredArtist/doNewObject" enctype="multipart/form-data" name="musicform"> 
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Name{/t}</th>
				<td><input type="text" name="Name" value="{$oObject->getName()}" /></td>
			</tr>
			<tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" /></td>
			</tr>
			<tr>
				<th>{t}Image Path{/t}</th>
                                
				<td>
                                 {*   <input type="text" name="ImagePath" value="{$oObject->getImagePath()}" /> *}
                                    <input type="file" style="width:200px" name="ImagePath" value="{$oObject->getImagePath()}">
		
                                </td>
			</tr>
			<tr>
				<th>{t}Status{/t}</th>
				<td><input type="text" name="Status" value="{$oObject->getStatus()}" /></td>
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