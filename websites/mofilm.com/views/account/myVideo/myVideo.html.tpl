{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}User movie list{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
{assign var=offset value=$oResults->getSearchInterface()->getOffset()|default:0}
{assign var=limit value=$oResults->getSearchInterface()->getLimit()}
{assign var=totalObjects value=$oResults->getTotalResults()}

<div id="body">
	<div class="container">
		<div class="content">
			<table class="data">

				<tfoot>
					{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=5}
				</tfoot>
				<thead>
					<tr>
						<th class="first">{t}MovieID{/t}</th>
						<th style="width: 55px;">{t}Event{/t}</th>
						<th style="width: 55px;">{t}Source{/t}</th>
						<th style="width: 55px;">{t}Thumb{/t}</th>
						<th>{t}Title{/t}</th>
						<th>{t}Status{/t}</th>
						<th>{t}Action{/t}</th>
					</tr>
				</thead>
				<tbody>
					{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=5}
					{if $oResults->getTotalResults() > 0}
						{foreach $oResults as $oVideoResult}
							<tr class="{cycle values=",alt"} {$oVideoResult->getStatus()|replace:' ':''|lower}{$oVideoResult->getActive()}">	
								<td>
									{$oVideoResult->getID()}
								</td>
								<td class="alignCenter"><img src="{$adminEventFolder}/{$oVideoResult->getSource()->getEvent()->getLogoName()}.jpg" width="50" height="28" alt="{$oVideoResult->getSource()->getEvent()->getName()}" title="{$oVideoResult->getSource()->getEvent()->getName()}" class="eventLogo valignMiddle" /></td>
								<td class="alignCenter"><img src="{$adminSourceFolder}/{$oVideoResult->getSource()->getLogoName()}.jpg" width="50" height="28" alt="{$oVideoResult->getSource()->getName()}" title="{$oVideoResult->getSource()->getName()}" class="sourceLogo valignMiddle" /></td>
								<td class="alignCenter">
									{assign var=videoStatus value = $oVideoResult->getStatus()}
									
									{*if $oVideoResult->getUploadStatusSet()->getVideoCloudID() > 0 }
									    {assign var=watchlink value=$oVideoResult->getShortUri($oVideoResult->getUserID(),true)}
									{else}
									    {assign var=watchlink value="/account/myVideo/edit/{$oVideoResult->getID()}"}
									{/if*}
                                                                        {if $videoStatus == "Approved" || $videoStatus == "Pending" }
                                                                            {assign var=watchlink value="/account/myVideo/edit/{$oVideoResult->getID()}"}
									{/if}
					
                                                                        
									{if $videoStatus == "Encoding"}
										<a href="" title="{t}Watch this movie{/t}">
											<img src="/resources/video/thumb_150x85.png" width="50" height="28" alt="Thumb" class="valignMiddle" />
										</a>
									{elseif $videoStatus == "Approved"}
										<a href="{$watchlink}" title="{t}Watch this movie{/t}">
											<img src="{$oVideoResult->getThumbnailUri('s')}" width="50" height="28" alt="Thumb" class="valignMiddle" />
										</a>
									{elseif $videoStatus == "Pending" && $oVideoResult->getActive() == "N"}
										<a href="/account/review/view/{$oVideoResult->getID()}" title="{t}Watch this movie{/t}">
											<img src="{$oVideoResult->getThumbnailUri('s')}" width="50" height="28" alt="Thumb" class="valignMiddle" />
										</a>
									{elseif $videoStatus == "Pending" && $oVideoResult->getActive() == "Y"}
										<a href="{$watchlink}" title="{t}Watch this movie{/t}">
											<img src="{$oVideoResult->getThumbnailUri('s')}" width="50" height="28" alt="Thumb" class="valignMiddle" />
										</a>							
									{/if}						
								</td>
								<td>{$oVideoResult->getTitle()}</td>
								<td class="alignCenter {$oVideoResult->getStatus()}">
									{if $videoStatus == "Pending" && $oVideoResult->getActive() == "N"}
										{t}User Approval Pending{/t}
									{elseif $videoStatus == "Pending" && $oVideoResult->getActive() == "Y"}
										{t}Admin Moderation Pending{/t}
									{else}
										{$oVideoResult->getStatus()}
									{/if}												
								</td>
								<td>
									{if $videoStatus == "Approved" || $videoStatus == "Pending" }
									<a href="/account/myVideo/edit/{$oVideoResult->getID()}">{t}EDIT{/t}</a>
									{/if}
								</td>	
							</tr>
						{/foreach}
					{else}
						<tr>
							<td colspan="5">{t}No objects found matching search criteria.{/t}</td>
						</tr>
					{/if}
				</tbody>

			</table>
		</div>	
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}
