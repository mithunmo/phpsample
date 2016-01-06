{if !$isProduction}
	<div class="debug">
		<h2>Debug Data</h2>

		<h3>Base Path</h3>
		<p>{$basePath}</p>

		<h3>Error Details</h3>
		<p>Exception occured at line {$oException->getLine()} in file {$oException->getFile()|replace:$basePath:''}</p>
		<p>The specific message was:<br /><em>{$oException->getMessage()|default:'No error message in exception'}</em></p>

		<h4>Partial source code snippet from line {$oException->getLine()-5} to {$oException->getLine()+5}</h4>
		{highlightSource file=$oException->getFile() start=$oException->getLine()-5 end=$oException->getLine()+5}

		<h3>Exception stack trace:</h3>
		<table>
			<thead>
				<tr>
					<th>Line</th>
					<th>Class</th>
					<th>Type</th>
					<th>Function</th>
					<th>File</th>
				</tr>
			</thead>
			<tbody>
			{foreach name=stacktrace item=data from=$oException->getTrace()}
				<tr>
					<td>{$data->getArrayValue('line')}</td>
					<td>{$data->getArrayValue('class')}</td>
					<td>{$data->getArrayValue('type')}</td>
					<td>{$data->getArrayValue('function')}</td>
					<td>
						{if $data->getArrayValue('file')}
							{assign var=it value=$smarty.foreach.stacktrace.iteration}
							<span id="fileSourceToggle{$it}" class="fileSourceToggle" onclick="toggleSource('fileSourceToggle{$it}','fileSource{$it}');" title="Toggle Partial Source">
								{$data->getArrayValue('file')|replace:$basePath:''} [+]
							</span>
							<div id="fileSource{$it}" class="fileSource" style="display: none;">
								<h4>Partial source dode snippet from line {$data->getArrayValue('line')-5} to {$data->getArrayValue('line')+5}</h4>
								{highlightSource file=$data->getArrayValue('file') start=$data->getArrayValue('line')-5 end=$data->getArrayValue('line')+5}
							</div>
						{/if}
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	<script type="text/javascript">
	{literal}
		function toggleSource(toggle, source) {
			oTog = document.getElementById(toggle);
			oSource = document.getElementById(source);

			if ( oSource.style.display == 'none' ) {
				oSource.style.display = 'block';
				oTog.innerHTML = oTog.innerHTML.replace('+', '-');
			} else {
				oSource.style.display = 'none';
				oTog.innerHTML = oTog.innerHTML.replace('-', '+');
			}
		}
	{/literal}
	</script>
{/if}