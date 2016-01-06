{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}System Status Information{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{generatePath controller=$oMap->getUriPath() parent='admin'}
			</div>

			<div class="floatLeft main">
				<h2>{$oMap->getDescription()}</h2>
				<div class="content">
					<div id="adminActions" class="body">
						<p class="noMargin">{t}Shows information about the system including daemon runtime information.{/t}</p>
					</div>
					<div class="clearBoth"></div>
				</div>

				<div class="content">
					<div class="body">
						<h3 class="noMargin">{t}Daemons{/t}</h3>
						<table class="data">
							<thead>
								<tr>
									<th>{t}Daemon{/t}</th>
									<th>{t}Status{/t}</th>
									<th>{t}Process ID{/t}</th>
									<th>{t}Last Updated{/t}</th>
								</tr>
							</thead>
							<tbody>
							{foreach $oModel->getDaemons() as $oProcInfo}
								<tr class="{cycle values="alt,"}">
									<td>{$oProcInfo->getProcessName()|xmlstring}</td>
									<td>{$oProcInfo->getProperty(cliProcessInformation::PROP_STATUS)|xmlstring}</td>
									<td>{$oProcInfo->getProcessID()}</td>
									<td>{$oProcInfo->getProperty(cliProcessInformation::PROP_LAST_UPDATED)}</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
				</div>
					
				<div class="content">
					<div class="body">
						<h3 class="noMargin">{t}MySQL Stats{/t}</h3>
						<table id="serverstatustraffic" class="data">
							<thead>
								<tr>
									<th colspan="2">{t}Traffic{/t}</th>
									<th>&oslash; per hour</th>
								</tr>
							</thead>
							<tbody>
								<tr class="{cycle values="alt,"}">
									<th class="name">{t}Received{/t}</th>
									<td class="value">{$oModel->getNumberFormat($server_status.Bytes_received)}</td>
									<td class="value">{$oModel->getNumberFormat($server_status.Bytes_received*$hour_factor)}</td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th class="name">{t}Sent{/t}</th>
									<td class="value">{$oModel->getNumberFormat($server_status.Bytes_sent)}</td>
									<td class="value">{$oModel->getNumberFormat($server_status.Bytes_sent*$hour_factor)}</td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th class="name">{t}Total{/t}</th>
									<td class="value">{$oModel->getNumberFormat($server_status.Bytes_received+$server_status.Bytes_sent)}</td>
									<td class="value">{assign var=total value=$server_status.Bytes_received+$server_status.Bytes_sent}{assign var=hourly value=$oModel->getNumberFormat($total*$hour_factor)}{$hourly}</td>
								</tr>
							</tbody>
						</table>

						<table id="serverstatusconnections" class="data">
							<thead>
								<tr>
									<th colspan="2">{t}Connections{/t}</th>
									<th>&oslash; per hour</th>
									<th>%</th>
								</tr>
							</thead>
							<tbody>
								<tr class="{cycle values="alt,"}">
									<th class="name">{t}Max Connections{/t}</th>
									<td class="value">{$server_status.Max_used_connections}</td>
									<td class="value">--- </td>
									<td class="value">--- </td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th class="name">{t}Failed attempts{/t}</th>
									<td class="value">{$server_status.Aborted_connects}</td>
									<td class="value">{$server_status.Aborted_connects*$hour_factor|string_format:"%.2f"}</td>
									<td class="value">{if $server_status.Connections > 0}{assign var=sum value=$server_status.Aborted_connects*100}{assign var=total value=$sum/$server_status.Connections}{$total|string_format:"%.2f"}%{else}---{/if}</td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th class="name">{t}Aborted{/t}</th>
									<td class="value">{$server_status.Aborted_clients}</td>
									<td class="value">{$server_status.Aborted_clients*$hour_factor|string_format:"%.2f"}</td>
									<td class="value">{if $server_status.Connections > 0}{assign var=sum value=$server_status.Aborted_clients*100}{assign var=total value=$sum/$server_status.Connections}{$total|string_format:"%.2f"}%{else}---{/if}</td>
								</tr>
								<tr class="{cycle values="alt,"}">
									<th class="name">{t}Total{/t}</th>
									<td class="value">{$server_status.Connections}</td>
									<td class="value">{$server_status.Connections*$hour_factor|string_format:"%.2f"}</td>
									<td class="value">100%</td>
							</tbody>
						</table>
					</div>
				</div>
					
				<div class="content">
					<div class="body">
						<h3 class="noMargin">{t}MySQL Query Stats{/t}</h3>
						<table id="serverstatusqueriessummary" class="data">
							<thead>
								<tr>
									<th>{t}Total{/t}</th>
									<th>&oslash; per hour</th>
									<th>&oslash; per minute</th>
									<th>&oslash; per second</th>
								</tr>
							</thead>
							<tbody>
								<tr class="{cycle values="alt,"}">
									<td class="value">{$server_status.Questions}</td>
									<td class="value">{$server_status.Questions*$hour_factor|string_format:"%.2f"}</td>
									<td class="value">{assign var=sum value=$server_status.Questions*60}{assign var=total value=$sum/$server_status.Uptime}{$total|string_format:"%.2f"}</td>
									<td class="value">{$server_status.Questions/$server_status.Uptime|string_format:"%.2f"}</td>
								</tr>
							</tbody>
						</table>

						<div id="serverstatusqueriesdetails">
							{assign var=current_table value=0}
							{assign var=odd_row value=true}
							{assign var=countRows value= 0}

							{foreach name=loop key=name item=value from=$sections.com.vars}
								{assign var=current_table value=$current_table+1}

								{if $countRows === 0 || $countRows === $rows_per_table}
									{assign var=odd_row value=true}
									{if $countRows === $rows_per_table}
										</tbody>
										</table>
									{/if}

									<table id="serverstatusqueriesdetails{$current_table}" class="data">
										<col class="namecol" />
										<col class="valuecol" span="3" />
										<thead>
											<tr>
												<th colspan="2">Query Type</th>
												<th>&oslash; per hour</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
								{/if}

								{assign var=countRows value=$countRows+1}
								{assign var=name value=$name|replace:'Com_':''}
								{assign var=name value=$name|replace:'_':' '}
									<tr class="{cycle values="alt,"}">
										<th class="name">{$name|xmlstring}</th>
										<td class="value">{$value|string_format:"%.2f"}</td>
										<td class="value">{$value*$hour_factor|string_format:"%.2f"}</td>
										<td class="value">{$value*$perc_factor|string_format:"%.2f"}%</td>
									</tr>
							{/foreach}
								</tbody>
							 </table>
						</div>

						<div class="clearBoth"></div>
					</div>
				</div>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}