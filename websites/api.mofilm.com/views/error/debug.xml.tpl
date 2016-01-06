{if !$isProduction}
	<debug>
		<basePath>{$basePath}</basePath>

		<exception>
			<line>{$oException->getLine()}</line>
			<file>{$oException->getFile()|replace:$basePath:''}</file>
			<message>{$oException->getMessage()|default:'No error message in exception'|xmlstring}</message>
		</exception>
		
		<stackTrace>
			{foreach name=stacktrace item=data from=$oException->getTrace()}
				<exception>
					<line>{$data->getArrayValue('line')}</line>
					<file>{if $data->getArrayValue('file')}{$data->getArrayValue('file')|replace:$basePath:''}{/if}</file>
					<command>{$data->getArrayValue('class')|xmlstring}{$data->getArrayValue('type')|xmlstring}{$data->getArrayValue('function')|xmlstring}</command>
					<message>{$oException->getMessage()|default:'No error message in exception'|xmlstring}</message>
				</exception>
			{/foreach}
		</stackTrace>
	</debug>
{/if}