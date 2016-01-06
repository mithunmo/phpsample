{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}

{assign var=offset value=$oModel->getOffset()}
{assign var=limit value=$oModel->getLimit()}
{assign var=totalObjects value=$oModel->getMashTotalObjects($userID)}

<div style="height:800px;background-image:url(/themes/momusic/images/page_back_border.gif)"">
	{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;">
		<div align="center"><h3>My Work</h3></div>
		<table class="data">

				<tfoot>
					{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=2}
				</tfoot>
				<thead>
					<tr>
						<th class="first">{t}Name{/t}</th>
						<th style="width: 280px;">{t}Link{/t}</th>
						<th style="width: 30px;">{t}Email/Share{/t}</th>
						<th class="last" style="width: 100px;">Created Date</th>
					</tr>
				</thead>
				<tbody>
					{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=2}
					{foreach $oResult as $oMashResult}
							<tr >	
								<td>
									{$oMashResult->getName()}
								</td>
								<td>
									<a target="_Blank" href="{$momusicuri}/music/mash/{$oMashResult->getHash()}">{$momusicuri}/music/mash/{$oMashResult->getHash()} </a>
								</td>
								<td align="center">
									<a target="_Blank" href="mailto:?Subject=Watch this Video/Music Sync&body={$momusicuri}/music/mash/{$oMashResult->getHash()}"> Share </a>
								</td>				
								<td>
									{$oMashResult->getCreateDate()|date_format:"%e %b %y"}
								</td>
								
								
							</tr
					{/foreach}		
				</tbody>
		</table>
		
		<div style="height:8px;"></div>
	</div>	
</div> <!-- Content Ends -->
</div></div>


{include file=$oView->getTemplateFile('momusicfooter','/shared') pageTitle="momusic"}
