<h3>{t}Referral Bonus{/t}</h3>
<dl class="userProfile">
	<dt><img src="/themes/shared/icons/refer.png" alt="user" class="smallIcon"/></dt>
	<dd>
		{t}Referred By{/t} : 
		<a href="/users/edit/{$oMovie->getReferrer()->getID()}" title="{t}Edit User Details{/t}">{$oMovie->getReferrer()->getFullname()|truncate:25:'..'|xmlstring}</a>
	</dd>
	
	<dt><img src="/themes/shared/icons/date.png" alt="date" class="smallIcon"/></dt>
	<dd>{t}Days since Registered{/t} : {$oMovie->getReferredDays()}</dd>
</dl>