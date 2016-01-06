<?php
/**
 * paymentDetailsView.class.php
 * 
 * paymentDetailsView class
 *
 * @author Poulami Chakraborty
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category paymentDetailsView
 * @version $Rev: 624 $
 */


/**
 * paymentDetailsView class
 * 
 * Provides the "paymentDetailsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category paymentDetailsView
 */
class paymentDetailsView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		/**
		 * @todo set these parameters
		 */
		$this->getEngine()->assign('parentController', 'admin');
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('paymentDetailsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('paymentDetailsForm');
	}
        
        function showPaymentListPage($paymentData,$paramData){
            parent::setupInitialVars();
            $this->addCssResource(new mvcViewCss('payment', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/payment.css?' . mofilmConstants::CSS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('paymentAutomcomplete', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/paymentAutocomplete.js?'.mofilmConstants::JS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('jqueryautocompletehtml', mvcViewJavascript::TYPE_FILE, '/libraries/jqueryautocomplete/jquery.ui.autocomplete.html.js'));
            $this->getEngine()->assign('oPaymentList', $paymentData);
            $this->getEngine()->assign('oParam', $paramData);
            $this->addJavascriptResource(new mvcViewJavascript('paymentAdmin', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/paymentAdmin.js?'.mofilmConstants::JS_VERSION));
            $this->getEngine()->assign('rawDaoSearchQuery', $this->getController()->getSearchQueryAsString());
	    $this->render($this->getTpl('paymentDetails'));
        }
        
        function showFinanceListPage($financeData,$paramData,$csvUpdatedMsg){
            parent::setupInitialVars();
            $this->addCssResource(new mvcViewCss('payment', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/payment.css?' . mofilmConstants::CSS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('paymentAutomcomplete', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/paymentAutocomplete.js?'.mofilmConstants::JS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('jqueryautocompletehtml', mvcViewJavascript::TYPE_FILE, '/libraries/jqueryautocomplete/jquery.ui.autocomplete.html.js'));
            $this->getEngine()->assign('oFinanceList', $financeData);
            $this->getEngine()->assign('oParam', $paramData);
            $this->getEngine()->assign('csvUpdatedMsg', $csvUpdatedMsg);
            $this->addJavascriptResource(new mvcViewJavascript('paymentAdmin', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/paymentAdmin.js?'.mofilmConstants::JS_VERSION));
            $this->getEngine()->assign('rawDaoSearchQuery', $this->getController()->getSearchQueryAsString());
            $this->render($this->getTpl('paymentDetails'));
        }
        
        function showCreatePaymentPage(){
            parent::setupInitialVars();
            $this->addCssResource(new mvcViewCss('payment', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/payment.css?' . mofilmConstants::CSS_VERSION));
            $this->addCssResource(new mvcViewCss('responsive', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/responsive.css?' . mofilmConstants::CSS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('paymentAutomcomplete', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/paymentAutocomplete.js?'.mofilmConstants::JS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('paymentAdmin', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/paymentAdmin.js?'.mofilmConstants::JS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('jqueryautocompletehtml', mvcViewJavascript::TYPE_FILE, '/libraries/jqueryautocomplete/jquery.ui.autocomplete.html.js'));
            
            $this->render($this->getTpl('addPaymentForm'));
        }
        
        function showAdvanceGrantPage($paymentDetails){
            parent::setupInitialVars();
            $this->addCssResource(new mvcViewCss('payment', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/payment.css?' . mofilmConstants::CSS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('paymentAdmin', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/paymentAdmin.js?'.mofilmConstants::JS_VERSION));
            $this->getEngine()->assign('oPaymentDetails', $paymentDetails);
            $this->render($this->getTpl('addAdvanceGrantForm'));
        }
        
        function financeView($paymentDetails,$params){
            parent::setupInitialVars();
            $this->addCssResource(new mvcViewCss('payment', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/payment.css?' . mofilmConstants::CSS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('paymentAdmin', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/paymentAdmin.js?'.mofilmConstants::JS_VERSION));
            $this->getEngine()->assign('oParams', $params);
            $this->getEngine()->assign('oPaymentDetails', $paymentDetails);
            $this->render($this->getTpl('editFinanceForm'));
        }
        
        function editPayment($paymentDetails,$params){
            parent::setupInitialVars();
            $this->addCssResource(new mvcViewCss('payment', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/payment.css?' . mofilmConstants::CSS_VERSION));
            $this->addCssResource(new mvcViewCss('featherlight', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/featherlight.css?' . mofilmConstants::CSS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('paymentAdmin', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/paymentAdmin.js?'.mofilmConstants::JS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('featherlight', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/featherlight.min.js?'.mofilmConstants::JS_VERSION));
            $this->getEngine()->assign('oPaymentDetails', $paymentDetails);
            $this->getEngine()->assign('oParams', $params);
            $this->render($this->getTpl('editPaymentForm'));
        }
        
        function editAdhocPayment($paymentDetails,$paymentID,$params){
            parent::setupInitialVars();
            $this->addCssResource(new mvcViewCss('payment', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/payment.css?' . mofilmConstants::CSS_VERSION));
            $this->addCssResource(new mvcViewCss('featherlight', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/featherlight.css?' . mofilmConstants::CSS_VERSION));
            $this->addCssResource(new mvcViewCss('responsive', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/responsive.css?' . mofilmConstants::CSS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('paymentAdmin', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/paymentAdmin.js?'.mofilmConstants::JS_VERSION));
            $this->addJavascriptResource(new mvcViewJavascript('featherlight', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/featherlight.min.js?'.mofilmConstants::JS_VERSION));
            $this->getEngine()->assign('oPaymentDetails', $paymentDetails);
            $this->getEngine()->assign('oPaymentID', $paymentID);
            $this->getEngine()->assign('oParams', $params);
            $this->render($this->getTpl('editAdhocPaymentForm'));
        }
        
        function showPaymentViewPage(){
            $this->render($this->getTpl('paymentView'));
        }
        
        function importPaymentList(){
            $this->render($this->getTpl('importView'));
        }
}