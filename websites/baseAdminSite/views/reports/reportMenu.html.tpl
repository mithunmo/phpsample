<div class="title">
	<p>
		{if $oModel->isDaemonRunning()}
		<img src="{$themeimages}/messagebox/success.png" alt="available" title="{t}Reporting Available{/t}" class="msgIcon" /> {t}Reporting Available{/t}
		{else}
		<img src="{$themeimages}/messagebox/error.png" alt="offline" title="{t}Reporting is Offline{/t}" class="msgIcon" /> {t}Reporting Offline{/t}
		{/if}
	</p>
	<p>
		<img src="{$themeimages}/messagebox/information.png" alt="info" title="{t}Important Information{/t}" class="msgIcon floatLeft spacer" style="margin-bottom: 50px;" />
		{t}Report downloads are available for {$cacheTTL}. To re-download after this time, refresh the report.{/t}
	</p>
</div>

<br />

<div class="title">
	<p>
		<a href="{$reportNewUri}">
			<img src="{$themeicons}/32x32/action-new-object.png" alt="new" title="{t}New Report{/t}" class="icon" /> {t}New Report{/t}
		</a>
	</p>
	
	<p>
		<a href="{$reportInboxUri}">
			<img src="{$themeicons}/32x32/mail-folder-inbox.png" alt="inbox" title="{t}Report Inbox{/t}" class="icon" /> {t}Report Inbox{/t}
		</a>
	</p>
	
	<p>
		<a href="{$reportScheduleUri}">
			<img src="{$themeicons}/32x32/schedule.png" alt="schedule" title="{t}Report Schedule{/t}" class="icon" /> {t}Report Schedule{/t}
		</a>
	</p>
</div>