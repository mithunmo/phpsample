{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}

<div style="height:700px;background-image:url(/themes/momusic/images/page_back_border.gif)"">
	{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;">
		<div align="center"><h3>List of Cart Items</h3></div>
		<table class="data">

				<thead>
					<tr>
						<th class="first" style="width: 340px;">{t}Name{/t}</th>
						<th style="width: 200px;">Link</th>
						<th class="last" style="width: 100px;">{t}Remove Cart{/t}</th>
					</tr>
				</thead>
				<tbody>
					{foreach $oObjects as $oObject}
							<tr >	
								<td>
									{$oModel->getItemName($oObject)}
								</td>
								<td>
									<a href="/music/download/{$oObject}">Download</a>
								</td>				
								<td>
									<a style="color:red;" href="/cart/delete/{$oObject}"><strong>Remove</strong></a>
								</td>	
							</tr
					{/foreach}		
				</tbody>
		</table>
		
		<div style="height:25px;"></div>
		
		<div style="padding-left:200px;"><a href="/music/sync"><img src="/themes/momusic/images/sync_tool_btn_1.png" /></a> </div>		
		
	</div>	
</div> <!-- Content Ends -->
</div></div>


{include file=$oView->getTemplateFile('momusicfooter','/shared') pageTitle="momusic"}
