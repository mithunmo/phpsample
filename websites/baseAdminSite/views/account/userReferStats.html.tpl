<h3>{t}Referral Bonus{/t}</h3>
<dl class="userProfile">
	<dt><img src="/themes/shared/icons/refer.png" alt="user" class="smallIcon"/></dt>
	<dd>
		{t}Referred By{/t} : 
		<a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'users/'}{$oMovie->getReferrer()->getID()}{'?token='}{$accessToken}" title="{t}Edit User Details{/t}">{$oMovie->getReferrer()->getFullname()|truncate:25:'..'|xmlstring}</a>
	</dd>
	
	<dt><img src="/themes/shared/icons/date.png" alt="date" class="smallIcon"/></dt>
	<dd>{t}Days since Registered{/t} : {$oMovie->getReferredDays()}</dd>
</dl>