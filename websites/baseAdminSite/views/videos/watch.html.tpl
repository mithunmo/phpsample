{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Videos - Watch - {/t}'|cat:$oModel->getMovieID()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h2>{t}Videos - Watch{/t} - {$oMovie->getTitle()}</h2>
			<div class="floatLeft movieDetails">
				<div class="content">
					<div class="daoAction">
						<a href="javascript:history.go(-1);" title="{t}Previous Page{/t}">
							<img src="{$themeicons}/32x32/action-back.png" alt="{t}Previous Page{/t}" class="icon" />
							{t}Previous Page{/t}
						</a>
						{if $oController->hasAuthority('videosController.edit')}
							<a href="{$editURI}/{$oMovie->getID()}" title="{t}Edit this Movie{/t}">
								<img src="{$themeicons}/32x32/action-edit-object.png" alt="{t}Edit Movie{/t}" class="icon" />
								{t}Edit Movie{/t}
							</a>
						{/if}
					</div>
					<div class="clearBoth"></div>
				</div>

				<div class="content">
					<div id="userFormAccordion">
						<h3><a href="#">{t}Movie Details{/t}</a></h3>
						<div>
							{if $oMovie->getUploadStatusSet()->getVideoCloudID() > 0 }
							<div class="mofilmMovieFrame">
								<div id="mofilmMoviePlayer">
									<!-- Start of Brightcove Player -->
									<div style="display:none"></div>

									    <object id="myExperience" class="BrightcoveExperience">
										<param name="bgcolor" value="#FFFFFF" />
										<param name="width" value="604" />
										<param name="height" value="338" />
										<param name="playerID" value="1031454470001" />
										<param name="playerKey" value="AQ~~,AAAA8BM582E~,KSC10SyvF5K1T463DarRIpcRiEz7MYg0" />
										<param name="isVid" value="true" />
										<param name="dynamicStreaming" value="true" />
										<param name="linkBaseURL" value="{$oMovie->getShortUri($oUser->getID(), true)}" />

										<param name="@videoPlayer" value="{$oMovie->getUploadStatusSet()->getVideoCloudID()}" />
									    </object>
								</div>
							</div>
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
							<div class="formFieldContainer">
								<h3>{$oMovie->getTitle()}</h3>
								<p>{t}Movie credits: {/t} {$oMovie->getCreditText()}</p>
								<p>{$oMovie->getDescription()|xmlstring}</p>
								
								<table class="data">
									<thead>
										<tr>
											<th>{t}Movie ID{/t}</th>
											<th>{t}Uploaded{/t}</th>
											<th>{t}Duration{/t}</th>
											<th>{t}Status{/t}</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>{$oMovie->getID()}</td>
											<td>{$oMovie->getUploadDate()|date_format:'%d-%m-%Y'}</td>
											<td>{if $oMovie->getDuration()}{$oMovie->getDuration()-8}{else}0{/if} {t}seconds{/t}</td>
											<td>{$oMovie->getStatus()}</td>
										</tr>
									</tbody>
								</table>
							</div>
							
							{if $oController->hasAuthority('getTinyUrl')}
								<div class="formFieldContainer">
									<h4>{t}Short URI{/t}</h4>
									<p><input type="text" name="ShortUri" value="{$oMovie->getShortUri($oUser->getID(), true)}" readonly="readonly" class="long" onclick="this.focus();this.select();" /></p>
								</div>
							{/if}
						</div>
						{if $oMovie->getUploadStatusSet()->getVideoCloudID() > 0 }
						<h3><a href="#">{t}Movie Assets{/t}</a></h3>
						<div>
							<div class="formFieldContainer">
								<h4>{t}Thumbnail URI Small{/t}</h4>
								<p><input type="text" name="ThumbNailSmall" value="{$oMovie->getThumbnailUri('s')}" readonly="readonly" class="long" onclick="this.focus();this.select();" /></p>
								
								<h4>{t}Thumbnail URI Large{/t}</h4>
								<p><input type="text" name="ThumbNailLarge" value="{$oMovie->getThumbnailUri('m')}" readonly="readonly" class="long" onclick="this.focus();this.select();" /></p>
								
								{if $oMovie->getFLVUri()}
									<h4>{t}FLV URI{/t}</h4>
									<p><a href="{$oMovie->getFLVUri()}"><img border="0" alt="Click here to Download FLV" src="/themes/mofilm/images/downloading.png" height="100" width="100"></a></p>
								{/if}
							</div>
						</div>
						{/if}
					</div>
				</div>
			</div>

			<div class="floatLeft movieSidebar">
				{include file=$oView->getTemplateFile('addToFavourites','videos') textLabels=true}
			
				{include file=$oView->getTemplateFile('profileMiniView', '/account') oUser=$oMovie->getUser() title='{t}User Profile{/t}'}
			</div>
			
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}