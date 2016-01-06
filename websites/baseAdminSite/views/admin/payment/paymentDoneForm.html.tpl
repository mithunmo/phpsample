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
				<form id="adminFormData" name="formData" method="post" action="/admin/payment/doPaymentDone" accept-charset="utf-8">
					<h2>{t}Enter Payment Details{/t}</h2>
					<div class="content">
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
                                                <a href="/admin/payment">
                                                <img src="{$themeicons}/32x32/other.png" alt="{t}View List{/t}" class="icon" />
                                                {t}View List{/t}</a>
                                            </div>
                                        <div class="clearBoth"></div>
					</div>

					<div class="content">
                                            <table class="data">
                                                <tbody>
                                                    <tr>
                                                        <th>User</th>
                                                        <th>{$oModel->getUserName($oObject->getUserID())}</th>
                                                    </tr>
                                                    <tr>
                                                        <th>{t}Payment date{/t}</th>
                                                        <th><input type="text" name="PaymentDate" value="" class="date datepicker" /></th>
                                                    </tr>
                                                    <tr>
                                                        <th>{t}Payment Description{/t}</th>
                                                        <th><textarea name="PaymentDesc" cols="70" rows="4"></textarea></th>
                                                         <input type="hidden" name="ID" value="{$oObject->getID()}" />
                                                         <input type="hidden" name="UserID" value="{$oObject->getUserID()}" />
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