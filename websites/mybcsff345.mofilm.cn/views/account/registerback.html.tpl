{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Register New User{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
{literal}
	<style type="text/css">
		#registerForm {background: url(/themes/mofilm/images/mofilmcn/login-bg-fb.jpg) no-repeat; }
	</style>
{/literal}

<div id="body">
	<div class="container">
		{include file=$oView->getTemplateFile('statusMessage', '/shared')}

		<div class="floatLeft main">
			<form id="registerForm" action="{$doRegisterUri}" method="post" name="registerForm" class="dropShadow">
				<h1 class="noMargin">{t}用户注册{/t}</h1>

				<div id="profilecontents">
					<dl>

						<dt>{t}用户名{/t}</dt>
						<dd><input id="profileName" name="ProfileName" type="text" value="{$formData.ProfileName}" class="required string" /></dd>

						<div id="nameField">
							<dt>{t}姓{/t}</dt>
							<dd><input id="surName" name="Surname" type="text" value="{$formData.Surname}" class="required string" /></dd>

							<dt>{t}名{/t}</dt>
							<dd><input id="firstName" name="Firstname" type="text" value="{$formData.Firstname}" class="required string" /></dd>
						</div>

						<dt>{t}邮箱地址{/t}</dt>
						<dd><input id="emailAddress" name="username" type="text" value="{$formData.username}" class="required string" /></dd>

						<dt>{t}密码{/t}</dt>
						<dd><input name="Password" type="password" class="required" /></dd>

						<dt>{t}确认密码{/t}</dt>
						<dd><input name="ConfirmPassword" type="password" class="required" /></dd>

						<div id="cityField">
							{if $formData.City}
								<dt>{t}City{/t}</dt>
								<dd><input name="City" type="text" value="{$formData.City}" class="string" /></dd>
								{/if}
						</div>

	<!--dt>{t}国家/地区{/t}</dt>
	<dd>{*territorySelect selected=$formData.territory|default:$oCountry->getID() name='territory'*}</dd-->
						<dt>国家/地区</dt>
						<dd><select name="territory">
								<option value="0">Not selected</option>
								<option value="1">Afghanistan</option>
								<option value="2">Åland Islands</option>
								<option value="3">Albania</option>
								<option value="4">Algeria</option>
								<option value="5">American Samoa</option>
								<option value="6">Andorra</option>
								<option value="7">Angola</option>
								<option value="8">Anguilla</option>
								<option value="9">Antarctica</option>
								<option value="10">Antigua And Barbuda</option>
								<option value="11">Argentina</option>
								<option value="12">Armenia</option>
								<option value="13">Aruba</option>
								<option value="14">Australia</option>
								<option value="15">Austria</option>
								<option value="16">Azerbaijan</option>
								<option value="17">Bahamas</option>
								<option value="18">Bahrain</option>
								<option value="19">Bangladesh</option>
								<option value="20">Barbados</option>
								<option value="21">Belarus</option>
								<option value="22">Belgium</option>
								<option value="23">Belize</option>
								<option value="24">Benin</option>
								<option value="25">Bermuda</option>
								<option value="26">Bhutan</option>
								<option value="27">Bolivia</option>
								<option value="28">Bosnia And Herzegovina</option>
								<option value="29">Botswana</option>
								<option value="30">Bouvet Island</option>
								<option value="31">Brazil</option>
								<option value="32">British Indian Ocean Territory</option>
								<option value="33">Brunei Darussalam</option>
								<option value="34">Bulgaria</option>
								<option value="35">Burkina Faso</option>
								<option value="36">Burundi</option>
								<option value="37">Cambodia</option>
								<option value="38">Cameroon</option>
								<option value="39">Canada</option>
								<option value="40">Cape Verde</option>
								<option value="41">Cayman Islands</option>
								<option value="42">Central African Republic</option>
								<option value="43">Chad</option>
								<option value="44">Chile</option>
								<option value="45" selected="selected">中国大陆</option>
								<option value="46">Christmas Island</option>
								<option value="47">Cocos (Keeling) Islands</option>
								<option value="48">Colombia</option>
								<option value="49">Comoros</option>
								<option value="50">Congo</option>
								<option value="51">Congo, The Democratic Republic Of The</option>
								<option value="52">Cook Islands</option>
								<option value="53">Costa Rica</option>
								<option value="54">Côte D'ivoire</option>
								<option value="55">Croatia</option>
								<option value="56">Cuba</option>
								<option value="57">Cyprus</option>
								<option value="58">Czech Republic</option>
								<option value="59">Denmark</option>
								<option value="60">Djibouti</option>
								<option value="61">Dominica</option>
								<option value="62">Dominican Republic</option>
								<option value="63">Ecuador</option>
								<option value="64">Egypt</option>
								<option value="65">El Salvador</option>
								<option value="66">Equatorial Guinea</option>
								<option value="67">Eritrea</option>
								<option value="68">Estonia</option>
								<option value="69">Ethiopia</option>
								<option value="70">Falkland Islands (Malvinas)</option>
								<option value="71">Faroe Islands</option>
								<option value="72">Fiji</option>
								<option value="73">Finland</option>
								<option value="74">France</option>
								<option value="75">French Guiana</option>
								<option value="76">French Polynesia</option>
								<option value="77">French Southern Territories</option>
								<option value="78">Gabon</option>
								<option value="79">Gambia</option>
								<option value="80">Georgia</option>
								<option value="81">Germany</option>
								<option value="82">Ghana</option>
								<option value="83">Gibraltar</option>
								<option value="84">Greece</option>
								<option value="85">Greenland</option>
								<option value="86">Grenada</option>
								<option value="87">Guadeloupe</option>
								<option value="88">Guam</option>
								<option value="89">Guatemala</option>
								<option value="90">Guernsey</option>
								<option value="91">Guinea</option>
								<option value="92">Guinea-Bissau</option>
								<option value="93">Guyana</option>
								<option value="94">Haiti</option>
								<option value="95">Heard Island And Mcdonald Islands</option>
								<option value="97">Honduras</option>
								<option value="98">Hong Kong</option>
								<option value="99">Hungary</option>
								<option value="100">Iceland</option>
								<option value="101">India</option>
								<option value="102">Indonesia</option>
								<option value="103">Iran, Islamic Republic Of</option>
								<option value="104">Iraq</option>
								<option value="105">Ireland</option>
								<option value="106">Isle Of Man</option>
								<option value="107">Israel</option>
								<option value="108">Italy</option>
								<option value="109">Jamaica</option>
								<option value="110">Japan</option>
								<option value="111">Jersey</option>
								<option value="112">Jordan</option>
								<option value="113">Kazakhstan</option>
								<option value="114">Kenya</option>
								<option value="115">Kiribati</option>
								<option value="116">Korea, Democratic People's Republic Of</option>
								<option value="117">Korea, Republic Of</option>
								<option value="118">Kuwait</option>
								<option value="119">Kyrgyzstan</option>
								<option value="120">Lao People's Democratic Republic</option>
								<option value="121">Latvia</option>
								<option value="122">Lebanon</option>
								<option value="123">Lesotho</option>
								<option value="124">Liberia</option>
								<option value="125">Libyan Arab Jamahiriya</option>
								<option value="126">Liechtenstein</option>
								<option value="127">Lithuania</option>
								<option value="128">Luxembourg</option>
								<option value="129">Macao</option>
								<option value="130">Macedonia, The Former Yugoslav Republic</option>
								<option value="131">Madagascar</option>
								<option value="132">Malawi</option>
								<option value="133">Malaysia</option>
								<option value="134">Maldives</option>
								<option value="135">Mali</option>
								<option value="136">Malta</option>
								<option value="137">Marshall Islands</option>
								<option value="138">Martinique</option>
								<option value="139">Mauritania</option>
								<option value="140">Mauritius</option>
								<option value="141">Mayotte</option>
								<option value="142">Mexico</option>
								<option value="143">Micronesia, Federated States Of</option>
								<option value="144">Moldova, Republic Of</option>
								<option value="145">Monaco</option>
								<option value="146">Mongolia</option>
								<option value="147">Montenegro</option>
								<option value="148">Montserrat</option>
								<option value="149">Morocco</option>
								<option value="150">Mozambique</option>
								<option value="151">Myanmar</option>
								<option value="152">Namibia</option>
								<option value="153">Nauru</option>
								<option value="154">Nepal</option>
								<option value="155">Netherlands</option>
								<option value="156">Netherlands Antilles</option>
								<option value="157">New Caledonia</option>
								<option value="158">New Zealand</option>
								<option value="159">Nicaragua</option>
								<option value="160">Niger</option>
								<option value="161">Nigeria</option>
								<option value="162">Niue</option>
								<option value="163">Norfolk Island</option>
								<option value="164">Northern Mariana Islands</option>
								<option value="165">Norway</option>
								<option value="166">Oman</option>
								<option value="167">Pakistan</option>
								<option value="168">Palau</option>
								<option value="169">Palestinian Territory, Occupied</option>
								<option value="170">Panama</option>
								<option value="171">Papua New Guinea</option>
								<option value="172">Paraguay</option>
								<option value="173">Peru</option>
								<option value="174">Philippines</option>
								<option value="175">Pitcairn</option>
								<option value="176">Poland</option>
								<option value="177">Portugal</option>
								<option value="178">Puerto Rico</option>
								<option value="179">Qatar</option>
								<option value="180">Réunion</option>
								<option value="181">Romania</option>
								<option value="182">Russian Federation</option>
								<option value="183">Rwanda</option>
								<option value="184">Saint Barth</option>
								<option value="185">Saint Helena</option>
								<option value="186">Saint Kitts And Nevis</option>
								<option value="187">Saint Lucia</option>
								<option value="188">Saint Martin</option>
								<option value="189">Saint Pierre And Miquelon</option>
								<option value="190">Saint Vincent And The Grenadines</option>
								<option value="191">Samoa</option>
								<option value="192">San Marino</option>
								<option value="193">Sao Tome And Principe</option>
								<option value="194">Saudi Arabia</option>
								<option value="195">Senegal</option>
								<option value="196">Serbia</option>
								<option value="197">Seychelles</option>
								<option value="198">Sierra Leone</option>
								<option value="199">Singapore</option>
								<option value="200">Slovakia</option>
								<option value="201">Slovenia</option>
								<option value="202">Solomon Islands</option>
								<option value="203">Somalia</option>
								<option value="204">South Africa</option>
								<option value="205">South Georgia And The South Sandwich Isl</option>
								<option value="206">Spain</option>
								<option value="207">Sri Lanka</option>
								<option value="208">Sudan</option>
								<option value="209">Suriname</option>
								<option value="210">Svalbard And Jan Mayen</option>
								<option value="211">Swaziland</option>
								<option value="212">Sweden</option>
								<option value="213">Switzerland</option>
								<option value="214">Syrian Arab Republic</option>
								<option value="215">Taiwan</option>
								<option value="216">Tajikistan</option>
								<option value="217">Tanzania, United Republic Of</option>
								<option value="218">Thailand</option>
								<option value="219">Timor-Leste</option>
								<option value="220">Togo</option>
								<option value="221">Tokelau</option>
								<option value="222">Tonga</option>
								<option value="223">Trinidad And Tobago</option>
								<option value="224">Tunisia</option>
								<option value="225">Turkey</option>
								<option value="226">Turkmenistan</option>
								<option value="227">Turks And Caicos Islands</option>
								<option value="228">Tuvalu</option>
								<option value="229">Uganda</option>
								<option value="230">Ukraine</option>
								<option value="231">United Arab Emirates</option>
								<option value="232">United Kingdom</option>
								<option value="233">United States</option>
								<option value="234">United States Minor Outlying Islands</option>
								<option value="235">Uruguay</option>
								<option value="236">Uzbekistan</option>
								<option value="237">Vanuatu</option>
								<option value="238">Vatican City State</option>
								<option value="239">Venezuela, Bolivarian Republic Of</option>
								<option value="240">Viet Nam</option>
								<option value="241">Virgin Islands, British</option>
								<option value="242">Virgin Islands, U.s.</option>
								<option value="243">Wallis And Futuna</option>
								<option value="244">Western Sahara</option>
								<option value="245">Yemen</option>
								<option value="246">Zambia</option>
								<option value="247">Zimbabwe</option>
							</select>
						</dd>


						<dt>{t}您是怎么知道MOFILM的?{/t}</dt>
						<dd><select name="SignupCode" class="required">
								<option value="">选择选项</option>	
								<option value="12">搜索引擎</option>
								<option value="13">社交网络</option>
								<option value="14">社交网络广告</option>
								<option value="15">视频竞赛新闻或者相关</option>
								<option value="16">MOFILM Live 活动或者演讲</option>
								<option value="17">朋友介绍</option>
							</select>
						</dd>							

						<dt>{t}生日{/t} <sup>1</sup></dt>
						<dd>{html_select_date start_year='1900' field_order='DMY' prefix='' field_array='DateOfBirth' month_format='%m' time=$formData.dob id='dobID'}</dd>

							<!--dt>{t}MOFILM Live! Code{/t} <sup>2</sup></dt>
							<dd><input name="SignupCode" type="text" value="{*$formData.SignupCode*}" class="small" /></dd-->

						<dt>{t}接收新闻与活动通知{/t} <sup>2</sup></dt>
						<dd><input type="checkbox" name="optIn" value="1" checked="checked" /></dd>

						<dt>&nbsp;</dt>
						<dd>
							<input type="submit" name="submit" value="{t}注册帐户{/t}" class="submit signup" />
							<input type="hidden" name="_sk" value="{$formSessionKey}" />
							<input id="regSource" name="registrationSource" type="hidden" value="{$formData.registrationSource}" />
							<input id="facebookID" name="facebookID" type="hidden" value="{$formData.facebookID}" />
						</dd>
					</dl>

				</div>
				
				<div style="clear:both;"> </div>
				<ol class="registrationNotes">
				<br />
				<br />
				<div>
					<h4>注册声明*</h4>
					<p>您所填写的信息将仅用于北京大学生电影节MOFILM商业短片竞赛参赛，不会用于其他用途，且会被严格保密。 </p>
				</div>	
					
					<li>当参加MOFILM竞赛活动时您已经16岁或以上</li>
					<li>MOFILM会时不时的发送竞赛活动,MOFILM Live活动和相关新闻邮件至您的邮箱, 如果您不希望接收这些信息请取消此项</li>
				</ol>
			</form>

			<br class="clearBoth" />
		</div>

		<div class="floatRight registerBar">
			<h3>{t}用户注册{/t}</h3>
			<p>
				如果您需要使用MOFILM网站的全部功能,请注册.
				MOFILM尊重您的隐私权,我们承诺不会将您的信息转交给第三方或者给您发送垃圾邮件.
			</p>
			<hr />
			<p>在您注册前,请阅读 <a href="http://www.mofilm.cn/terms-conditions/" title="MOFILM: Registered User Agreement">注册用户须知</a></p>
			<hr />
			<p><a href="/account/activation">{t}没有收到激活邮件?{/t}</a></p>

			<p class="alignCenter noMargin">
				<a href="http://www.mofilm.cn/competitions/lastest-competitions/" title="{t}MOFILM: Open Competitions{/t}"><img src="{$themeimages}/competitions-open.jpg" alt="open" style="width: 90px; height: 90px;" /></a>
				&nbsp;&nbsp;
				<a href="http://www.mofilm.cn/competitions/past-competitions/" title="{t}MOFILM: Past Competitions{/t}"><img src="{$themeimages}/competitions-past.jpg" alt="past" style="width: 90px; height: 90px;" /></a>
			</p>
			<p>
				<a href="http://bcsff.mofilm.cn/" title="MOFILM北京大学生电影节竞赛单元"><img src="/themes/mofilm/images/mofilmcn/bcsfficon.jpg" alt="bcsff" ></a>
			</p>
			
		</div>

		<br class="clearBoth" />
	</div>
</div>

{include file=$oView->getTemplateFile('footer', 'shared')}