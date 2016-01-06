<?php
/**
 * paymentDetailsController
 *
 * Stored in paymentDetailsController.class.php
 * 
 * @author Poulami Chakraborty
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category paymentDetailsController
 * @version $Rev: 624 $
 */


/**
 * paymentDetailsController
 *
 * paymentDetailsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category paymentDetailsController
 */
class paymentDetailsController extends mvcDaoController {
    
        const ACTION_CREATE_PAYMENT = 'addPayment';
        const ACTION_DO_ADD_PAYMENT = 'doAddPayment';
        const ACTION_ADVANCE_GRANT = 'addAdvanceGrant';
        const ACTION_DO_ADVANCE_GRANT = 'doAdvanceGrant';
        const ACTION_VIEW = 'viewPayment';
        const ACTION_VIEW_ACCOUNT_MANAGER = 'viewAccountManger';
        const ACTION_VIEW_COMPLIANCE = 'viewCompliance';
        const ACTION_NEW_PAYMENTS = 'newPayments';
        const ACTION_DUE_PAYMENTS = 'duePayments';
        const ACTION_VIEW_FINANCE = 'viewFinance';
        const ACTION_EDIT = 'edit';
        const ACTION_DO_SAVE_EDIT = 'doSaveEdit';
        const ACTION_EDIT_ADHOC = 'editAdhoc';
        const ACTION_DO_EDIT_ADHOC = 'doEditAdhoc';
        const ACTION_CHANGE_STATUS_ADHOC = 'doChangeStatusAdhoc';
        const ACTION_CHANGE_STATUS = 'doChangeStatus';
        const ACTION_FINANCE_EDIT = 'financeEdit';
        const ACTION_DO_FINANCE_EDIT = 'doFinanceEdit';
        const ACTION_EXPORT = 'export';
        const ACTION_IMPORT = 'import';
        const ACTION_CONFIRM = 'confirmPayment';
        
        protected $_CallBackQuery;
        protected $_PageType;
        
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		$this->setRequiresAuthentication(true);
		$this->getControllerActions()
				->addAction(self::ACTION_CREATE_PAYMENT)
                                ->addAction(self::ACTION_DO_ADD_PAYMENT)
                                ->addAction(self::ACTION_VIEW)
                                ->addAction(self::ACTION_VIEW_ACCOUNT_MANAGER)
                                ->addAction(self::ACTION_VIEW_COMPLIANCE)
                                ->addAction(self::ACTION_VIEW_FINANCE)
                                ->addAction(self::ACTION_NEW_PAYMENTS)
                                ->addAction(self::ACTION_DUE_PAYMENTS)
                                ->addAction(self::ACTION_EDIT)
                                ->addAction(self::ACTION_DO_SAVE_EDIT)
                                ->addAction(self::ACTION_CHANGE_STATUS)
                                ->addAction(self::ACTION_EDIT_ADHOC)
                                ->addAction(self::ACTION_DO_EDIT_ADHOC)
                                ->addAction(self::ACTION_CHANGE_STATUS_ADHOC)
                                ->addAction(self::ACTION_ADVANCE_GRANT)
                                ->addAction(self::ACTION_DO_ADVANCE_GRANT)
                                ->addAction(self::ACTION_FINANCE_EDIT)
                                ->addAction(self::ACTION_DO_FINANCE_EDIT)
                                ->addAction(self::ACTION_IMPORT)
                                ->addAction(self::ACTION_CONFIRM)
                                ->addAction(self::ACTION_EXPORT);
		$this->addInputFilters();
                $this->setSearchQuery(array());
                
	}
        
        function launch(){
            switch($this->getAction()){
                case self::ACTION_VIEW:
                                $oView = new paymentDetailsView($this);
                                $oView->showAdvanceGrantPage();
                                break;
                case self::ACTION_ADVANCE_GRANT:
                                $paymentID = $this->getActionFromRequest(false, 1);
                                $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
                                $paymentDetails = $this->getModel()->getPaymentDetails($paymentID,$sessionUserID);
                                $oView = new paymentDetailsView($this);
                                $oView->showAdvanceGrantPage($paymentDetails['paymentDetails'][0]);
                                break;
                                 break;
                case self::ACTION_DO_ADVANCE_GRANT:
                                if($this->doAddPayment('advanceGrantPayment')){
                                    $this->redirect('/admin/paymentDetails');
                                }
                                break;
                case self::ACTION_CREATE_PAYMENT:
                                $oView = new paymentDetailsView($this);
                                $oView->showCreatePaymentPage();
                                break;
                case self::ACTION_DO_ADD_PAYMENT:
                                if($this->doAddPayment('adhocPayment')){
                                    $this->redirect('/admin/paymentDetails');
                                }
                                break;
                case self::ACTION_CHANGE_STATUS:
                                $editParam = array();
                                $editParam['status']   = $this->getActionFromRequest(false, 1);
                                if($editParam['status'] == 'ApprovedDraft'){
                                    $editParam['status'] = 'Pending Approval';
                                }  
                                $editParam['ID']       = $this->getActionFromRequest(false, 2);  
                                $sessionUserID  = $this->getRequest()->getSession()->getUser()->getID();
                                $returnVal = $this->getModel()->doEditPayment($editParam,$sessionUserID);
                                $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
                                $data = $this->getInputManager()->doFilter();
                                $this->setCallBackQuery($data);
                                if(isset($returnVal['budgetExceeded']) && $returnVal['budgetExceeded'] == 1){
                                    $this->redirect('/admin/paymentDetails/edit/'.$editParam['ID'].'?'.$this->getCallBackQuery().'&budgetExceeded=1');
                                }else{
                                    $this->redirect('/admin/paymentDetails/edit/'.$editParam['ID'].'?'.$this->getCallBackQuery());
                                }
                                break;                        
                case self::ACTION_CHANGE_STATUS_ADHOC:
                                $editParam = array();
                                $editParam['status']   = $this->getActionFromRequest(false, 1);
                                $paymentRedirection       = $this->getActionFromRequest(false, 2); 
                                $editParam['ID']       = $this->getActionFromRequest(false, 3);  
                                $sessionUserID  = $this->getRequest()->getSession()->getUser()->getID();
                                $returnVal = $this->getModel()->doEditAdhocPayment($editParam,$sessionUserID);
                                if($returnVal){
                                    if(isset($returnVal['budgetExceeded']) && $returnVal['budgetExceeded'] == 1){
                                        $this->redirect('/admin/paymentDetails/editAdhoc/'.$paymentRedirection.'?budgetExceeded=1');
                                    }else{
                                        $this->redirect('/admin/paymentDetails/editAdhoc/'.$paymentRedirection);
                                    }
                                }
                                break;             
                case self::ACTION_FINANCE_EDIT :
                                $editParam = array();
                                $editParam['param']   = $this->getActionFromRequest(false, 1);
                                $editParam['ID']      = $this->getActionFromRequest(false, 2);
                                $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
                                $paymentDetails = $this->getModel()->getPaymentDetails($editParam['ID'],$sessionUserID);
                                $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
                                $data = $this->getInputManager()->doFilter();
                                $this->setCallBackQuery($data);
                                $oView = new paymentDetailsView($this);
                                $paymentDetails['paymentDetails'][0]['payment']['dueDateDisplay'] = $paymentDetails['paymentDetails'][0]['payment']['dueDate'];
                                $paymentDetails['paymentDetails'][0]['payment']['dueDate'] = substr($paymentDetails['paymentDetails'][0]['payment']['dueDate'],6,10).'-'.substr($paymentDetails['paymentDetails'][0]['payment']['dueDate'],0,2).'-'.substr($paymentDetails['paymentDetails'][0]['payment']['dueDate'],3,2);
                                $oView->financeView($paymentDetails['paymentDetails'][0],$editParam);
                                break;
                case self::ACTION_DO_FINANCE_EDIT :
                                $paymentID = $_POST['paymentID'];
                                unset($_POST['paymentID']);
                                $_POST['ID'] = $paymentID;
                                if(!isset($_POST['cancelEdit']) && $_POST['cancelEdit'] != 'cancel'){
                                    $sessionUserID  = $this->getRequest()->getSession()->getUser()->getID();
                                    $returnVal = $this->getModel()->doEditPayment($_POST,$sessionUserID);
                                }
                                if(isset($returnVal['budgetExceeded']) && $returnVal['budgetExceeded'] == 1){
                                    $this->redirect('/admin/paymentDetails/edit/'.$_POST['ID'].'?budgetExceeded=1');
                                }else{
                                    $this->redirect('/admin/paymentDetails/edit/'.$_POST['ID']);
                                }
                                break;
                case self::ACTION_EXPORT:
                                $this->exportPaymentList();
                                break;
                case self::ACTION_IMPORT:
                                $this->importPaymentCSV();
                                break;
                case self::ACTION_CONFIRM:
                                $this->confirmCSVPayment();
                                break;               
                case self::ACTION_EDIT:
                                $paymentID = $this->getActionFromRequest(false, 1);
                                $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
                                $paymentDetails = $this->getModel()->getPaymentDetails($paymentID,$sessionUserID);
                                $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
                                $data = $this->getInputManager()->doFilter();
                                if(isset($data['PageType']) && $data['PageType'] != ''){
                                    $this->setPageType($data['PageType']);
                                }
                                $dataToSearchQuery = $data;
                                unset($data['budgetExceeded']);
                                $this->setCallBackQuery($data);
                                $oView = new paymentDetailsView($this);
                                $oView->editPayment($paymentDetails['paymentDetails'][0],$dataToSearchQuery);
                                break;
                case self::ACTION_DO_SAVE_EDIT:
                                $paymentID = $this->getActionFromRequest(false, 1);
                                $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
                                $data = $this->getInputManager()->doFilter();
                                $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
                                if (count($_FILES) > 0 ){
                                    $filename = basename($_FILES["fileupload"]["tmp_name"]);
                                    $path_parts = pathinfo($_FILES["fileupload"]["name"]);
                                    $ext = $path_parts['extension'];
                                    $nameWithoutSpace = str_replace(' ', '_', $path_parts['filename']);
                                    
                                    $destFolder = mofilmConstants::getInvoiceDocsFolder().$paymentID;
                                    if( is_dir( $destFolder ) ) {
                                        $destinationPath = $destFolder .'/'.$nameWithoutSpace . "." . $ext;
                                    } else{
                                        mkdir($destFolder,0777,true); 
                                        $destinationPath = $destFolder.'/' .$nameWithoutSpace . "." . $ext;
                                    }
                                    chmod($destinationPath, 0777);
                                    if(move_uploaded_file($_FILES["fileupload"]["tmp_name"], $destinationPath)){
                                        $data['FileName']     = $nameWithoutSpace . "." . $ext;     
                                        $data['FileLocation'] = $destinationPath;   
                                    }
                                }                                       
                                $data['ID'] = $paymentID;
                                $saveEdit = $this->getModel()->doEditPayment($data,$sessionUserID);
                                if($saveEdit['message'] == 'Saved'){
                                    $this->redirect('/admin/paymentDetails/edit/'.$paymentID);
                                }
                                break;
                case self::ACTION_EDIT_ADHOC:
                                $paymentID = $this->getActionFromRequest(false, 1);
                                $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
                                $paymentDetails = $this->getModel()->getMultiPaymentDetails($paymentID,$sessionUserID);
                                $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
                                $data = $this->getInputManager()->doFilter();
                                $oView = new paymentDetailsView($this);
                                $oView->editAdhocPayment($paymentDetails,$paymentID,$data);
                                break;
                case self::ACTION_DO_EDIT_ADHOC:
                                if($this->doAddPayment('editAdhocPayment')){
                                    $this->redirect('/admin/paymentDetails');
                                }
                                break;
                case self::ACTION_VIEW_COMPLIANCE :
                                $this->paymentList();
                                break;
                case self::ACTION_VIEW_ACCOUNT_MANAGER:
                                $this->paymentList();
                                break;
                case self::ACTION_VIEW_FINANCE:
                                $this->financeList();
                                break;
                case self::ACTION_NEW_PAYMENTS:
                                $this->financeList();
                                break;
                case self::ACTION_DUE_PAYMENTS:
                                $this->financeList();
                                break;
                default:
                                $this->paymentList();
                                break;			                              
            }
        }
        
        function confirmCSVPayment()
        {
            $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
            $message = $this->getModel()->addConfirmPayment($_POST,$sessionUserID);
            $this->redirect('/admin/paymentDetails/viewFinance/updatedAdvice/'.$message['message']);

        }
        
        function importPaymentCSV()
        {
            $oView = new paymentDetailsView($this);
            $oView->importPaymentList();
        }
        
        
        function paymentList(){
            
            $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
            $data = $this->getInputManager()->doFilter();
            $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
            
            if(isset($data['BrandID'])){
                $brandParamArray = explode('-',$data['BrandID']);
                $data['BrandID']= $brandParamArray[0];
            }
            
            if (!strstr($data['FromDate'], '-') && $data['FromDate']) {
                $data['FromDate'] = '';
            }
            
            if (!strstr($data['ToDate'], '-') && $data['ToDate']) {
                $data['ToDate'] = '';
            }
            $data['RowsLimit'] = 30;
            
            if(!isset($data['PageNo'])){
                 $data['PageNo']= 1;
            }
            
            if(isset($data['FilmMaker'])  && $data['FilmMaker'] != ''){
                if(is_numeric($data['FilmMaker'])){
                    $oUser = mofilmUserManager::getInstanceByID($data['FilmMaker']);
                    $data['FilmMakerID'] = $data['FilmMaker'];
                }else{
                    $oUser = mofilmUserManager::getInstanceByID($data['FilmMakerID']);
                }              
                if ( isset($oUser) && $oUser instanceof mofilmUser && $oUser->getID() > 0 ) {
			$data['FilmMaker'] = $oUser->getFirstname().' '.$oUser->getSurname();
		}    
            }  
          
            if($this->getRequest()->getSession()->getUser()->getPermissions()->isRoot()){
                if($this->getAction() == 'viewCompliance'){
                    if($data['Status'] == ''){  
                        $data['Status'] = 'Pending Approval';
                    }
                    $data['OrderByParam']   = 'payment.dueDate';
                    $data['OrderBy']   = 'ASC';
                }
            } else{
                if($this->hasAuthority('paymentDetailsController.viewCompliance')){
                    if($data['Status'] == ''){  
                        $data['Status'] = 'Pending Approval';
                    }
                    $data['OrderByParam']   = 'payment.dueDate';
                    $data['OrderBy']   = 'ASC';
                }
            }
            $paymentData = $this->getModel()->getPaymentList($data,$sessionUserID);
            $this->setCallBackQuery($data);
            unset($data['PageNo']);
            unset($data['RowsLimit']);
            $this->setSearchQuery($data);
            $oView = new paymentDetailsView($this);
            $oView->showPaymentListPage($paymentData,$data);
        }
        
        function exportPaymentList()
        {
            $paymentData = $this->generatExportList();
            $fileName = "ExportPaymentList".time();
            $filename = "/var/www/html/trunk/websites/base/resources/exportPayments/".$fileName;
            $file = fopen($filename,"w");
            
            $headerInfo = array("Payment ID","Hash ID","Filmmaker ID","Filmmaker Name", "Amount(USD)","Date Due","Paid Date", "Status");
            fputcsv($file,$headerInfo);
            foreach ($paymentData as $line) {
                unset($due_dt); unset($paid_amt); unset($ptdate);
            $due_dt =  explode(" ",$line['payment']['dueDate']);
            $ptdate = date("m/d/Y", strtotime($due_dt[0]));
            $paid_amt = explode(" ",str_replace(",","",$line['payment']['payableAmount']));
            $lineArr = array($line['payment']['ID'],"'".$line['payment']['hashKey'],$line['users']['ID'], $line['users']['firstname']." ".$line['users']['surname'] ,$paid_amt[0],$ptdate, date("m/d/Y"), '' );
                fputcsv($file,$lineArr);
                }
            
            fclose($file);
            //header("Content-Type: text/csv");
            header("Content-type: application/vnd.ms-excel;charset=utf-8");
            header("Content-Disposition: attachment;Filename=$fileName.csv");
            
            
            readfile($filename);
            unlink($filename);
            
        }
        
        function generatExportList(){
            $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
            $data = $this->getInputManager()->doFilter();
            $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
          
            if(isset($data['BrandID'])){
                $brandParamArray = explode('-',$data['BrandID']);
                $data['BrandID']= $brandParamArray[0];
            }
            
            if (!strstr($data['FromDate'], '-') && $data['FromDate']) {
                $data['FromDate'] = '';
            }
            
            if (!strstr($data['ToDate'], '-') && $data['ToDate']) {
                $data['ToDate'] = '';
            }
            
            
            /*if(isset($data['FilmMaker'])  && $data['FilmMaker'] != ''){
                $oUser = mofilmUserManager::getInstanceByID($data['FilmMaker']);
                $data['FilmMakerID'] = $data['FilmMaker'];
                if ( isset($oUser) && $oUser instanceof mofilmUser && $oUser->getID() > 0 ) {
			$data['FilmMaker'] = $oUser->getFirstname().' '.$oUser->getSurname();
		}    
            }  */
            $data['OrderByParam']   = 'payment.dueDate';
            $data['OrderBy']   = 'DESC';
             $data['Status']     = 'Approved';
                $data['searchType'] = 'paymentDue';
                $data['PageType'] = 'duePayments';
             
            $paymentData = $this->getModel()->getPaymentList($data,$sessionUserID);
            
            return $paymentData['paymentList'];
            
        }
        
        function financeList(){
            $confirmPayment = $this->getActionFromRequest(false, 1);
            $updatedCount = $this->getActionFromRequest(false, 2);
            $csvUpdatedMsg = '';
            if(isset($confirmPayment) && $confirmPayment == 'updatedAdvice' && $updatedCount !== ''){
                if($updatedCount == 1){
                    $csvUpdatedMsg = '1 Payment records updated.';
                }else{
                    $csvUpdatedMsg = $updatedCount.' Payment records updated.';
                }
                
            }
            $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
            $data = $this->getInputManager()->doFilter();
            $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
          
            if(isset($data['BrandID'])){
                $brandParamArray = explode('-',$data['BrandID']);
                $data['BrandID']= $brandParamArray[0];
            }
            
            if (!strstr($data['FromDate'], '-') && $data['FromDate']) {
                $data['FromDate'] = '';
            }
            
            if (!strstr($data['ToDate'], '-') && $data['ToDate']) {
                $data['ToDate'] = '';
            }
            $data['RowsLimit'] = 30;
            
            if(!isset($data['PageNo'])){
                 $data['PageNo']= 1;
            }
            
            if(isset($data['FilmMaker'])  && $data['FilmMaker'] != ''){
                if(is_numeric($data['FilmMaker'])){
                    $oUser = mofilmUserManager::getInstanceByID($data['FilmMaker']);
                    $data['FilmMakerID'] = $data['FilmMaker'];
                }else{
                    $oUser = mofilmUserManager::getInstanceByID($data['FilmMakerID']);
                }
                if ( isset($oUser) && $oUser instanceof mofilmUser && $oUser->getID() > 0 ) {
			$data['FilmMaker'] = $oUser->getFirstname().' '.$oUser->getSurname();
		}    
            } 
            $data['OrderByParam']   = 'payment.dueDate';
            $data['OrderBy']   = 'DESC';
            if($this->getAction() == 'viewFinance' || $this->getAction() == 'duePayments'){
                $data['Status']     = 'Approved';
                $data['searchType'] = 'paymentDue';
                $data['PageType'] = 'duePayments';
                $paymentData = $this->getModel()->getPaymentList($data,$sessionUserID);
            }else if($this->getAction() == 'newPayments' ){
                $data['Status']     = 'Draft';
                $data['PageType'] = 'newPayments';
                $paymentData = $this->getModel()->getPaymentList($data,$sessionUserID);
            }
            if(!isset($data['DateFilter'])  && $data['DateFilter'] == ''){
                $data['DateFilter'] = 'dueDate';
            }
            $this->setCallBackQuery($data);
            unset($data['PageNo']);
            unset($data['RowsLimit']);
            $this->setSearchQuery($data);
            $oView = new paymentDetailsView($this);
            $oView->showFinanceListPage($paymentData,$data,$csvUpdatedMsg);
        }
        
        function doAddPayment($paymentUrl){
            $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
            if($this->getModel()->addPayment($_POST,$sessionUserID,$paymentUrl)){
                return true;
            }else{
                return false;
            }
        }

	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('BrandID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MovieID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('GrantID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('PaymentType', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SubmitterID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SubmitterComments', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ApproverID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ApproverComments', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('PayableAmount', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('PaidAmount', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('FromDate', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('ToDate', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('DueDate', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('PaidDate', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('PaymentDesc', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('AccountUser', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('AccountComments', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('BankReference', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('HasMultipart', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ParentID', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('PageNo', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('FilmMaker', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('FilmMakerID', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('DateFilter', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('PageType', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('FileName', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('FileLocation', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('budgetExceeded', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param paymentDetailsModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['ID']);
		$inModel->setEventID($inData['EventID']);
		$inModel->setSourceID($inData['SourceID']);
		$inModel->setUserID($inData['UserID']);
		$inModel->setMovieID($inData['MovieID']);
		$inModel->setGrantID($inData['GrantID']);
		$inModel->setPaymentType($inData['PaymentType']);
		$inModel->setSubmitterID($inData['SubmitterID']);
		$inModel->setSubmitterComments($inData['SubmitterComments']);
		$inModel->setApproverID($inData['ApproverID']);
		$inModel->setApproverComments($inData['ApproverComments']);
		$inModel->setPayableAmount($inData['PayableAmount']);
		$inModel->setPaidAmount($inData['PaidAmount']);
		$inModel->setStatus($inData['Status']);
		$inModel->setCreated($inData['Created']);
		$inModel->setDueDate($inData['DueDate']);
		$inModel->setPaidDate($inData['PaidDate']);
		$inModel->setPaymentDesc($inData['PaymentDesc']);
		$inModel->setAccountUser($inData['AccountUser']);
		$inModel->setAccountComments($inData['AccountComments']);
		$inModel->setBankReference($inData['BankReference']);
		$inModel->setHasMultipart($inData['HasMultipart']);
		$inModel->setParentID($inData['ParentID']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new paymentDetailsModel();
		$this->setModel($oModel);
	}
        
        function getSearchQuery() {
		return $this->_SearchQuery;
	}
        
        function getSearchQueryAsString() {
		return http_build_query($this->getSearchQuery());
	}
        
        function setSearchQuery($inSearchQuery) {
		if ( $inSearchQuery !== $this->_SearchQuery ) {
			$this->_SearchQuery = $inSearchQuery;
			$this->setModified();
		}
		return $this;
	}
        
        function getCallBackQuery() {
		return http_build_query($this->_CallBackQuery);
	}
        
        function setCallBackQuery($inSearchQuery) {
		if ( $inSearchQuery !== $this->_CallBackQuery ) {
			$this->_CallBackQuery = $inSearchQuery;
			$this->setModified();
		}
		return $this;
	}
        
        function getPageType() {
		return $this->_PageType;
	}
        
        function setPageType($pageType) {
		if ( $pageType !== $this->_PageType ) {
			$this->_PageType = $pageType;
			$this->setModified();
		}
		return $this;
	}
        
}
