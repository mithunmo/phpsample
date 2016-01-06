<h3><a href="#">{t}Preferences{/t}</a></h3>
<div>
	<div class="formFieldContainer">
		<h4>{t}Language{/t}</h4>
		<p>{languageSelect name="Prefs[Language]" useISO=true selected=$oUser->getParamSet()->getParam("Language")|default:'en'}</p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Receive Private Message Alerts{/t}</h4>
		<p>{booleanSelect name="Prefs[PrivateMessageAlerts]" selected=$oUser->getParamSet()->getParam("PrivateMessageAlerts") true='Yes' false='No'}</p>
	</div>
	
	{if $oController->hasAuthority('videosController.rate')}
	<div class="formFieldContainer">
		<h4>{t}Flag Unrated Movies On Movie Search Results{/t}</h4>
		<p>{booleanSelect name="Prefs[FlagUnratedMovies]" selected=$oUser->getParamSet()->getParam("FlagUnratedMovies", 0) true='Yes' false='No'}</p>
		<p>{t}This will mark all movies that you have not rated with a yellow background in the video search result.{/t}</p>
	</div>
	{/if}
	
	{if $oController->hasAuthority('videosController.review')}
	<div class="formFieldContainer">
		<h4>{t}Judging List Events{/t}</h4>
		<p>{eventSelect name="ExcludedEvents[]" multiple="true" size="10" selected=$oUser->getEventFilter()->getIterator() user=$oUser class="medium"}</p>
		<p>
			{t}Select the events to show in Judging mode.{/t}
			{t}If none are selected all viewable events will be used.{/t}
			{t}Hold Ctrl (Command on Mac) to select multiple items.{/t}
		</p>
	</div>
	{/if}
</div>