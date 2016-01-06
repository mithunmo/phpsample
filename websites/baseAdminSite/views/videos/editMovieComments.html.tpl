<h3><a href="#">{t}Comments{/t} ({$oMovie->getCommentSet()->getCount()})</a></h3>
<div>
	{include file=$oView->getTemplateFile('editMovieCommentsList')}
	
	{if !$hideCommentBox && $oUser->isAuthorised('canComment')}
		<div id="movieCommentBox">
			<script type="text/javascript">
			document.write(
				'<button id="movieCommentPost" type="button" name="SaveComment" value="Save" title="{t}Save Comment{/t}" class="floatRight">{t}Save Comment{/t}</button>' +
				'<input type="hidden" id="movieCommentMovieID" value="{$oMovie->getID()}" />'
			);
			</script>
			
			<h4>{t}Add new comment{/t}</h4>
			<textarea id="movieComment" name="Comment" rows="5" cols="60" style="width: 600px;"></textarea>
			<noscript>
				<p class="noMargin"><em>{t}Click "Save Changes" at the top of the page to post your comment.{/t}</em></p>
			</noscript>
		</div>
	{/if}
</div>