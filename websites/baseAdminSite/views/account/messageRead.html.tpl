{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Reading Message - {/t}'|cat:$oModel->getMessage()->getSubject()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h2>{t}Reading Message - {/t} {$oModel->getMessage()->getSubject()}</h2>
			
			<div class="content">
				<div id="adminActions" class="body">
					{include file=$oView->getTemplateFile('daoActionsMenu', '/shared')}
					
					{if $oModel->getMessage()->isAttachedToMovie()}
						<a href="{adminMovieLink movieID=$oModel->getMessage()->getAttachedMovieID()}" title="{t}Click to see video details{/t}">
							<img src="{$themeicons}/32x32/watch.png" alt="{t}Click to see video details{/t}" class="icon" />
							{t}Go to Video{/t}
						</a>
					{/if}
				</div>
				<div class="clearBoth"></div>
			</div>

			<div class="content">
				<div class="body">
			
					<table class="data">
						<tbody>
							<tr class="{cycle values="alt,"}">
								<th class="pmSender">{t}Received{/t}</th>
								<td>{$oModel->getMessage()->getCreateDate()|date_format:'%d %B %Y @ %H:%M'}</td>
							</tr>
							<tr class="{cycle values="alt,"}">
								<th class="pmSender">{t}From{/t}</th>
								<td>{$oModel->getMessage()->getSender()->getFullname()}</td>
							</tr>
							<tr class="{cycle values="alt,"}">
								<th>{t}Subject{/t}</th>
								<td>{$oModel->getMessage()->getSubject()|xmlstring}</td>
							</tr>
							<tr class="{cycle values="alt,"}">
								<th class="valignTop">{t}Message{/t}</th>
								<td>{$oModel->getMessage()->getMessage()|xmlstring|nl2br}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}