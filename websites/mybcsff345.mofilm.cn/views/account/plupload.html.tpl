{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Upload a Movie{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body">
	<div class="container">
		
		<h3>MOFILM视频上传</h3>
		
		<form  id="plu" method="post" action="/account/upload/doPlupload">
			<div id="uploader">
				<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
			</div>
		</form>

		<form id="basicuploader" method="post" action="/account/upload/doBasicSave">
			<div class="formFieldContainer">
				<h4>{t}选择竞赛{/t}</h4>
				<p>		<select id ="eventUpload" name="EventID" class="long required">
						<option value="72"> 北京大学生电影节</option>
					</select>
				</p>
			</div>


			<div class="formFieldContainer">
				<h4>{t}选择品牌{/t}</h4>
				<p>		<select id="sourceUpload1" name="sourceID" class="long required">
							<option value="540">{t}Pepsi{/t}</option>
							<option value="541">{t}Renren{/t}</option>
						</select>

				</p>
			</div>

			<div class="formFieldContainer">
				<h4><input name="check" type="checkbox" class="required" />  {t}同意 <a target="_blank" href="{t}http://www.mofilm.com/info/uploadTerms{/t}">{t}Terms and Conditions{/t}</a> {/t}</h4>
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
				<h4>{t}输入音乐许可证信息 ( 如有，请填写相关信息；如无，请填写无 ){/t}</h4>
				<p><textarea name="customLicense"  class="long required" minlength="3"> </textarea></p>
			</div>
				
			<div class="formFieldContainer">
				<p><input type="submit" id="signup" value="保存视频"></p>
			</div>
				<input type="hidden" name="fileName" id="fileNameStored">
		</form>	

		<br class="clearBoth">
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}
{literal}
<script type="text/javascript" src="/libraries/plupload/js1/i18n/zh.js"></script>
{/literal}	

