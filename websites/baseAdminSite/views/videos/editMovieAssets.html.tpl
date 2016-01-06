<h3><a href="#">{t}Movie Assets & Download{/t}</a></h3>
<div>
    {if $oMovie->getUploadStatusSet()->getVideoCloudID() > 0 }
        <div class="formFieldContainer">
            <h4>{t}Thumbnail URI Small{/t}</h4>
            <p><input type="text" name="ThumbNailSmall" value="{$result->thumbnailURL}" readonly="readonly" class="long" onclick="this.focus();
                        this.select();" /></p>

            <h4>{t}Thumbnail URI Large{/t}</h4>
            <p><input type="text" name="ThumbNailLarge" value="{$result->videoStillURL}" readonly="readonly" class="long" onclick="this.focus();
                        this.select();" /></p>

            <h4>{t}MP4 Downloads{/t}</h4>
            {if $oMovie->getID() <= 5000 }
                    <div class="adminAssets" align="center">
                        <a href="/download/movie/?url=http://s3.amazonaws.com/mofilm-video/{$oMovie->getID()}/{$oMovie->getID()}.mp4"><img border="0" alt="Click here to Download FLV" src="/themes/mofilm/images/downloading.png" height="100" width="100"></a>
                    </div>

            {else}   
                {foreach $result->renditions as $value}
                    <div class="adminAssets" align="center">
                        {assign var=size value=$value->size}
                        <b>Size :</b> {math equation="$size/1000000" format="%.2f"} MB
                        <br />
                        {assign var=er value=$value->encodingRate}
                        <b>Encodingrate :</b>  {math equation="$er/1000000" format="%.2f"} Mbps
                        <br />
                        <a href="/download/movie/?url={$value->url}"><img border="0" alt="Click here to Download FLV" src="/themes/mofilm/images/downloading.png" height="100" width="100"></a>
                    </div>
                {/foreach}
            {/if}
        </div>
    {else}
    </form>
    <form id="photoDownloadForm" action="/videos/doPhotoDownload" method="post" name="photoDownloadForm">
        <div>
            <div id="mofilmPhotoPlayer">
                {assign var=imageslist value=$oMovie->getAssetSet()->getObjectByAssetType('Source')->getIterator()}
                {foreach $imageslist as $image}
                    <div style="padding: 10px 10px 10px 10px; float: left;">
                        <input type="checkbox" name="downloadImage[]" value="{$image->getID()}" />
                        {assign var=temp value="{$image->getMovieID()}/thumbs"}
                        {assign var=thumblink value="{$image->getFilename()|replace:$image->getMovieID():$temp|strstr:".":"true"}"}
                        <img src="{$thumblink}.jpg" width="100" height="100" title="{$image->getNotes()}" />
                    </div>
                {/foreach}
            </div>
            <div class="actions">
                <input type="hidden" id="MasterMovieID" name="MovieID" value="{$oMovie->getID()}" />
                <button type="submit" name="photoDownload" value="Save" title="{t}Save{/t}" id="photoDownload">
                    <img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Download Selected Photos{/t}" class="icon" />
                    {t}Download Selected Photos{/t}
                </button>
            </div>
        </div>
    {/if}
</div>
