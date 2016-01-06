<h3><a href="#">{t}MRSS Details{/t}</a></h3>
<div>
    <div class="formFieldContainer">
        <h4>{t}Select Distribution{/t}</h4>
        <p>
            <select name="dist">
                <option value=""> Select Distribution</option>
                {foreach $dist as $oDist}                
                    <option {if $movieChannel->getDistributionID() == $oDist->getID()}selected="selected"{/if} value="{$oDist->getID()}">{$oDist->getName()}</option>
                {/foreach}

            </select>            

        </p>
    </div>

    <div class="formFieldContainer">
        <h4>{t}Select Channel{/t}</h4>
        <p>
            <select name="channel">
                <option value=""> Select Channel</option>
                {foreach $channel as $oChannel}                
                    <option {if $movieChannel->getChannelID() == $oChannel->getID()}selected="selected"{/if} value="{$oChannel->getID()}">{$oChannel->getName()}</option>
                {/foreach}

            </select>            

        </p>
    </div>

    <div class="formFieldContainer">
        <h4>{t}Select Action{/t}</h4>
        <p>

            <select name="mrss_action">
                <option value=""> Select Action</option>
                <option {if $movieChannel->getAction() == "new"}selected="selected"{/if} value="new">New</option>
                <option {if $movieChannel->getAction() == "update"}selected="selected"{/if} value="update">Update</option>
                <option {if $movieChannel->getAction() == "delete"}selected="selected"{/if} value="delete">Delete</option>
            </select>            

        </p>
    </div>

    <div class="formFieldContainer">
        <h4>{t}Select Category{/t}</h4>
        <p>

            <select name="mrss_category">
                <option value=""> Select Category</option>
                <option {if $movieChannel->getCategory() == "drama"}selected="selected"{/if} value="drama">drama</option>
                <option {if $movieChannel->getCategory() == "comedy"}selected="selected"{/if} value="comedy">comedy</option>
                <option {if $movieChannel->getCategory() == "shortfilms"}selected="selected"{/if} value="shortfilms">shortfilms</option>
                <option {if $movieChannel->getCategory() == "documentary"}selected="selected"{/if} value="documentary">documentary</option>
                <option {if $movieChannel->getCategory() == "romance"}selected="selected"{/if} value="romance">romance</option>
                <option {if $movieChannel->getCategory() == "thriller"}selected="selected"{/if} value="thriller">thriller</option>
                <option {if $movieChannel->getCategory() == "action"}selected="selected"{/if} value="action">action</option>
                <option {if $movieChannel->getCategory() == "crimefiction"}selected="selected"{/if} value="crimefiction">crimefiction</option>
                <option {if $movieChannel->getCategory() == "animation"}selected="selected"{/if} value="animation">animation</option>
                <option {if $movieChannel->getCategory() == "indie"}selected="selected"{/if} value="indie">indie</option>
                <option {if $movieChannel->getCategory() == "horror"}selected="selected"{/if} value="horror">horror</option>
                <option {if $movieChannel->getCategory() == "blackandwhite"}selected="selected"{/if} value="blackandwhite">blackandwhite</option>
                <option {if $movieChannel->getCategory() == "family"}selected="selected"{/if} value="family">family</option>
                <option {if $movieChannel->getCategory() == "music"}selected="selected"{/if} value="music">music</option>
                <option {if $movieChannel->getCategory() == "adventure"}selected="selected"{/if} value="adventure">adventure</option>
            </select>            

        </p>
    </div>


</div>