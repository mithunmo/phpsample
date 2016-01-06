<h3><a href="#">{$title|default:'{t}Your Favourites{/t}'}</a></h3>
<div>
	<table id="favourites" class="data">
		<thead>
			<tr>
				<th>#</th>
				<th>Movie Title</th>
				<th>Author</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{if $oUser->getFavourites()->getCount() > 0}
				{foreach $oUser->getFavourites() as $oMovie}
				<tr>
					<td><input type="hidden" name="Favourites[{$index = $oMovie@iteration}{$index}][ID]" value="{$oMovie->getID()}" /><span class="recordNumber">{$index}</span></td>
					<td>{$oMovie->getTitle()|xmlstring}</td>
					<td>{$oMovie->getUser()->getFullname()|xmlstring}</td>
					<td><input type="checkbox" name="Favourites[{$index}][Remove]" value="1" class="addRemoveControl" id="remove-{$oMovie->getID()}" /></td>
				</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="4">{t}You have no favourites.{/t}</td>
				</tr>
			{/if}
		</tbody>
	</table>
</div>