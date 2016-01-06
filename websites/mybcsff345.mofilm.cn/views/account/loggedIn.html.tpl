{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Logged In{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft">
				<h2>{t}Welcome back {$oUser->getFirstname()}{/t}</h2>
				<p>{t}You have successfully logged in to Mofilm.{/t}</p>
				
				

                {$oView->getControllerView('pm', '/account', 'messageCheck')}

				<p>{t}Go to <a href="/account/profile" title="Your Profile">your profile</a>.{/t}</p>
				
				前往<a href="http://bcsff.mofilm.cn">大学生电影接MOFILM商业短片竞赛</a>
				
			</div>
			
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}