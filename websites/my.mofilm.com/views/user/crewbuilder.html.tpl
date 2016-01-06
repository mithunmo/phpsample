<div class="entry labels {cycle values=",alt"}">
	{*	<div class="position">{t}Rank{/t}</div> *}
	{*	<div class="profileImage"></div> *}
	<div class="profileName">{t}Name{/t}</div>
	<div class="city">{t}City{/t}</div>
	<div class="contact">{t}Contact{/t}</div>

</div>
{*	
<div class="entry labels {cycle values=",alt"}">
	<div class="previous">
		{if $page-1 > 0}
			<a class="previous" href="/user/skills/{$linkPage|lower}/{$page-1}/{$key}">
				<img src="{$themeicons}/32x32/result-set-first.png" alt="previous" class="icon" /> {t}Previous{/t}</a>
			{else}
			<a class="previous" >
				<img src="{$themeicons}/32x32/result-set-first.png" alt="previous" class="icon" /> {t}Previous{/t}</a>						
			{/if}
	</div>
	<div class="res"> Page {$page} of {$lastPage} , {$total} profiles found </div>
	<div class="next floatRight">
		{if $page+1 <= $lastPage}<a class="next" href="/user/skills/{$linkPage|lower}/{$page+1}/{$key}">{t}Next{/t} 
				<img src="{$themeicons}/32x32/result-set-last.png" alt="next" class="icon" /></a>
			{else}
			<a class="next">{t}Next{/t} 
				<img src="{$themeicons}/32x32/result-set-last.png" alt="next" class="icon" /></a>						
			{/if}
	</div>
</div>
*}
{foreach $oResult as $oProfile}
	<div class="entry {cycle values=",alt"}">
		{*	<div class="position">{$oProfile@iteration+$offset}</div> *}
		{assign var=oUser value=$oModel->getUserProfile($oProfile->s_id)}
		<div class="profileImage">{strip}
			<a href="/user/{$oUser->getProfile()->getProfileName()}" title="{t}View Profile{/t}">

				{if $oUser->getAvatar()->getImageFilename()}
					<img src="{$oModel->getUserProfile($oProfile->s_id)->getAvatar()->getImageFilename()}" alt="avatar" />
				{else}
					<img src="{$themeimages}/profile/avatar.jpg" alt="avatar" />
				{/if}

			</a>
			{/strip}</div>
			<div class="profileNameSkill">
				<div style="width:400px;height:40px;line-height: 70px;float:left;">
					<a href="/user/{$oUser->getProfile()->getProfileName()}" title="{t}View Profile{/t}">	
						{strip}
							{$oProfile->s_name}
						{/strip}
					</a>
				</div>

				<div style="width:450px;height:30px;line-height: 30px;font-size: 12px;color:#FD7801;">
					{strip}
						{if $oProfile->s_skill}
							{$oProfile->s_skill|truncate:65}
						{else}
							{$oProfile->s_role|truncate:65}
						{/if}
					{/strip}
				</div>


			</div>
			<div style="width:160px;height:40px;line-height: 90px;">
				{$oProfile->s_city|default:"-"}
			</div>
			
			<a style="color:white;" target="_blank" href="/account/pm/new?userID={$oProfile->s_id}" class="myButton">Send Message</a>

		</div>
		{/foreach}
			{if $uri}
			<div class="entry labels {cycle values=",alt"}">
				<div class="previous">{if $page-1 > 0}<a class="previous" href="/user/skills/{$linkPage|lower}/{$page-1}/?{$uri}"><img src="{$themeicons}/32x32/result-set-first.png" alt="previous" class="icon" /> {t}Previous{/t}</a>{else}&nbsp;{/if}</div>
				<div class="next floatRight">{if $page+1 <= $lastPage}<a class="next" href="/user/skills/{$linkPage|lower}/{$page+1}/?{$uri}">{t}Next{/t} <img src="{$themeicons}/32x32/result-set-last.png" alt="next" class="icon" /></a>{else}&nbsp;{/if}</div>
			</div>				
			{else}	
			<div class="entry labels {cycle values=",alt"}">
				<div class="previous">{if $page-1 > 0}<a class="previous" href="/user/skills/{$linkPage|lower}/{$page-1}/{$key}"><img src="{$themeicons}/32x32/result-set-first.png" alt="previous" class="icon" /> {t}Previous{/t}</a>{else}&nbsp;{/if}</div>
				<div class="next floatRight">{if $page+1 <= $lastPage}<a class="next" href="/user/skills/{$linkPage|lower}/{$page+1}/{$key}">{t}Next{/t} <img src="{$themeicons}/32x32/result-set-last.png" alt="next" class="icon" /></a>{else}&nbsp;{/if}</div>
			</div>
			{/if}