{capture assign=xml}
{'<?xml version="1.0" encoding="UTF-8"?>'}
<mofilm>
	<request>
		<eventID>{$oResults->getSearchInterface()->getEvents()|xmlstring}</eventID>
		<sourceID>{$oResults->getSearchInterface()->getSources()|xmlstring}</sourceID>
		<keywords>{$oResults->getSearchInterface()->getKeywords()|xmlstring}</keywords>
		<offset>{$oResults->getSearchInterface()->getOffset()|xmlstring}</offset>
		<limit>{$oResults->getSearchInterface()->getLimit()|xmlstring}</limit>
		<totalResults>{$oResults->getTotalResults()|xmlstring}</totalResults>
	</request>
	<results>
{foreach $oResults as $oMovie}
		<movie id="{$oMovie->getID()}">
			<shortDescription>{$oMovie->getShortDesc()|xmlstring}</shortDescription>
			<longDescription>{$oMovie->getLongDesc()|xmlstring}</longDescription>
			<runtime>{$oMovie->getRuntime()|convertSecondsToMinutes}</runtime>
			<date>{$oMovie->getDateModified()|date_format:'%d %b %Y'}</date>
			<rating>{$oMovie->getAvgRating()|xmlstring}</rating>
			<ratingCount>{$oMovie->getRatingCount()|xmlstring}</ratingCount>
			<producer>{$oMovie->getUser()->getFullname()|xmlstring}</producer>
		</movie>
{/foreach}
	</results>
</mofilm>
{/capture}
{if $jsonCallback}
{$jsonCallback}({
	data: '{$xml|asJson}'
});
{else}
{
	data: '{$xml|asJson}'
}
{/if}

{*
[_AssetSet:protected] => 
[_AwardSet:protected] => 
[_CategorySet:protected] => 
[_CommentSet:protected] => 
[_ContributorSet:protected] => 
[_DataSet:protected] => 
[_HistorySet:protected] => 
[_SourceSet:protected] => 
[_TagSet:protected] => 
[_TrackSet:protected] => 
[_Modified:protected] => 
[_ID:protected] => 2956
[_UserID:protected] => 11839
[_Status:protected] => Approved
[_Active:protected] => Y
[_ShortDesc:protected] => wal5.wmv
[_LongDesc:protected] => 
[_Runtime:protected] => 37
[_ProductionYear:protected] => 
[_Uploaded:protected] => 2010-03-06 17:00:16
[_DateModified:protected] => 2010-03-06 17:33:22
[_Moderated:protected] => 2010-03-06 17:33:22
[_ModeratorID:protected] => 7727
[_ModeratorComments:protected] => 
[_AvgRating:protected] => 8
[_RatingCount:protected] => 1
*}