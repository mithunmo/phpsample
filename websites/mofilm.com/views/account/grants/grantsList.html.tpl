{assign var=offset value=$oModel->getOffset()|default:0}
{assign var=limit value=30}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{assign var=objects value=$oModel->getObjectList($offset, $limit)}

{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}User Grants List{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
	<div class="container">
	    		<h2>{t}User Grants{/t}</h2>
			{if $objects->getArrayCount() > 0}
			<table class="data">
				<tfoot>
					{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=5}
				</tfoot>
				<thead>
					<tr>
						<th>{t}Movie{/t}</th>
						<th>{t}Working Title{/t}</th>
						<th style="width: 55px;">{t}Event{/t}</th>
						<th style="width: 55px;">{t}Source{/t}</th>
						<th>{t}Granted Amount{/t}</th>
						<th>{t}Status{/t}</th>
						<th>{t}Actions{/t}</th>
					</tr>
				</thead>
				<tbody>
				{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=5}
				{foreach $objects as $oObject}
					<tr class="{cycle values=",alt"}">
						<td {if $oObject@iteration % 2}class="alt"{/if}>
							{if $oObject->getStatus() == 'Approved' }
								{if $oObject->getMovieID() > 0}
									<a href="{$oObject->getMovie()->getShortUri($oObject->getUserID(), true)}" ><img src="{$oObject->getMovie()->getThumbnailUri()}" alt="{$oObject->getMovieID()}" width="50" height="28" class="valignMiddle" /></a>
								{/if}
							{/if}
						</td>
						<td {if $oObject@iteration % 2}class="alt"{/if}>
							{$oObject->getFilmTitle()}
							{if !($oObject->getApplicationAppliedStatus())}
								<img src="/themes/mofilm/images/past_deadline.png" width="16" height="16" alt="Applied past deadline" title="Applied past deadline" />
							{/if}
						</td>
						<td {if $oObject@iteration % 2}class="alt"{/if}><img src="/resources/client/events/{$oObject->getGrants()->getSource()->getEvent()->getLogoName()}.jpg" width="50" height="28" alt="{$oObject->getGrants()->getSource()->getEvent()->getName()}" title="{$oObject->getGrants()->getSource()->getEvent()->getName()}" /></td>
						<td {if $oObject@iteration % 2}class="alt"{/if}><img src="/resources/client/sources/{$oObject->getGrants()->getSource()->getLogoName()}.jpg" width="50" height="28" alt="{$oObject->getGrants()->getSource()->getName()}" title="{$oObject->getGrants()->getSource()->getName()}" /></td>
						<td {if $oObject@iteration % 2}class="alt"{/if}>
							{if $oObject->getStatus() == 'Approved' && $oObject->getGrantedAmount() > 0}
							    {$oObject->getGrants()->getCurrencySymbol()} {$oObject->getGrantedAmount()}
							{/if}
						</td>
						<td {if $oObject@iteration % 2}class="alt"{/if}>{t}{$oObject->getStatus()}{/t}</td>
						<td {if $oObject@iteration % 2}class="alt"{/if}>
							<a href="/account/grants/view/{$oObject->getID()}">{t}View{/t}</a>
							{if $oObject->getStatus() == 'Pending' && $oObject->getGrants()->getSource()->isOpen()}
							    | <a href="/account/grants/edit/{$oObject->getID()}">{t}EDIT{/t}</a>
							{/if}
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
			{else}
				<p>{t}No objects found in system{/t}.</p>
			{/if}
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}
