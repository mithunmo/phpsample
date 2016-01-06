{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}uploadTrack{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body">
	<div class="container">
		{include file=$oView->getTemplateFile('statusMessage', '/shared')}

		<div class="floatLeft main">
			<h2>{t}Meta data for the Track{/t}</h2>
			<form method="post" action="/admin/other/momusic/uploadTrack/doEdit" enctype="multipart/form-data" name="musicform"> 
				<input type="hidden" name="ID" value="{$oWork->getID()}"/>
				<table class="data">
					<tbody>

						<tr>
							<th>{t} Song Name{/t}</th>
							<td><input type="text" name="SongName" value="{$oWork->getSongName()}" /></td>
						</tr>
						<tr>
							<th>{t} Artist Name{/t}</th>
							<td><input type="text" name="ArtistName" value="{$oWork->getArtistName()}" /></td>
						</tr>
						<tr>
							<th>{t} Album Name{/t}</th>
							<td><input type="text" name="AlbumName" value="{$oWork->getAlbum()}" /></td>
						</tr>


						<tr>
							<th>{t}Mood1{/t}</th>
							<td><select name="mood1">
									{foreach $oMood as $val}
										<option value="{$val->getName()}"  {if $val->getName()|lower  == $oWork->getMood1()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>
						</tr>
						<tr>
							<th>{t}Mood2{/t}</th>
							<td><select name="mood2">
									{foreach $oMood as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getMood2()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>
						</tr>
						<tr>
							<th>{t}Mood3{/t}</th>
							<td><select name="mood3">
									{foreach $oMood as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getMood3()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>
						</tr>



						<tr>
							<th>{t}Genre1{/t}</th>
							<td><select name="genre1">
									{foreach $oGenre as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getGenre1()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>
						</tr>
						<tr>
							<th>{t}Genre2{/t}</th>
							<td><select name="genre2">
									{foreach $oGenre as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getGenre2()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>
						</tr>
						<tr>
							<th>{t}Genre3{/t}</th>
							<td><select name="genre3">
									{foreach $oGenre as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getGenre3()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>
						</tr>


						<tr>
							<th>{t}Style1{/t}</th>
							<td><select name="style1">
									{foreach $oStyle as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getStyle1()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>
						</tr>
						<tr>
							<th>{t}Style2{/t}</th>
							<td><select name="style2">
									{foreach $oStyle as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getStyle2()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>
						</tr>
						<tr>
							<th>{t}Style3{/t}</th>
							<td><select name="style3">
									{foreach $oStyle as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getStyle3()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>
						</tr>


						<tr>
							<th>{t}Inst1{/t}</th>
							<td><select name="inst1">
									{foreach $oInst as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getInstrument1()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>							
						</tr>
						<tr>
							<th>{t}Inst2{/t}</th>
							<td><select name="inst2">
									{foreach $oInst as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getInstrument2()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>							
						</tr>
						<tr>
							<th>{t}Inst3{/t}</th>
							<td><select name="inst3">
									{foreach $oInst as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getInstrument3()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>							
						</tr>
						<tr>
							<th>{t}Inst4{/t}</th>
							<td><select name="inst4">
									{foreach $oInst as $val}
										<option value="{$val->getName()}" {if $val->getName()|lower  == $oWork->getInstrument4()|lower} selected="selected"{/if}> {$val->getName()} </option>
									{/foreach}	
								</select>
							</td>							
						</tr>



						<tr>
							<th>{t}Sounds like1{/t}</th>
							<td><input type="text" name="sl1" value="{$oWork->getSoundsLike1()}" /></td>
						</tr>
						<tr>
							<th>{t}Sounds like2{/t}</th>
							<td><input type="text" name="sl2" value="{$oWork->getSoundsLike2()}" /></td>
						</tr>
						<tr>
							<th>{t}Sounds like3{/t}</th>
							<td><input type="text" name="sl3" value="{$oWork->getSoundsLike3()}" /></td>

						</tr>

						<tr>
							<th>{t}Resembles Songs1{/t}</th>
							<td><input type="text" name="rs1" value="{$oWork->getResemblesSong1()}" /></td>
						</tr>
						<tr>
							<th>{t}Resembles Songs2{/t}</th>
							<td><input type="text" name="rs2" value="{$oWork->getResemblesSong2()}" /></td>
						</tr>
						<tr>
							<th>{t}Resembles Songs3{/t}</th>
							<td><input type="text" name="rs3" value="{$oWork->getResemblesSong3()}" /></td>

						</tr>




						<tr>
							<th>{t}Keywords{/t}</th>
							<td><input type="text" name="keywords" value="{$oWork->getKeywords()}" /></td>
						</tr>					
						<tr>
							<th>{t}Description{/t}</th>
							<td><input type="text" name="desc" value="{$oWork->getDescription()}" /></td>
						</tr>
						<tr>
							<th>{t}Composer{/t}</th>
							<td><input type="text" name="composer" value="{$oWork->getComposer()}" /></td>
						</tr>
						<tr>
							<th>{t}Writer{/t}</th>
							<td><input type="text" name="writer" value="{$oWork->getWriter()}" /></td>
						</tr>				
						<tr>
							<th>{t}Publisher{/t}</th>
							<td><input type="text" name="publisher" value="{$oWork->getPublisher()}" /></td>
						</tr>				
						<tr>
							<th>{t}Music Source{/t}</th>
							<td><input type="text" name="musicsource" value="{$oWork->getMusicSource()}" /></td>
						</tr>

						<tr>
							<th>{t}Status{/t}</th>
							<td><input type="text" name="status" value="{$oWork->getStatus()}" /></td>
						</tr>

						<tr>
							<th>{t}Priority{/t}</th>							
							<td><input type="text" name="priority" value="{$oWork->getPriority()}" /></td>
						</tr>






						<tr>
							<th>{t}Save{/t}</th>
							<td> 
								<input type="submit" name="save" value="Save" />
							</td>
						</tr>


					</tbody>
				</table>

			</form>

		</div>

		<br class="clearBoth" />
	</div>
</div>

{include file=$oView->getTemplateFile('footer', 'shared')}