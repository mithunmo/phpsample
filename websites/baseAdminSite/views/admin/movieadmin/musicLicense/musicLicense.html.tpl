{include file=$oView->getTemplateFile('header', 'shared') pageTitle=$oMap->getDescription()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>
			
			<div class="floatLeft main">
				<p>
					{t}To validate a music licenseID, Enter the license and if you need to add more licenses then click on +{/t}
					<br />
					{t}Click on the Validate to know the if license is valid{/t}
					<br />
					{t}Click on the show movie details to know the licenses associated with movieID{/t}
				</p>

				<form id="adminFormData" name="formData" method="post" action="" accept-charset="utf-8">
					<table class="data">
						<tbody>
							<tr>
								<th>{t}Movie ID{/t}</th>
								<td><input type="text" name="MovieID" id="MasterMovieID"> </td>
							</tr>							
							<tr>
								<th>{t}License ID{/t}</th>
									<td><input type="text" name="LicenseID" id="LicenseID0"> <span id="addLicense"><img src="/themes/mofilm/images/icons/16x16/add.png"/></span></td>
							</tr>
							<tr id="licenseText">
								<th>{t}Options{/t}</th>
								<td><input type="submit" value="Validate" id="validateLicense"/> <input type="submit" id="movieDetail" value="Show movie Details"/></td>
							</tr>
							
						</tbody>
					</table>
				</form>
				<br />	
				<b>{t}Details of all the licenses{/t}</b>	
				<br />
				<div class="content"> 	
				{include file=$oView->getTemplateFile('licenseTemplate', '/admin/movieadmin/musicLicense')}	
				</div>					
			</div>
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}