<h3><a href="#">{t}My.MOFILM.com Profile Settings{/t}</a></h3>
<div>
	<p>
		{t}Here you can control your my.mofilm.com profile, including any active information!{/t}
		{t}You can block access to your profile at any time by marking it unavailable.{/t}
		{t}You won't lose your profile name and this can be re-activated at any time.{/t}
	</p>
	<p>
		{t}<strong>Important:</strong> you can change your profile name at any time BUT you cannot revert to a previous name.{/t}
	</p>
{*	{if $oUser->getProfile()->getActive()}
		<div class="formFieldContainer">
			<h4>
				{t}Your profile is active{/t}
				<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
			</h4>
			<p>
				<input name="ProfileName" type="hidden" value="{$oUser->getProfile()->getProfileName()|xmlstring}" readonly="readonly" />
				<a href="{$mofilmMyUri}/user/{$oUser->getProfile()->getProfileName()|xmlstring}" class="profileLink" target="_blank">my.mofilm.com/user/{$oUser->getProfile()->getProfileName()|xmlstring}</a>
			</p>

			{help text="{t}Your profile is currently active. While it is active you cannot change your profile name.{/t}"}
		</div>
	{else}
		<div class="formFieldContainer">
			<h4>
				{t}Your Profile Name (must be unique and will be checked).{/t}
				<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
			</h4>
			<p><em>{$mofilmMyUri}/user/</em><input id="profileName" name="ProfileName" type="text" value="{$oUser->getProfile()->getProfileName()|xmlstring}" class="medium" /></p>

			{help text="{t}You can only use: A-Z, a-z, 0-9, - (hyphen / dash) and _ (underscore). Max length: 50 characters. It is recommended to use lowercase characters and numbers, separate words with a - or _.{/t}"}
		</div>
	{/if}*}
	<div class="formFieldContainer">
		<h4>
			{t}Make your profile available?{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p>{booleanSelect selected=$oUser->getProfile()->getActive() id="ProfileActive" name='ProfileActive' true='Yes' false='No'}</p>

		{help text="This option allows you to control access to your public profile. You can completely disable your profile by selecting 'No' at any time."}
	</div>
	<div class="formFieldContainer">
		<h4>
			{t}Tell us about yourself{/t} (<em>{t}Max length, {mofilmConstants::PROFILE_TEXT_LENGTH} characters.{/t}</em>)
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><textarea rows="5" cols="75" id="ProfileText" name="ProfileText">{$oUser->getParamSet()->getParam('ProfileText')|xmlstring}</textarea></p>

		{help text="
			{t}Here you can write a few sentences that will be displayed below your profile picture.{/t}<br />
			{t}Please, <strong>NO</strong> HTML, BBCode or other markups - they are not supported.{/t}<br />
			{t}Remember: keep it clean - this is a family friendly site. Max length, {mofilmConstants::PROFILE_TEXT_LENGTH} characters.{/t}"}
	</div>
	<div class="formFieldContainer">
		<h4>
			{t}Select your best or favourite movies (up to 5 total).{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		{help text="
		<ul>
			<li>{t}Left click and drag the movies to / from the active area.{/t}</li>
			<li>{t}You can change the movie title by clicking on it when in the active area.{/t}</li>
			<li>{t}Your titles will be kept even if you change the movie order.{/t}</li>
			<li>{t}To remove a movie, drag and drop it out of the active area.{/t}</li>
			<li>{t}Changes to the movies will automatically save any changes to your profile.{/t}</li>
			<li>{t}At the moment you can only select from your approved, active movies.{/t}</li>
			<li>{t}We automatically show your award winning movies.{/t}</li>
			<li>{t}Remember: keep your movie titles clean!{/t}</li>
		</ul>"}
		<ol id="profileMovies" class="connectedSortable">
			{foreach $oUser->getProfileMovieSet() as $oProfileMovie}
				<li class="ui-state-default">
					<input type="checkbox" name="ProfileMovies[]" value="{$oProfileMovie->getMovieID()}" checked="checked" />
					<input type="hidden" name="ProfileMovieTitles[{$oProfileMovie->getMovieID()}]" value="{$oProfileMovie->getTitle()|xmlstring}" />
					#{$oProfileMovie->getMovieID()} - <span id="movID-{$oProfileMovie->getMovieID()}" class="movieTitle editable ignore">{$oProfileMovie->getTitle()|xmlstring}</span>
				</li>
			{/foreach}
		</ol>
		
		<h5>{t}Your available movies:{/t}</h5>
		<ol id="availableProfileMovies" class="connectedSortable">
			{foreach $oModel->getApprovedMovies() as $oMovie}
				{if !$oUser->getProfileMovieSet()->isMovieInProfile($oMovie->getID())}
					<li class="ui-state-default">
						<input type="checkbox" name="ProfileMovies[]" value="{$oMovie->getID()}" />
						<input type="hidden" name="ProfileMovieTitles[{$oMovie->getID()}]" value="{$oMovie->getTitle()|xmlstring}" />
						#{$oMovie->getID()} - <span id="movID-{$oMovie->getID()}" class="movieTitle editable">{$oMovie->getTitle()|xmlstring}</span>
					</li>
				{/if}
			{/foreach}
		</ol>
	</div>
</div>