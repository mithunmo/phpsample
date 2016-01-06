{assign var=rootID value=$oModel->getRootID()}
{assign var=objects value=$oModel->getMusicType()}
<mofilm>
	<request>
	</request>
	<data>
		<source>
			<id>0</id>
			<name>Select Source</name>
		</source>
		{foreach $objects as $oObject}
		<source>
			<id>{$oObject->getID()}</id>
			<name>{$oObject->getName()|xmlstring}</name>
		</source>
		{/foreach}
	</data>
</mofilm>