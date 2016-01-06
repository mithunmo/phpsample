<div id="body" style="display:">

		<form id="grantsApprovalEmailCommunication" class="userGrantsApprovalEmailCommunication" name="userGrantsApprovalEmailCommunication" method="post" action="/grants/doSendEmail">
			<div class="content">
				<div class="formFieldContainer">
					<h2>To</h2>
					<div class="">
						{$oUserMovieGrant->getUser()->getPropername()} < {$oUserMovieGrant->getUser()->getEmail()} >
					</div>
				</div>
				<div class="formFieldContainer">
					<h2>Message to Film-Maker</h2>
					<div class="">
						<textarea name="EmailMessage" id="EmailMessage" cols="120" rows="10"></textarea>
					</div>
				</div>			    
				<div>
					<input type="hidden" name="FilmMakerID" id="FilmMakerID" value="{$oUserMovieGrant->getUser()->getID()}" />
					<input type="hidden" name="GrantID" id="GrantID" value="{$oUserMovieGrant->getID()}" />
					<input type="button" class="submit" name="submitEmailCommunication" id="EmailCommunicationSend" value="Send" />
				</div>
				<br class="clearBoth">
			</div>
			<br class="clearBoth">
		</form>

</div>
<div id="bodyMessage" style="display:none"> sdfds fdsf</div>
<script type="text/javascript" src="/libraries/core_js/core.js"></script>
<script type="text/javascript" src="/libraries/jquery/jquery.min.js"></script>
<script type="text/javascript" src="/libraries/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/libraries/mofilm/admin.js?{mofilmConstants::JS_VERSION}"></script>