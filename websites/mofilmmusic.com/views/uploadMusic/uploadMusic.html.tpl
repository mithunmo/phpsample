{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
{*include file=$oView->getTemplateFile('header','/shared') pageTitle="uploadMusic"*}


<div style="height:900px;">
	{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;  height:inherit;">
		<div>
			
<div id="body">
	<div class="container" style="padding-left:40px;">
		
		<h3> MOMUSIC Audio Contest </h3>
		
		<form  id="plu" method="post" action="/uploadMusic/doPlupload">
			<div id="uploader" style="width:600px;">
				<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
			</div>
		</form>

		<form id="basicuploader" method="post" action="/uploadMusic/doBasicSave">
			<div class="formFieldContainer">
				<h4>{t}Select the Event{/t}</h4>
				<p>		<select id ="eventUpload" name="EventID" class="long required">
						<option value="74">Lollapalooza 2013</option>
					</select>
				</p>
			</div>


			<div class="formFieldContainer">
				<h4>{t}Select the Brand{/t}</h4>
				<p>		<select id="sourceUpload" name="sourceID" class="long required">
						<option value="444">Wall's Cornetto</option>

					</select>

				</p>
			</div>

			<div class="formFieldContainer">
				<h4><input name="check" type="checkbox" class="required" />  {t}Agree to <a target="_blank" href="{t}http://www.mofilm.com/info/uploadTerms.html{/t}">{t}Terms and Conditions{/t}</a> {/t}</h4>
			</div>
				
				
			<div class="formFieldContainer">
				<h4>{t}Jingle/Music Name{/t}</h4>
				<p><input name="Title" type="text" class="long required" minlength="3" maxlength="55"/></p>
			</div>

			<div class="formFieldContainer">
				<h4>{t}Description{/t}</h4>
				<p><textarea name="Description"  class="long required" minlength="3"> </textarea></p>
			</div>
				
			<div class="formFieldContainer">
				<p><input type="submit" id="signup" value="Submit"></p>
			</div>
				<input type="hidden" name="fileName" id="fileNameStored">
		</form>	

		<br class="clearBoth">
	</div>
</div>

						
						
</div> <!-- Content Ends -->
</div></div>


{include file=$oView->getTemplateFile('momusicfooter','/shared')}
{*include file=$oView->getTemplateFile('footer','/shared')*}