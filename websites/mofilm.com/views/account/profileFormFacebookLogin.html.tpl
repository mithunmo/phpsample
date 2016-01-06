<h3><a href="#">{t}Login using Facebook{/t}</a></h3>
<div>
	<div id="fb-root"></div>
	<div class="formFieldContainer">
			<h4>
				{t}Login using your Facebook account : {/t}
				<div id="fbStatus" style="display:inline">
					{if $oUser->getFacebookID() != NULL }
						Enabled
					{else}
						Disabled
					{/if}
				</div>
			</h4>
			<div class="fbDataContainer">
					<div id="showDisableFBLogin" style="display: {if $oUser->getFacebookID() != NULL }''{else}none{/if}">
						<input id="disableFBLogin" type="checkbox" name="facebookID" value="" /> Disable login using Facebook account
					</div>
					<div id="showFbButton" style="display:  {if $oUser->getFacebookID() != NULL }none{else}''{/if}">
						<div id="fb_link_display" style="display: none">
							<div class="enableFBLoginLink"><a class="fb_button fb_button_medium"><span class="fb_button_text">Login with Facebook</span></a></div>
						</div>
					
						<div id="fb_button_display" style="display: none">	    
							<fb:login-button scope="publish_stream">Login with Facebook</fb:login-button>
						</div>
					</div>
			</div>
	</div>
</div>