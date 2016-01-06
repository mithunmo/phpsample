<?xml version="1.0" encoding="UTF-8"?>
{if is_object($oMovie) && $oMovie->getID() > 0}
<mofilm col1="5e92dc" col2="ff6500" infobar="true" controlbar="true" related="false" vol="true" rated="false">
	<video>
		<flvClip>{$oMovie->getAssetSet()->getObjectByAssetAndFileType(mofilmMovieAsset::TYPE_FILE, 'FLV')->getFirst()->getCdnURL()|xmlstring}</flvClip>
		<flvPreviewImage>{$oMovie->getThumbnailUri('l')|xmlstring}</flvPreviewImage>
		<flvThumbnail>{$oMovie->getThumbnailUri('m')|xmlstring}</flvThumbnail>
		<flvClipTitle>{$oMovie->getTitle()|xmlstring}</flvClipTitle>
		<flvClipRating>{$oMovie->getAvgRating()}</flvClipRating>
		<flvDuration>{$oMovie->getRuntime()}</flvDuration>
		<flvClipAuthor>MOFILM</flvClipAuthor>
	</video>
</mofilm>
{else}
<mofilm col1="5e92dc" col2="ff6500" infobar="false" controlbar="true" related="false" vol="true" rated="false">
	<video>
		<flvClipAuthor>MOFILM</flvClipAuthor>
	</video>
</mofilm>
{/if}