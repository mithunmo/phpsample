<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
	<li class="ui-state-default ui-corner-top {if $selected == 'current'}ui-tabs-selected ui-state-active ui-state-focus{/if}"><a href="/user/current">{t}Top Filmmakers Last 12 Months{/t}</a></li>
	<li class="ui-state-default ui-corner-top {if $selected == 'alltime'}ui-tabs-selected ui-state-active ui-state-focus{/if}"><a href="/user/alltime">{t}Top Filmmakers of All Time{/t}</a></li>
	<li class="ui-state-default ui-corner-top {if $selected == 'country'}ui-tabs-selected ui-state-active ui-state-focus{/if}"><a href="/user/country">{t}Top Filmmakers by Country{/t}</a></li>
	<li>
		<div style="width:230px; height:31px; float:right; text-align:right; padding-top:4px; padding-right:20px; background-repeat:no-repeat; background-image:url('/themes/mofilm/images/search.gif')" >
			<!--input class="text ui-widget-content ui-corner-all" type="text" id="uSearch" name="userSearch" value="Search"-->
			<input style="height:13px; width:165px; border-style:none; border-color:white; outline:none; " type="text; font-size:10px;" id="uSearch" name="userSearch">
		</div>
	</li>
</ul>