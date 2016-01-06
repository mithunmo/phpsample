{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Users - Message{/t} - '|cat:$oObject->getEmail()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<h2>{t}Users - Message{/t} - {if $oObject->getFullname() && strlen($oObject->getFullname()) > 3}{$oObject->getFullname()}{else}{$oObject->getEmail()}{/if}</h2>
			<form id="messageForm" action="{$doMessageUri}" method="post" name="messageForm">
				<div class="content">
					<div class="daoAction">
						<a href="javascript:history.go(-1);" title="{t}Previous Page{/t}">
							<img src="{$themeicons}/32x32/action-back.png" alt="{t}Previous Page{/t}" class="icon" />
							{t}Previous Page{/t}
						</a>
						{if $oController->hasAuthority('usersController.edit')}
							<a href="{$editUri}/{$oObject->getID()}" title="{t}User Details{/t}">
								<img src="{$themeicons}/32x32/action-edit-object.png" alt="{t}User Details{/t}" class="icon" />
								{t}User Details{/t}
							</a>
						{/if}
						{if $oController->hasAuthority('usersController.doMessage')}
						<button type="submit" name="SendMessage" value="Send" title="{t}Send Message{/t}">
							<img src="{$themeicons}/32x32/action-send.png" alt="{t}Send Message{/t}" class="icon" />
							{t}Send Message{/t}
						</button>
						{/if}
					</div>
					<div class="clearBoth"></div>
				</div>
			
				<div class="content">
					<ul>
						<li>{t}You can send a private message to this user by completing the form and clicking "Send Message".{/t}</li>
						<li>{t}The user will be notified that they have a new message (if alerts are enabled).{/t}</li>
						<li>{t}Be courteous and clear; remember that what you say reflects on MOFILM.{/t}</li>
						<li>{t}Please note: all messages are logged.{/t}</li>
					</ul>
					
					<div id="userFormAccordion">
						<h3><a href="#">{t}Message Details{/t}</a></h3>
						<div>
							<div class="hidden">
								<input type="hidden" name="UserID" value="{$oObject->getID()}" />
								{foreach $messageParams as $param => $value}
								<input type="hidden" name="MsgParams[{$param|xmlstring}]" value="{$value|xmlstring}" />
								{/foreach}
								{if $oModel->getMovieID()}
								<input type="hidden" name="MovieID" value="{$oModel->getMovieID()}" />
								{/if}
							</div>
							<table class="data">
								<tbody>
									<tr>
										<th>{t}To{/t}</th>
										<td>{if strlen($oObject->getFullname()) > 3}{$oObject->getFullname()}{else}{$oObject->getEmail()}{/if}</td>
									</tr>
									<tr>
										<th>{t}Subject{/t}</th>
										<td><input type="text" name="Subject" value="{if $oModel->getMovieID()}Re: {$oModel->getMovie()->getTitle()|xmlstring} (#{$oModel->getMovieID()}){/if}" class="long" /></td>
									</tr>
									{if $oModel->getMovieID()}
										<tr>
											<th>{t}Message Template{/t}</th>
											<td>
												<select id="MsgTemplateID" name="TemplateID" size="1">
													<option value="0">{t}Select message template{/t}</option>
													{foreach $appMessages as $oMessage}
													<option value="{$oMessage->getMessageID()}">{$oMessage->getMessageHeader()|xmlstring}</option>
													{/foreach}
												</select>
											</td>
										</tr>
									{/if}
									<tr>
										<th class="valignTop">{t}Message{/t}</th>
										<td><textarea id="MessageBody" name="Message" rows="12" cols="70" class="long"></textarea></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</form>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}