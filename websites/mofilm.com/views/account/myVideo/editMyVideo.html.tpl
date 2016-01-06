{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}User movie list{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
	<div class="container">
		<div class="content">
			{if $oMovie->getUploadStatusSet()->getVideoCloudID() > 0}
                            

                                    {if $oMovie->getID() <= 5000 }

                                            <video id="my_video_2" class="video-js vjs-default-skin" controls
                                                   preload="auto" width="527" height="338" data-setup='{ "techOrder": ["flash"] }'>
                                                <source src="rtmp://s1bzjrwrwz16bm.cloudfront.net/cfx/st&mp4:{$oMovie->getID()}/{$oMovie->getID()}.mp4" type='rtmp/mp4'>
                                            </video>


                                        {else}    
                            
                            
			<div class="mofilmMovieFrame">
				<div id="mofilmMoviePlayer">
						<!-- Start of Brightcove Player -->
						<div style="display:none"></div>

						<object id="myExperience" class="BrightcoveExperience">
							<param name="bgcolor" value="#FFFFFF" />
							<param name="width" value="604" />
							<param name="height" value="338" />
							<param name="playerID" value="1667919342001" />
							<param name="playerKey" value="AQ~~,AAAA8BM582E~,KSC10SyvF5JMYDrNum2TcfuJnVAPT0mT" />
							<param name="isVid" value="true" />
							<param name="isUI" value="true" />
							<param name="dynamicStreaming" value="true" />                                                        
							<param name="linkBaseURL" value="{$oMovie->getShortUri($oUser->getID(), true)}" />
                                                        <param name="secureConnections" value="true" />
                                                        <param name="secureHTMLConnections" value="true" />
							<param name="@videoPlayer" value="{$oMovie->getUploadStatusSet()->getVideoCloudID()}" />
						</object>
				</div>
			</div>
                         {/if}                           
                                                
			{else}
			<div class="mofilmPhotoFrame">
				<div id="mofilmPhotoPlayer">			    
					<div id="gallery">
						<div class="album">
							{assign var=imageslist value=$oMovie->getAssetSet()->getObjectByAssetType('Source')->getIterator()}
							{foreach $imageslist as $image}
								<div style="padding: 10px 10px 10px 10px; float: left;">
									<a class="fancybox" data-fancybox-group="gallery" href="{$image->getFilename()}" title="{$image->getNotes()}" >
										{assign var=temp value="{$image->getMovieID()}/thumbs"}
										{assign var=thumblink value="{$image->getFilename()|replace:$image->getMovieID():$temp|strstr:".":"true"}"}
										<img src="{$thumblink}.jpg" width="100" height="100" title="{$image->getNotes()}" />
									</a>
								</div>
							{/foreach}
						</div>
					</div>
				</div>
			</div>
			{/if}

		</div>
		<form  id="myVideoForm" action="{$doMovieSave}" method="post" name="profileForm" accept-charset="utf-8" enctype="multipart/form-data">		
			<div class="hidden">
				<input type="hidden" id="MasterMovieID" name="MovieID" value="{$oMovie->getID()}" />
			</div>

			<div id="userFormAccordion">

				<h3><a href="#">{t}Give credit to your creative team !{/t}</a></h3>
				<div>
					<table id="contributors" class="data">
						<thead>
							<tr>
								<th>#</th>
								<th>{t}Contributor Name{/t}</th>
								<th>{t}Role{/t}</th>
								<th>{t}Check to Delete{/t}</th>
							</tr>
						</thead>
						<tbody>
							{foreach $oMovie->getContributorSet() as $oContributorMap}	
								{if $oModel->getValidUser($oContributorMap->getContributor()->getName())}	
									<tr>
										<td><input type="hidden" name="Contributors[{$index = $oContributorMap@iteration}{$index}][ID]" value="{$oContributorMap->getContributor()->getID()}" /><span class="recordNumber">{$index}</span></td>
										<td><input placeholder="name" readonly="readonly" type="text" class="contributorUser string" name="Contributors[{$index}][Name]" value="{$oModel->getUserName($oContributorMap->getContributor()->getName())}" /></td>
										<td><div class="ui-widget"><input type="text" readonly="readonly" class="contributorRole small" name="Contributors[{$index}][Role]" value="{$oContributorMap->getRole()->getDescription()}" /></div></td>
										<td><input type="checkbox" name="Contributors[{$index}][Remove]" value="1"  /></td>
									</tr>
								{/if}
							{/foreach}
							<tr>
								<td><input type="hidden" name="Contributors[{$index+1}][ID]" value="0" /><span class="recordNumber">{$index+1}</span></td>
								<td><input type="text" class="contributorUser string" name="Contributors[{$index+1}][Name]" value="" /></td>
								<td><div class="ui-widget"><input type="text" class="contributorRole small" name="Contributors[{$index+1}][Role]" value="" /></div></td>
								<td><input class="addRemoveControl" type="checkbox" name="Contributors[{$index+1}][Remove]" value="1"  title="{t}Tick box to remove contributor{/t}" /></td>
								<td><input type="hidden" name="Contributors[{$index+1}][roleID]" value="0" /></td>
							</tr>
						</tbody>
					</table>
					<div class="controls hidden">
						<div class="floatRight">
							<div class="addContributor formIcon ui-state-default floatLeft" title="{t}Add New Contributor{/t}"><span class="ui-icon ui-icon-plusthick"></span></div>
							<div class="removeContributor formIcon ui-state-default floatLeft" title="{t}Remove Last Contributor{/t}"><span class="ui-icon ui-icon-minusthick"></span></div>
						</div>
						<div class="clearBoth"></div>
					</div>
				</div>

				<h3><a href="#">{t}Tags{/t}</a></h3>
				<div id="tagsTab">
					<div class="formFieldContainer">
						<div class="ui-widget">
							{assign var=i value=1}
							{foreach $newGenres as $oTags}
								<div style="width:570px; height:25px; padding:4px;"><strong>{$oTags@key}</strong></div>
								<div style="padding:4px;width:570px; overflow:auto;">
									{foreach $oTags as $oTag}
										<div style="float:left; width:185px;">
											{if $oTag->getCategory() == "Industry"}
												<input class="industry" type="checkbox" name="Tags[]" value="{$oTag->getID()}" {if $oMovie->getTagSet()->hasTag($oTag->getName())}checked{/if} />{$oTag->getName()}
											{else}
												<input type="checkbox" name="Tags[]" value="{$oTag->getID()}" {if $oMovie->getTagSet()->hasTag($oTag->getName())}checked{/if} />{$oTag->getName()}
											{/if}
										</div>
									{/foreach}
								</div>
							{/foreach}
						</div>
					</div>
				</div>
				{if $oMovie->getStatus() == 'Approved' || $oMovie->getStatus() == 'Pending'}
				<h3><a href="#">{t}MOFILM Filmmaker Pack - Upload Section{/t}</a></h3>
				<div id="ccaTab" class="floatLeft spacer">
					<div class="formFieldContainer">
						<div class="ui-widget">
							<div>
								{assign var=assetSet value=$oMovie->getAssetSet()->getObjectByAssetType(mofilmMovieAsset::TYPE_CCA)->getFirst()}
								{if $assetSet->getNotes() != 'CCA VERIFIED'}
									{if $assetSet->getID() > 0 && $assetSet->getFileExists() }
										<input type="file" name="ccaFile" id="ccaFile" class="string" onclick="r=confirm('File Already uploaded.Do you want to reupload?'); if (r==false) { return false; } else { return true; }"/>
										{t}FILE UPLOADED SUCESSFULLY.{/t}
										<br /><a href="/download/ccaDownloads/{$oMovie->getID()}">{t}Click Here to Download File{/t}</a>
									{else}
										<input type="file" name="ccaFile" id="ccaFile" class="string"/>
									{/if}
									<h5>{t}Note : You can compress all your files in Zip format and then upload !{/t}</h5>
								{else}
									{t}DOCUMENT VERIFIED.{/t}
									<a href="/download/ccaDownloads/{$oMovie->getID()}">{t}Click Here to Download File{/t}</a>
								{/if}
							</div>
						</div>
					</div>
				</div>
				{/if}
			</div>
			<div class="content">			
			<button type="submit" id="myVideoSave" name="UpdateProfile" value="Save" title="{t}Save{/t}">
				<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save{/t}" class="icon" />
				{t}Save Changes{/t}
			</button>
			</div>
		</form>
		<br class="clearBoth" />
			<div>
				<strong>{t}Credit Help{/t}</strong>
				<br class="clearBoth" />
				1. {t}Only registered mofilm user can be added as Credit/Contributors.{/t} 
				<br class="clearBoth" />
				2. {t}If you need to add a person who is not a mofilm user then enter the email address in contributor name and we shall invite for you.{/t} 
				<br class="clearBoth" />
				3. {t}Choose from the predefined list of roles . Enter the first charcter and choose from the list of options.If you don't find your role email support@mofilm.com{/t}
			</div>					
	</div>
</div>	
<script type="text/javascript">
	<!--
	var availableRoles = {$availableRoles};
	//var availableUsers = {*$availableUsers*};
	//-->
</script>						
{include file=$oView->getTemplateFile('footer', 'shared')}
