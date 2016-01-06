{if $daoUriAction == 'viewObjects'}
	{include file=$oView->getObjectListView()}
{else}
<mofilm>
	<error>
		<message>{t}The requested action is not permitted during an XML request.{/t}</message>
	</error>
</mofilm>
{/if}