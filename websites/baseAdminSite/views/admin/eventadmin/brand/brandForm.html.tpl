{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden">
            <input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" />
             <input type="hidden" name="ID" value="{$oObject->getID()|default:0}" />
        </div>
	<table class="data">
            <tbody>
                <tr>
                    <th>{t}Name{/t}</th>
                    <td><input type="text" name="Name" value="{$oObject->getName()}" /></td>
                </tr>
                <tr>
                    <th>{t}Corporate {/t}</th>
                    <td>
                        {corporateDistinctSelect id="corporateList" name='CorporateID' selected=$oObject->getCorporateID() class="valignMiddle string" }
                    </td>
                </tr> 
                <tr>
                    <th>{t}Industry {/t}</th>
                    <td>
                        {industrySectorSelect id="industryList" name='IndustryID' selected=$oObject->getIndustryID() class="valignMiddle string" }
                    </td>
                </tr> 
                <tr>
                    <th>{t}Firstname{/t}</th>
                    <td><input type="text" name="Firstname" value="{$oObject->getFirstname()}" /></td>
                </tr>
                <tr>
                    <th>{t}Lastname{/t}</th>
                    <td><input type="text" name="Lastname" value="{$oObject->getLastname()}" /></td>
                </tr>
                <tr>
                    <th>{t}Address{/t}</th>
                    <td><input type="text" name="Address" value="{$oObject->getAddress()}" /></td>
                </tr>
                <tr>
                    <th>{t}City{/t}</th>
                    <td><input type="text" name="City" value="{$oObject->getCity()}" /></td>
                </tr>
                <tr>
                    <th>{t}Country{/t}</th>
                    <td><input type="text" name="Country" value="{$oObject->getCountry()}" /></td>
                </tr>
                <tr>
                    <th>{t}Email{/t}</th>
                    <td><input type="text" name="Email" value="{$oObject->getEmail()}" /></td>
                </tr>
                <tr>
                    <th>{t}Phone{/t}</th>
                    <td><input type="text" name="Phone" value="{$oObject->getPhone()}" /></td>
                </tr>
                <tr>
                    <th>{t}Status{/t}</th>
                    <td><input type="text" name="Status" value="{$oObject->getStatus()}" /></td>
                </tr>
            </tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getName()}&quot;?{/t}</p>
{/if}