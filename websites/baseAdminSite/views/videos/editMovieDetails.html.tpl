<h3><a href="#">{t}Movie Details{/t}</a></h3>
<div>
	{if $oMovie->getModerated()}
	<div class="formFieldModerator">
		<h4>{t}Moderated by{/t}</h4>
		<p class="moderator"><strong>{$oMovie->getModerator()->getFullname()|xmlstring}</strong> ({$oMovie->getModerator()->getClient()->getCompanyName()}) on {$oMovie->getModerated()|date_format:'%d-%m-%Y'}.</p>
		{if $oMovie->getModeratorComments()}
			<p>{$oMovie->getModeratorComments()|xmlstring}</p>
		{/if}
	</div>
	{/if}
	
	<div class="formFieldContainer">
		<h4>{t}Title{/t}</h4>
		{if $oController->hasAuthority('canChangeMovieTitle')}
			<p><input type="text" name="Title" value="{$oMovie->getTitle()|escape:'htmlall':'utf8'}" class="long" /></p>
		{else}
			<p>{$oMovie->getTitle()|xmlstring}</p>
		{/if}
	</div>
	
	<div class="formFieldContainer">
		<h4>{t}Brand &amp; Project{/t}</h4>
		<p>
			{if $oController->hasAuthority('canChangeMovieSource')}
                            
                            {brandDistinctSelect id="corporateListBrands" name='BrandID' selected=$brandDetails['BrandID']  class="valignMiddle " }       
                            {eventSelect id="eventListVideo" name='EventID' selected=$oMovie->getSource()->getEventID()  BrandID=$brandDetails['BrandID']  class="valignMiddle " user=$oUser}
			{else}
				{$brandDetails['BrandName']}
				{$oMovie->getSource()->getName()}
			{/if}
		</p>
	</div>
	
	<div class="formFieldContainer">
		<h4>{t}Movie Credits{/t}</h4>
		<p>
			{if $oController->hasAuthority('canChangeMovieCredits')}
				<p><input type="text" name="Credits" value="{$oMovie->getCredits()}" class="long" /></p>
			{else}
				<p>{$oMovie->getCredits()|xmlstring}</p>
			{/if}
		</p>
	</div>
	
	{if $oController->hasAuthority('getTinyUrl') && $oMovie->getStatus() != 'Rejected' && $oMovie->getStatus() != 'Removed'}
	<div id="movieShortUri" class="formFieldContainer">
		<h4>{t}Short URI{/t}</h4>
		<p><input type="text" name="ShortUri" value="{$oMovie->getShortUri($oUser->getID(), true)}" readonly="readonly" class="long" onclick="this.focus();this.select();" /></p>
	</div>
	{/if}

	<div class="formFieldContainer">
		<h4>{t}Description{/t}</h4>
		{if $oController->hasAuthority('canChangeMovieDescription')}
			<p><textarea name="Description" rows="6" cols="60" class="long">{$oMovie->getDescription()|xmlstring}</textarea></p>
		{else}
			<p>{$oMovie->getDescription()|xmlstring}</p>
		{/if}
	</div>
	
	<table class="data">
		<thead>
			<tr>
				<th>{t}Active{/t}</th>
				<th>{t}Private{/t}</th>
				<th>{t}Status{/t}</th>
				<th>{t}Duration{/t}</th>
				<th>{t}Production Year{/t}</th>
				<th>{t}Moderated{/t}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					{if $oController->hasAuthority('setStatus')}
						{yesNoSelect name="Active" selected=$oMovie->getActive()}
					{else}
						{$oMovie->getActive()}
					{/if}
				</td>
				<td>
					{if $oController->hasAuthority('setPrivate')}
						{booleanSelect name="Private" selected=$oMovie->getPrivate() true="Yes" false="No"}
					{else}
						{if $oMovie->getPrivate()}Yes{else}No{/if}
					{/if}
				</td>
				<td>
					{if $oController->hasAuthority('setStatus')}
						{if $oUser->getPermissions()->isRoot()}
							{movieStatusSelect id="movieStatus" name="Status" class="small" selected=$oMovie->getStatus() showAll=1}
						{else}
							{movieStatusSelect id="movieStatus" name="Status" class="small" selected=$oMovie->getStatus()}
						{/if}
					{else}
						{$oMovie->getStatus()}
					{/if}
				</td>
				<td><input type="text" name="Runtime" value="{$oMovie->getDuration()}" class="short" /> seconds</td>
				<td><input type="text" name="ProductionYear" value="{$oMovie->getProductionYear()}" class="short" /></td>
				<td>{if $oMovie->getModerated()}{$oMovie->getModerated()|date_format:'%d-%m-%Y'}{else}No{/if}</td>
			</tr>
		</tbody>
	</table>
</div>