{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Upload a Movie{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body">
	<div class="container">
		
		<h3>MOFILM Basic Uploader </h3>
		
		<form  id="plu" method="post" action="/account/upload/doPlupload">
			<div id="uploader">
				<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
			</div>
		</form>

		<form id="basicuploader" method="post" action="/account/upload/doBasicSave">
			<div class="formFieldContainer">
				<h4>{t}Select the Event{/t}</h4>
				<p>		<select id ="eventUpload" name="EventID" class="long required">
						<option value="">{t}Select event{/t}</option>
						{foreach $eventsall as $oEvent}
							<option value="{$oEvent->getID()}" {if $eventID  == $oEvent->getID()} selected="selected"{/if}>{$oEvent->getName()}</option>
						{/foreach}
					</select>
				</p>
			</div>


			<div class="formFieldContainer">
				<h4>{t}Select the Brand{/t}</h4>
				<p>		<select id="sourceUpload" name="sourceID" class="long required">
					</select>

				</p>
			</div>

			<div class="formFieldContainer">
				<h4><input name="check" type="checkbox" class="required" />  {t}Agree to <a target="_blank" href="{t}http://www.mofilm.com/info/uploadTerms.html{/t}">{t}Terms and Conditions{/t}</a> {/t}</h4>
			</div>
				
				
			<div class="formFieldContainer">
				<h4>{t}Title{/t}</h4>
				<p><input name="Title" type="text" class="long required" minlength="3" maxlength="55"/></p>
			</div>

			<div class="formFieldContainer">
				<h4>{t}Description{/t}</h4>
				<p><textarea name="Description"  class="long required" minlength="3"> </textarea></p>
			</div>

			<div class="formFieldContainer">
				<h4>{t}Enter your music license Details{/t}</h4>
				<p><textarea name="customLicense"  class="long required" minlength="3"> </textarea></p>
			</div>
				
			<div class="formFieldContainer">
				<p><input type="submit" id="signup" value="Save Video"></p>
			</div>
				<input type="hidden" name="fileName" id="fileNameStored">
		</form>	

		<br class="clearBoth">
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}
