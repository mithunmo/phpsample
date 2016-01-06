{include file=$oView->getTemplateFile('header', 'shared') pageTitle=$oMap->getDescription()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
				<form id="adminFormData" name="formData" method="post" action="/admin/payment/doChangeStatus" accept-charset="utf-8">
					<h2>{t}Payment Status{/t}</h2>
					 <div class="daoAction">
                                            <button type="reset" name="Cancel" title="{t}Reset Changes{/t}">
                                                    <img src="{$themeicons}/32x32/action-undo.png" alt="{t}Reset Changes{/t}" class="icon" />
                                                    {t}Reset Changes{/t}
                                            </button>
                                            {if $oController->hasAuthority('usersController.doEdit')}
                                            <button type="submit" name="UpdateProfile" value="Save" title="{t}Save Changes{/t}">
                                                    <img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save Changes{/t}" class="icon" />
                                                    {t}Save Changes{/t}
                                            </button>
                                            {/if}
                                            <button type="submit" name="View List" title="{t}View List{/t}">
                                                <img src="{$themeicons}/32x32/other.png" alt="{t}View List{/t}" class="icon" />
                                                {t}View List{/t}
                                            </button>
                                        </div>
                                        <div class="clearBoth"></div>
					<div class="content">
                                            <table class="data">
                                                <tbody>
                                                    <tr>
                                                        <th>{t}User Name{/t}</th>
                                                        <th>{$oModel->getUserName($oObject->getUserID())}</th>
                                                    </tr>
                                                    <tr>
                                                        <th>{t}Submitter Comments{/t}</th>
                                                        <th>{$oObject->getComments()}</th>
                                                    </tr>
                                                    <tr>
                                                        <th>{t}Amount Grant{/t}</th>
                                                        <th>${$oObject->getAmountGrant()}</th>
                                                    </tr>
                                                    <tr>
                                                        <th>{t}Status{/t}</th>
                                                        <th>
                                                            <select name="Status">
                                                                <option value="Pending" selected>Pending</option>
                                                                <option value="Payment Approved">Approved</option>
                                                                <option value="Rejected">Rejected</option>
                                                            </select>
                                                        </th>
                                                    </tr>
                                                     <tr>
                                                        <th>{t}Approver Comments{/t}</th>
                                                        <th><textarea cols="70" rows="4" name="ApproverComments"></textarea></th>
                                                        <input type="hidden" name="ID" value="{$oObject->getID()}" />
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="clearBoth"></div>
					</div>
				</form>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}