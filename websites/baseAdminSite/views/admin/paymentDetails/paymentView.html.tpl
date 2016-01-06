{include file=$oView->getTemplateFile('header', 'shared') pageTitle=''}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body">
  
    <link rel="stylesheet" type="text/css" href="{$themefolder}/css/payment.css"  />
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
    <div style="max-width:940px; margin:0px auto; padding-top: 40px;">
        <div style="margin-bottom: 20px;">
        <h2>Payment Advice</h2>
        </div>
    </div>
    <section class="tabsection">

        <input id="tab1" type="radio" name="tabs" checked>
        <label for="tab1">All Payments</label>

        <input id="tab2" type="radio" name="tabs">
        <label for="tab2">Compliance</label>

        <input id="tab3" type="radio" name="tabs">
        <label for="tab3">Finance</label>


        <div class="paymentcontent">
            <div id="content1">

               
		 <nav id="paymentnav" role="navigation" style="display: inline-block;">
                    <a href="#paymentnav" title="Show navigation">Show navigation</a>
                    <a href="#paymentnav" title="Hide navigation">Hide navigation</a>
                    <ul>

                        <li>
                            <a href="/" >Event/Project</a>
                            <ul>
                                <li><a href="/">1</a>
                                </li>
                                <li><a href="/">2</a>
                                </li>
                                <li><a href="/">3</a>
                                </li>
                                <li><a href="/">4</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="/" aria-haspopup="true">Brand</a>
                            <ul>
                                <li><a href="/">1</a>
                                </li>
                                <li><a href="/">2</a>
                                </li>
                                <li><a href="/">3</a>
                                </li>
                            </ul>
                        </li>
                        <li><a href="/">Filmmaker name</a>
                        </li>
                        <li>
                            <a href="/" aria-haspopup="true">type</a>
                            <ul>
                                <li><a href="/">1</a>
                                </li>
                                <li><a href="/">2</a>
                                </li>
                                <li><a href="/">3</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="/" aria-haspopup="true">Status</a>
                            <ul>
                                <li><a href="/">1</a>
                                </li>
                                <li><a href="/">2</a>
                                </li>
                                <li><a href="/">3</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="/" aria-haspopup="true">Created on</a>
                            <ul>
                                <li><a href="/">1</a>
                                </li>
                                <li><a href="/">2</a>
                                </li>
                                <li><a href="/">3</a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="/" aria-haspopup="true">Till Date</a>
                            <ul>
                                <li><a href="/">1</a>
                                </li>
                                <li><a href="/">2</a>
                                </li>
                                <li><a href="/">3</a>
                                </li>
                            </ul>
                        </li>

                        <li class="paysearch" style="width:35px;">
                            <a href="#">&nbsp;</a>
                        </li>

                    </ul>
                </nav>
                <div class="rightwrapper">
                    <span class="pluspay"><img src="/themes/mofilm/images/payment/newpay.png" /></span>
                    <a class="newpay" href="/admin/paymentDetails/addPayment"> Create Payment</a>
                </div>

		{include file=$oView->getTemplateFile('paymentDetailsList')}
                </div>
            </div>
    </section>
    </div>
{include file=$oView->getTemplateFile('footer', 'shared')}


