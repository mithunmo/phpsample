<?php
/**
 * grantsModel.class.php
 * 
 * grantsModel class
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category grantsModel
 * @version $Rev: 623 $
 */


/**
 * grantsModel class
 * 
 * Provides the "grants" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category grantsModel
 */
class grantsModel extends mofilmUserMovieGrants {
	
    	/**
	 * Stores an instance of mofilmUserMovieGrantsSearch
	 * 
	 * @var mofilmUserMoviegrantsSearch
	 * @access protected
	 */
	protected $_GrantsSearch;
	
    	/**
	 * Stores an instance of mofilmUserMovieGrantsSearchResult
	 * 
	 * @var mofilmUserMoviegrantsSearchResult
	 * @access protected
	 */
	protected $_GrantsSearchResult;

    	/**
	 * Stores an integer of total amount disbursed
	 * 
	 * @access protected
	 */
	protected $_GrantsDisbursed;
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return mofilmUserMovieGrants::listOfObjects(NULL, $inOffset, $inLimit);
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count from mofilm_content.userMovieGrants 
                    inner join mofilm_content.users on users.ID = userMovieGrants.userID
                    inner join mofilm_content.grants on grants.ID = userMovieGrants.grantID
                and userMovieGrants.private = 0';
                
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getTotalObjects();
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}


	/**
	 * Returns a new blank object
	 *
	 * @return systemDaoInterface
	 */
	function getNewObject() {
		return new mofilmUserMovieGrants();
	}
	
	/**
	 * Creates an returns a mofilmUserMovieGrantsSearch object
	 * 
	 * @return mofilmUserMovieGrantsSearch
	 */
	function getGrantsSearch() {
		if ( !$this->_GrantsSearch instanceof mofilmUserMovieGrantsSearch ) {
			$this->_GrantsSearch = new mofilmUserMovieGrantsSearch();
		}
		return $this->_GrantsSearch;
	}
	
	/**
	 * Returns the search result object, or null if no search has been run
	 * 
	 * @return mofilmUserMovieGrantsSearchResult
	 */
	function getSearchResult() {
		return $this->_GrantsSearchResult;
	}
	
	/**
	 * Runs the search with provided parameters
	 * 
	 * @return mofilmUserMovieGrantsSearchResult
	 */
	function doSearch() {
		$this->_GrantsSearchResult = $this->getGrantsSearch()->search();
		return $this->_GrantsSearchResult;
	}

	/**
	 * Returns total grants disbursed
	 * 
	 * @return float
	 */
	function getTotalGrantsDisbursed() {
		$this->_GrantsDisbursed = $this->getGrantsSearch()->totalGrantsDisbursed();
		return $this->_GrantsDisbursed;
	}
        
        /*
         * 
         */
        function getGrantAssets($grantID) {
		$query = '
			SELECT paramValue
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrantsData
                        WHERE userMovieGrantsData.paramName = "GrantAssetsPath" AND userMovieGrantsData.userMovieGrantsID = '.$grantID;
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['paramValue'];
		} else {
			return 0;
		}
	}

                
        function getProductID($grantID)
        {
           $query = ' SELECT d.productID FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants AS a, 
                                              '.system::getConfig()->getDatabase('mofilm_content').'.grants AS b, 
                                              '.system::getConfig()->getDatabase('mofilm_content').'.sources AS c,
                                              '.system::getConfig()->getDatabase('mofilm_content').'.events AS d'
                   . '                          WHERE a.ID = '.$grantID.' AND a.grantID = b.ID AND b.sourceID = c.ID AND c.eventID = d.ID';
            $oRes = dbManager::getInstance()->query($query);
            $res = $oRes->fetch();
            if ( is_array($res) && count($res) > 0 ) {
			return $res['productID'];
		} else {
			return 0;
		}
        }
	/**
	 * 
	 */
	function approvalProcess($inData, $inUserID) {
		try {
			$oGrant = mofilmUserMovieGrants::getInstance($inData['GrantID']);
			
			if ( $inData['GrantedStatus'] == 'Approved' ) {
				$inGrantStatus = $oGrant->getStatus();
				$oGrant->setGrantedAmount(floatval($inData['GrantedAmount']));
			} else {
				$oGrant->setGrantedAmount(floatval(0));
			}
			
			$oGrant->setModeratorComments(nl2br($inData['ModeratorComments']));
			$oGrant->setStatus($inData['GrantedStatus']);
			$oGrant->setModeratorID($inUserID);
			$oGrant->setMovieID($inData['MovieID']);
			$oGrant->setModerated(new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue()));
			$oGrant->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_AGREEMENT, $inData['DocumentAgreement']);
			$oGrant->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_BANK_DETAILS, $inData['DocumentBankDetails']);
			$oGrant->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_IDPROOF, $inData['DocumentIdProof']);
			$oGrant->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_RECEIPTS, $inData['DocumentReceipts']);
			$oGrant->save();
			
                        if($inGrantStatus != $inData['GrantedStatus']) {
                            if ($inData['GrantedStatus'] == 'Approved' ) {
                                $approvalFile = $this->sendApprovalEmail($inData['GrantID']);
                                $grantAction = 'Approved';
                                
                            } elseif( $inData['GrantedStatus'] == 'Rejected' ) {
                                //$this->sendGrantsRejectedEmail($oGrant->getUserID(), $oGrant->getGrants()->getSourceID());
                                $grantAction = 'Rejected';   
                            }
                            
                                 
                            $params = array(
                                            'http' => array(
                                                            'method' => 'POST',
                                                            'header'  => 'Content-type: application/x-www-form-urlencoded',
                                                            'content' => 'userID='.$oGrant->getUserID().'&grantID='. $inData['GrantID'].'&grantAction='.$grantAction.'&approvalFile='.$approvalFile
                                                            )
                                            );

                            $context  = stream_context_create($params);
                            $emailUrl = system::getConfig()->getParam('mofilm', 'emailMofilmUri')->getParamValue().'/grant/?';
                            $result   = file_get_contents($emailUrl, false, $context);
                        }
                   

			return true;
		} catch (Exception $error) {
			return $error;
		}
	}
	
		/**
	 * Generates PDF with specified options
	 * 
	 * @param array $options
	 * @return filename
	 */
	function generatePDF($inGrantID, $dirPath) {
		try{   
			$oUserMovieGrants = mofilmUserMovieGrants::getInstance($inGrantID);
                        $ProductID  = $this->getProductID($inGrantID);
                        $eventName = $oUserMovieGrants->getGrants()->getSource()->getEvent()->getName();
			$sourceName = $oUserMovieGrants->getGrants()->getSource()->getName();
                        
                        $sourceID = $oUserMovieGrants->getGrants()->getSourceID();
                        
                        $GID = $oUserMovieGrants->getGrantID();
                        $oGrantData = new grantdata();
                        $grantUpdate = $oGrantData->getValue($GID,"Question");
                        $FMfullname = str_replace(" ","_",$oUserMovieGrants->getUser()->getFullname());
                            
			$fileName = $eventName.'_'.$sourceName.'_'.$FMfullname.'_'.$inGrantID.'_'.time().'.pdf';
		
			$oGenPDF = new TCPDF('P', 'mm', 'A4', TRUE, 'UTF-8', FALSE);
			
			// setting doc information
			$oGenPDF->SetCreator('MOFILM Technologies');
			$oGenPDF->SetAuthor('Pavan Kumar P G');
			$oGenPDF->SetTitle('Grant Application form for '.$eventName.' - '.$sourceName);
			$oGenPDF->SetSubject('Grant Application Form'); 
			$oGenPDF->SetKeywords('MOFILM, Grants Application');

			// setting header and footer
			$oGenPDF->setHeaderData('mofilm_logo.jpg', 180);
			$oGenPDF->setFooterData($tc=array(0,64,0), $lc=array(0,64,128));
			$oGenPDF->SetDefaultMonospacedFont('pdfatimes');
			$oGenPDF->SetMargins(20, 20, 20);
			$oGenPDF->setHeaderMargin(3);
			$oGenPDF->setFooterMargin(10);
			$oGenPDF->SetAutoPageBreak(TRUE, 10);
			$oGenPDF->AddPage();
			
			if ( (preg_match("/\p{Han}+/u", $oUserMovieGrants->getUser()->getFullname())) || (preg_match("/\p{Han}+/u", $oUserMovieGrants->getFilmTitle()))  || (preg_match("/\p{Han}+/u", $oUserMovieGrants->getFilmConcept()))  || (preg_match("/\p{Han}+/u", $oUserMovieGrants->getUsageOfGrants()))  || (preg_match("/\p{Han}+/u", $oUserMovieGrants->getScript())) ) {
				$oGenPDF->SetFont('kozminproregular');
			} else {
				$oGenPDF->SetFont('pdfatimes');
			}
			
			$html = '<div style="padding-left:200px">';
			$html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">For the Event</span>
				  <span style="font-size:25px; color:#5a5857">'.$eventName.'</span> <br /><br />';
			$html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Submitted By</span><br />';
			$html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getUser()->getFullname().'</span> <br /><br />';
			if($ProductID == "12")
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Title of Show</span><br />';
                        elseif($ProductID == "3")
                        {
                            if($sourceID == "941")
                                $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">What is your interpretation of the problem we need to solve?</span><br />';
                            else    
                                $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">What is the main problem we need to solve?</span><br />';
                        }
                        elseif($ProductID == "5"){
                            if(isset($grantUpdate))
                            {
                                $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;"> Pro Idea Submission </span><br />';
                            }
                            else
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Working Film Title</span><br />';
                        }    
                        else    
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Working Film Title</span><br />';
                        if($ProductID == "3")
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('MomindsTitle').'</span><br /><br />';
                        else
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getFilmTitle().'</span><br /><br />';
                        if($ProductID == "12")
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Logline</span><br />';
                        elseif($ProductID == "3")
                        {
                            if($sourceID == "941")
                                $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">What is the creative idea?</span><br />';
                            else    
                                $html .= '<span style="fx   ont-size:30px; font-weight:bold; color:#077ac3;">What is your creative platform?</span><br />';
                        }    
			elseif($ProductID == "5"){
                            if(isset($grantUpdate))
                            {
                                $QuestionVal = $grantUpdate->getParamValue();
                                $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">'.$QuestionVal.'</span><br />';
                            }
                            else
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Film Concept</span><br />';
                        }    
                        else    
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Film Concept</span><br />';
			$html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getFilmConcept().'</span><br /><br />';
                        if($ProductID == "3")
                        {
                            if(($sourceID == "941")||($sourceID == "990")||($sourceID == "989"))
                                $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">What is your insight?</span><br />';
                            else    
                                $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">What is the insight?</span><br />';
                             $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('Insight').'</span><br /><br />';
                        }    
                        if($ProductID == "12")
                        {    
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Amount Requested</span><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getRequestedAmount().'</span><br /><br />';
                        }
                        if($oUserMovieGrants->getUsageOfGrants())
                        {
			$html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Usage of Grants</span><br />';
			$html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getUsageOfGrants().'</span><br /><br />';
                        }
			if($ProductID == "12")
                        {
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Show Format</span><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('ShowFormat').'</span><br /><br />';
			    $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Episodes</span><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857"> 1.'.$oUserMovieGrants->getParamSet()->getParam('Episode1').'</span><br /><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857"> 2.'.$oUserMovieGrants->getParamSet()->getParam('Episode2').'</span><br /><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857"> 3.'.$oUserMovieGrants->getParamSet()->getParam('Episode3').'</span><br /><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857"> 4.'.$oUserMovieGrants->getParamSet()->getParam('Episode4').'</span><br /><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857"> 5.'.$oUserMovieGrants->getParamSet()->getParam('Episode5').'</span><br /><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857"> 6.'.$oUserMovieGrants->getParamSet()->getParam('Episode6').'</span><br /><br />';
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Synopsis</span><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('synopsis').'</span><br /><br />';
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Showreel URL</span><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857"> <a href="'.$oUserMovieGrants->getParamSet()->getParam('ShowReelURL').'" target="_blank">'
								.$oUserMovieGrants->getParamSet()->getParam('ShowReelURL').' </a>'.'</span><br /><br />';
                            
                        }
                        if ($oUserMovieGrants->getScript()) {
                            if($ProductID == "12")
                               $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Treatment</span><br />';
                            elseif($ProductID == "3")
                            {
                                if($sourceID == "941")
                                    $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Please complete this sentence: This creative idea will inspire social content about …</span><br />';
                                else if(($sourceID == "959") || ($sourceID == "963"))
                                    $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">How might you execute this creative platform for TV?</span><br />';
                                else if($sourceID == "989"){
                                    $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">How does it work?</span><br />';
				}
                                else
                                    $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">How would you execute this creative platform for video content?</span><br />';
                            }    
                            else    
				$html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Script</span><br />';
				$html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getScript().'</span><br /><br />';
			}
                        if($ProductID == "3"){
                        if($sourceID == "990"){
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Please complete this sentence:</span><br />';
                                    $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">This creative platform will inspire films for Mother’s Day about... </span><br />';
				$html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('question1').'</span><br /><br />';
                                $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">This creative platform will inspire films for Holidays about... </span><br />';
				$html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('question2').'</span><br /><br />';
                                }
                        }
                        if($sourceID == "989"){
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Please complete this sentence:</span><br />';
                                    $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">This creative solution will inspire films about… </span><br />';
				$html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('question1').'</span><br /><br />';
                                 
                        }
                        if($ProductID == "3")
                        {
                            if($sourceID == "941") ;
                            else if(($sourceID == "959")||($sourceID == "963"))
                            {
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Please describe 3 additional non-film executions that could live within this creative platform.</span><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('ReferenceFilm1').'</span><br /><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('ReferenceFilm2').'</span><br /><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('ReferenceFilm3').'</span><br /><br />';
                            }  
                            else if($sourceID == "990"){
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Please provide at least 2 additional executions (using Mother’s Day, Holiday or neither) that could be derived from the creative platform for Social and Point of Sale. </span><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('ReferenceFilm1').'</span><br /><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('ReferenceFilm2').'</span><br /><br />';
                            }
                            else if($sourceID == "989"){
                                
                            }
                            else{
                            $html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Please describe 3 additional film or non-film executions that could be a part of the creative platform</span><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('ReferenceFilm1').'</span><br /><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('ReferenceFilm2').'</span><br /><br />';
                            $html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getParamSet()->getParam('ReferenceFilm3').'</span><br /><br />';
                            }
                            
                        }
			if ($oUserMovieGrants->getModeratorComments()) {
				$html .= '<span style="font-size:30px; font-weight:bold; color:#077ac3;">Moderator Comments</span><br />';
				$html .= '<span style="font-size:25px; color:#5a5857">'.$oUserMovieGrants->getModeratorComments().'</span><br /><br />';
			}
			$html .= '</div>';
			
			$oGenPDF->writeHTMLCell($w=180, $h=0, $x='', $y='', nl2br($html), $border=0, $ln=1, $fill=0, $reseth=true, $align='', FALSE);
			$oGenPDF->Output($dirPath.$fileName, 'F');
			return $fileName;
                        
		} catch (Exception $error) {
			systemLog::message($error);
		}
		return FALSE;
	}
	function GrantRejectionEmail($inGrantID)
        {
            try 
            { 
                $oUserMovieGrants = mofilmUserMovieGrants::getInstance($inGrantID);
                if ( $oUserMovieGrants->getStatus() == 'Pending' || $oUserMovieGrants->getStatus() == 'Approved')
                {   
                    $result = $this->editGrantsStatus($inGrantID);
                    if($result){
                        $grantAction = 'Rejected';  
                        $params = array(
                                        'http' => array(
                                                        'method' => 'POST',
                                                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                                                        'content' => 'userID='.$oUserMovieGrants->getUserID().'&grantID='. $inGrantID.'&grantAction='.$grantAction.'&approvalFile='.$approvalFile
                                                        )
                                        );

                        $context  = stream_context_create($params);
                        $emailUrl = system::getConfig()->getParam('mofilm', 'emailMofilmUri')->getParamValue().'/grant/?';
                        $result   = file_get_contents($emailUrl, false, $context);
                       // $this->sendGrantsRejectedEmail($oUserMovieGrants->getUserID(), $oUserMovieGrants->getGrants()->getSourceID());
                    }
                }
                
            } 
            catch (Exception $error) 
            {
                return $error;
            }
            		
        }
	/**
	 * Generates PDF Agreement form and sends to user along with bank details form.
	 * 
	 * @param array $oGrantIds
	 */
	function sendApprovalEmail($inGrantID) {
                    $oUserMovieGrants = mofilmUserMovieGrants::getInstance($inGrantID);
                    try{
                            if ( $oUserMovieGrants->getStatus() == 'Approved' && $oUserMovieGrants->getGrantedAmount() > 0 ) {
				$eventName = $oUserMovieGrants->getGrants()->getSource()->getEvent()->getName();
				$sourceName = $oUserMovieGrants->getGrants()->getSource()->getName();
				$grantedAmount = $oUserMovieGrants->getGrantedAmount();
				 $dirPath = mofilmConstants::getTcpdfFolder();

				$oGenPDF = new TCPDF('P', 'mm', 'A4', TRUE, 'UTF-8', FALSE);

				// setting doc information
				$oGenPDF->SetCreator('MOFILM Technologies');
				$oGenPDF->SetAuthor('Pavan Kumar P G');
				$oGenPDF->SetTitle('Grant Application Acceptance Agreement for '.$eventName.' - '.$sourceName);
				$oGenPDF->SetSubject('Grant Application Acceptance Agreement'); 
				$oGenPDF->SetKeywords('MOFILM, Grant Application Acceptance Agreement');

				// setting header and footer
				$oGenPDF->setPrintHeader(false);
				$oGenPDF->setPrintFooter(false);

				// set header and footer fonts
				$oGenPDF->setHeaderFont(Array('', '', 14));

				$oGenPDF->SetDefaultMonospacedFont('pdfatimes');
				$oGenPDF->SetMargins(15, -9, 0);
				$oGenPDF->AddPage();

				$oGenPDF->SetFont('pdfatimes');
				$oGenPDF->SetFontSize(11);

				$html = '<div style="width:800px; margin:0 auto; text-align:justify">
						<div style="text-align:center; text-decoration:underline;"><h2>MOFILM Production Grant Approval Form</h2></div>
						MOFILMs Production Grants are available to Filmmakers working on entries only for the <b>'.$sourceName.' '.$eventName.'</b>.  The Production Grants are available in amounts ranging from US$500 up to US$3000 to help Filmmakers create films with higher production values and overcome speculative funding issues. The funds can be used to help pay for additional equipment, vehicle rental, editing software, props hire or any of the other significant expenses that might stop someone from creating their very best work.  
						<br />Please note that all applications will be assessed and/or approved on the basis of the information provided in this form, at MOFILMs sole discretion. MOFILMs decisions regarding all applications will be final and no discussion or further correspondence will be entered into.
						<br />The agreed amount of each approved Production Grant will be paid to the Filmmaker by MOFILM no earlier than sixty (60) days after the relevant film is uploaded to <a href="http://www.mofilm.com">www.mofilm.com</a> PROVIDED that all of the requirements set out below have been met, verified and approved by MOFILM.  The requirements for payment of the award are: (1) timely delivery of a properly-executed Filmmaker Agreement; (2) timely delivery of all properly-executed Talent Release Forms (and, if applicable, Music Rights Clearance Forms; (3) full content clearance by MOFILM of the submitted video; and (4) delivery of valid receipts for all Grant-related goods and services (including in each case full vendor name and contact details); (5) the payment represents the transference of all rights to Mofilm, as set out in Clause 5.1 in the Filmmaker Agreement, which must accompany any video submission.


						<img src="/resources/pdf/pdf_box.png" height="130px" width="500px"/>

						Film Name (Working Title):
						Filmmaker Phone Number:
						Filmmaker Email Address:
						Filmmaker Address:

						Proposed use of Grant Funding:

						Amount Approved : $'.$grantedAmount.'
						MOFILM Signature: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <img src="/resources/pdf/jm_digitalsign_new.jpg" height="40px" />
						Print Full Name (BLOCK CAPITALS): &nbsp; &nbsp; &nbsp; JEFFREY MERRIHUE


						MOFILM Signature: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u>
						Print Full Name (BLOCK CAPITALS): &nbsp; &nbsp; &nbsp; '.$oUserMovieGrants->getUser()->getFullname().'

						<small><i><b>NOTE:</b>Requesting Filmmaker must attach to this Application a signed copy of Filmmakers passport or, driving licence or similar government-issued identification document</i></small>
						</div>';

				$oGenPDF->writeHTMLCell($w=180, $h=0, $x='', $y='', nl2br($html), $border=0, $ln=1, $fill=0, $reseth=true, $align='', FALSE);

				$file_name = 'Production Grant Approval Form '.$eventName.' '.$sourceName.' '.$oUserMovieGrants->getUser()->getFullname().'_'.time().'.pdf';
				$oGenPDF->Output($dirPath.$file_name, 'F');

				if (file_exists($dirPath.$file_name)) {
                                    chmod($dirPath.$file_name,0777);
                                    return $file_name;
                                        /*
					$accessKey = system::getConfig()->getParam('amazonses', 'accesskey')->getParamValue();
					$secretKey = system::getConfig()->getParam('amazonses', 'secretkey')->getParamValue();

					$regUri = system::getConfig()->getParam('mofilm', 'myMofilmUri')."/accounts/grants/edit/".$inGrantID;

					$oMessage = commsApplicationMessage::getInstance(36);
					$inSubject = $oMessage->getMessageHeader();
					$htmlBody = $oMessage->getMessageBody();
					$htmlBody = str_replace('%sourceName%', $sourceName, $htmlBody);
					$htmlBody = str_replace('%eventName%', $eventName, $htmlBody);
					$htmlBody = str_replace('%grantedAmount%', $grantedAmount, $htmlBody);
					$htmlBody = str_replace('%regUrl%', $regUri, $htmlBody);

					$oSesMessage = new mofilmGrantsAmazonSESMailer($accessKey, $secretKey);
					
					if ( system::getConfig()->isProduction() ) {
						$oSesMessage->AddAddress($oUserMovieGrants->getUser()->getEmail());
					} else {
						$oSesMessage->AddAddress('pavan.kumar@mofilm.com');
					}
					
					$oSesMessage->AddReplyTo('no-reply@mofilm.com', 'MOFILM');
					$oSesMessage->SetFrom('no-reply@mofilm.com', 'MOFILM');
					
					if ( system::getConfig()->isProduction() ) {
						$oSesMessage->Subject = $inSubject;
					} else {
						$oSesMessage->Subject = 'Non-Production - '.$inSubject;
					}

					$oSesMessage->MsgHTML($htmlBody);
					$oSesMessage->AddAttachment($dirPath.$file_name);
					$oSesMessage->AddAttachment(mofilmConstants::RESOURCES_FOLDER.DIRECTORY_SEPARATOR.'pdf/MofilmBankDetailsForm.pdf');
					$amazonSESResponse = $oSesMessage->Send();

					if ( $amazonSESResponse ) {
						systemLog::message('Message sent to Email : '.$oUserMovieGrants->getUser()->getEmail().' for Grant ID '.$inGrantID.' directly from AmazonSES.');
						return true;
					} else {
						systemLog::message('Error Sending Email to : '.$oUserMovieGrants->getUser()->getEmail().' for Grant ID '.$inGrantID.' directly from AmazonSES.');
					}
                                         
                                         */
				}
			}
		} catch (Exception $error) {
			systemLog::message($error);
		}
		return false;
	}
	
	/**
	 * 
	 * Sends and saves the message that has to be sent to the user through email from admin
	 * 
	 */
	function sendEmailCommunication($inData, $inUserID) {
		try {
			$oGrant = new mofilmUserMovieGrants($inData['GrantID']);
			
			$txt = 'On <strong>'.date('d M Y').'</strong> at <strong> '.date('H:i:s').' </strong>';
			$txt .= ' , <strong>'.mofilmUserManager::getInstanceByID($inUserID)->getPropername().' < '.mofilmUserManager::getInstanceByID($inUserID)->getEmail().' ></strong> wrote <br />';
			$txt .= '<strong>Message : </strong> <br />'.nl2br($inData['EmailMessage']).' <br /> <br />';
			
			$new_txt = $txt.$oGrant->getMessagesToFilmmaker();
			
			$oGrant->setMessagesToFilmmaker($new_txt);
			$oGrant->save();

			$this->sendGrantsPendingEmail($oGrant->getUserID(), $oGrant->getGrants()->getSourceID(), $inData['EmailMessage']);
			
			return true;
		} catch (Exception $error) {
			return $error;
		}
	}
	
	/**
	 * 
	 * Sends an email to the user with customised message from admin
	 * 
	 */
	function sendGrantsPendingEmail($inUserID, $inSourceID, $inComments) {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_USR_GRNT_PENDING
		);
		
		$oUser = mofilmUserManager::getInstanceByID($inUserID);
		$oObject = $this->getGrants()->getSource();
		
		commsOutboundManager::setCustomerInMessageStack($oQueue, $this->getUserID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getEmail());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%mofilm.username%', '%mofilm.brand%', '%mofilm.event%', '%mofilm.comments'), array($oUser->getFullname(), $oObject->getName(), $oObject->getEvent()->getName(), $inComments));

		return $oQueue->send();
	}
	
	/**
	 * 
	 * Sends an automated email to user after admin approves the grants application
	 * 
	 */
	function sendGrantsApprovalEmail($inUserID, $inSourceID, $inGrantedAmount) {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_USR_GRNT_APPROVED
		);

		$oUser = mofilmUserManager::getInstanceByID($inUserID);
		$oObject = mofilmSource::getInstance($inSourceID);

		commsOutboundManager::setCustomerInMessageStack($oQueue, $this->getUserID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getEmail());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%mofilm.username%', '%mofilm.brand%', '%mofilm.event%', '%mofilm.grantedAmount%'), array($oUser->getFullname(), $oObject->getName(), $oObject->getEvent()->getName(), $inGrantedAmount));

		return $oQueue->send();
	    
	}
	
	/**
	 * 
	 * Sends an automated email to user after admin rejects the grants application
	 * 
	 */
	function sendGrantsRejectedEmail($inUserID, $inSourceID) {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_USR_GRNT_REJECTED
		);
		
		$oUser = mofilmUserManager::getInstanceByID($inUserID);
		$oObject = mofilmSource::getInstance($inSourceID);
		
		commsOutboundManager::setCustomerInMessageStack($oQueue, $this->getUserID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getEmail());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%mofilm.username%', '%mofilm.brand%', '%mofilm.event%'), array($oUser->getFullname(), $oObject->getName(), $oObject->getEvent()->getName()));

		return $oQueue->send();
	}
        
        
        function getGrantUser($grantID) {
                $query = '
			SELECT userID
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants
                        WHERE userMovieGrants.id = '.$grantID;
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['userID'];
		} else {
			return 0;
		}
	}
        
	function rateGrant($currentUser, array $inData = array()) {
		if ( !isset($inData['Rating']) || !is_numeric($inData['Rating']) || $inData['Rating'] == 0 ) {
			throw new mvcModelException('Missing a rating value, or is not numeric');
		}                
		$oRating = mofilmUserGrantsRating::getInstanceByGrantID($inData["GrantID"], $currentUser);                             
		$oRating->setGrantID($inData["GrantID"]);
		$oRating->setRating($inData['Rating']);
		$oRating->setUserID($currentUser);
		$oRating->save();
		
                $oGrant = mofilmUserMovieGrants::getInstance($inData["GrantID"]);
                $avgRating = mofilmUserGrantsRating::averageGrantRating($inData["GrantID"]);
                $oGrant->setRating($avgRating[0]);
                $oGrant->save();
		return true;
	}
        
        function getAvgRating($inGrantID){
            $avgRating = mofilmUserGrantsRating::averageGrantRating($inGrantID);
            if ( $avgRating  != "" ){
                return ceil($avgRating[0]);
            } else {
                return "Unrated";
            }
            
        }

}
