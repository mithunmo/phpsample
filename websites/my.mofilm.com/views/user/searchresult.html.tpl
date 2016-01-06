{if $oResult->getResultCount() > 0 }
<div class="entry labels {cycle values=",alt"}">
	<div class="profileImage"></div>
	<div class="profileName">{t}Name{/t}</div>
	<div class="score">{t}Score{/t}</div>
</div>

{foreach $oResult as $oProfile}
	<div class="entry {cycle values=",alt"}">
		<div class="profileImage">{strip}
			{if $oProfile->getProfile()->isActive()}
				<a href="/user/{$oProfile->getProfile()->getProfileName()}" title="{t}View Profile{/t}">
			{/if}
			{if $oProfile->getAvatar()->getImageFilename()}
				<img src="{$oProfile->getAvatar()->getImageFilename()}" alt="avatar" />
			{else}
				<img src="{$themeimages}/profile/avatar.jpg" alt="avatar" />
			{/if}
			{if $oProfile->getProfile()->isActive()}
				</a>
			{/if}
		{/strip}</div>
		<div class="profileName">{strip}
			{if $oProfile->getProfile()->isActive()}
				<a href="/user/{$oProfile->getProfile()->getProfileName()}" title="{t}View Profile{/t}">
			{/if}
			
			{if $oProfile->getProfile()->isActive()}
			{$oProfile->getFullname()|escape:'htmlall':'UTF-8'}
			{else}
			{$oProfile->getFullname()|escape:'htmlall':'UTF-8'} <div style="font-size:10px;">User has no Profile</div>
			{/if}	

			{if $oProfile->getProfile()->isActive()}
				</a>
			{/if}
		{/strip}</div>
		<div class="score">{if $highscore}{$oProfile->getPoints()->getHighScore()}{else}{$oProfile->getPoints()->getScore()}{/if}</div>
	</div>
{/foreach}

<div class="entry labels {cycle values=",alt"}">
	<div class="previous">{if $page-1 > 0}<a class="previous" href="/user/{$linkType}/{$linkPage|lower}/{$page-1}?userSearch={$searchKey}"><img src="{$themeicons}/32x32/result-set-first.png" alt="previous" class="icon" /> {t}Previous{/t}</a>{else}&nbsp;{/if}</div>
	<div class="next floatRight">{if $page+1 <= $lastPage}<a class="next" href="/user/{$linkType}/{$linkPage|lower}/{$page+1}?userSearch={$searchKey}">{t}Next{/t} <img src="{$themeicons}/32x32/result-set-last.png" alt="next" class="icon" /></a>{else}&nbsp;{/if}</div>
</div>
{else}
<div class="entry"> <p><center><b> No Results Found </b></center></p> </div>	
{/if}