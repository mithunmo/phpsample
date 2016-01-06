{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Upload Photo{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body">
	<div class="container">
		{if $error }
			<h3>{t}Invalid URL. Please check the link properly.{/t}</h3>
		{else}
		<h3>{t}MOFILM Photo Uploader{/t}</h3>
		<div style="width:648px;">
		<form id="photouploader" method="post" action="/account/upload/doUploadPhotoSave" enctype="multipart/form-data">
			<div style="width:310px; float:right;padding-left:10px; padding-top:5px; height:35px; background-color:#efeeee">
				<strong>{t}Brand{/t}</strong> : {$source->getName()}
				<input type="hidden" name="sourceID" value="{$source->getID()}" />
			</div>
			
			<div style="width:315px; float:left;padding-left:10px;padding-top:5px;height:35px; background-color:#efeeee">
				<strong>{t}Event{/t}</strong> : {$event->getName()}
				<input type="hidden" name="EventID" value="{$event->getID()}" />
			</div>
			
			<div style="clear:both; height:55px;  padding-left:10px; padding-bottom:5px; padding-top:15px; background-color:#f8f8f8;">
				<strong>{t}Title{/t}</strong> <br /> <input name="Title" type="text" class="long required" minlength="3" maxlength="55" id="Title"/>
				<span id="msg_title" style="color: red;"></span>
			</div>
			
			<div style="clear:both; height:85px;  padding-left:10px; padding-bottom:5px; padding-top:15px; background-color:#efeeee;">
				<strong>{t}Description{/t}</strong> <br /> <textarea name="Description"  class="long required" minlength="3" id="Description"> </textarea>
				<span id="msg_desc" style="color: red;"></span>
			</div>
			
			<div style="clear:both;  padding-left:10px; padding-bottom:5px; padding-top:15px; background-color:#f8f8f8; overflow: auto;">
				{t}Just Click on Browse to add multiple photos one at a time{/t}
				<input class="multi" type="file" name="Photos[]" style="background-color: #f8f8f8; border: none;" id="photoFileName" />
			</div>
			
			<div style="clear:both; height:25px;  padding-left:10px; padding-bottom:27px; padding-top:10px; background-color:#efeeee;">
				<h4><input name="check" type="checkbox" class="required" id="tnc" />  
				    {t}Agree to{/t}
				    <a target="_blank" href="http://www.mofilm.com/info/uploadTerms">
					{t}Terms and Conditions{/t}
				    </a>
				    <span id="msg_agree" style="color: red;"></span>
				</h4>
			</div>

			<div class="formFieldContainer">
				<p><input type="submit" id="signup" value="{t}Upload Photos{/t}"></p>
			</div>
		</form>	
		</div>
		{/if}
		<br class="clearBoth">
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}
