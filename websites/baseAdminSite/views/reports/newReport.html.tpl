{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Reports - Select Report{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{include file=$oView->getTemplateFile('reportMenu')}
			</div>

			<div class="floatLeft main">
				<h2>{t}Select Report{/t}</h2>
				<div class="reportContainer">
					{if $oReports->getTotalResults() > 0}
						{foreach $oReports as $oReport}
							<div class="report floatLeft">
								<h4>{$oReport->getTypeName()}</h4>
								<p>{$oReport->getDescription()}</p>
								<p class="alignRight"><a href="{$reportNewUri}/{$oReport->getReportTypeID()}">{t}Create Report{/t}</a></p>
							</div>
						{/foreach} 
					{else}
						<p>{t}There are no reports available to you at this time.{/t}</p>
					{/if}
					
					<br class="clearBoth" />
				</div>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}