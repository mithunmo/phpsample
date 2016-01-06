<h3><a href="#">{t}Movie Music License{/t}</a></h3>
<div>
	<input id="videoLicense" type="submit" value="Validate">
	{include file=$oView->getTemplateFile('licenseTemplate', '/admin/movieadmin/musicLicense') oLicenseSet=$oMovie->getLicenseSet()}

	<h4>{t}Custom Video license{/t}</h4>
	{$oMovie->getDataSet()->getProperty(mofilmDataname::DATA_MOVIE_LICENSEID)}
</div>
