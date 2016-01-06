{assign var=i value=0}
<tr>
    <th>{t}List{/t}</th>
    <td><select id ="ListID">
    {foreach $oList as $oListObj}
	    <option value="{$oListObj->getID()}"> {$oListObj->getName()} </option>
    {/foreach}
    </select></td>
</tr>
<tr>
	<td><input type="submit" id="userAddList" value="Add"></td>
	<td><input type="submit" id="userDeleteList" value="Delete"></td>
	<td><input type="button" id="userSelectAll" value="Select All"></td>
	<td><input type="button" id="userUnSelectAll" value="Un-Select All"></td>
</tr>
<tr>
</tr>
{foreach $oResult as $oResultObj}
<tr>
	<th>{t}Record{/t}</th>
	<td>{$oResultObj->getEmail()}</td>
	<td><input type="checkbox" id="id{$i++}"></td>
	<td>{$oResultObj->getID()}</td>
</tr>
{/foreach}
