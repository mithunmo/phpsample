{if $oController->hasAuthority($oController->getClassName()|cat:'.editObject') && !$hideEdit}
<a href="{$daoUriEdit}/{$oObject->getPrimaryKey()}" title="{t}Edit object{/t}" class="edit"><img src="{$themeicons}/32x32/action-edit-object.png" alt="{t}Edit object{/t}" class="icon" /></a>
{/if}
{if $oController->hasAuthority($oController->getClassName()|cat:'.deleteObject') && !$hideDelete}
<a href="{$daoUriDelete}/{$oObject->getPrimaryKey()}" title="{t}Delete object{/t}" class="delete"><img src="{$themeicons}/32x32/action-delete-object.png" alt="{t}Delete object{/t}" class="icon" /></a>
{/if}