<?php
/**
 * paymentDetailsModel.class.php
 * 
 * paymentDetailsModel class
 *
 * @author Poulami Chakraborty
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category paymentDetailsModel
 * @version $Rev: 624 $
 */


/**
 * paymentDetailsModel class
 * 
 * Provides the "paymentDetails" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category paymentDetailsModel
 */
class paymentDetailsModel extends mofilmPaymentDetails implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
            return mofilmPaymentDetails::listOfObjects(NULL, $inOffset, $inLimit);
	}
        
        /*
         * Get payment Dashboard list for 'All Payments', 'Compliance', 'Finance'
         */
        function getPaymentList($paymentArray,$sessionUserID) {
            
            list($secretToken,$publicKey) = $this->getSecretToken();
            $currentTimestamp = time();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, system::getConfig()->getParam('mofilm', 'apiWebServiceUri')->getParamValue().'/v1/search/?'.http_build_query($paymentArray)); 
            curl_setopt($ch,CURLOPT_HTTPHEADER,array( 'Accept: application/json','PAYMENT-API: Token="'.$secretToken.'",timestamp="'.$currentTimestamp.'",publickey="'.$publicKey.'",userID="'.$sessionUserID.'"'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            $jsonString = curl_exec($ch); 
            curl_close($ch);
            $data       = json_decode($jsonString, true);
            return $data;
         
        }
        
        /*
         * Save Adhoc fee payments
         */
        function addPayment($paymentArray,$sessionUserID,$paymentUrl) {
            list($secretToken,$publicKey) = $this->getSecretToken();
            $ch = curl_init();
            unset($paymentArray['Contributors']);
            $currentTimestamp = time();
            curl_setopt($ch, CURLOPT_URL, system::getConfig()->getParam('mofilm', 'apiWebServiceUri')->getParamValue().'/v1/payment/'.$paymentUrl); 
            curl_setopt($ch,CURLOPT_HTTPHEADER,array( 'Accept: application/json','PAYMENT-API: Token="'.$secretToken.'",timestamp="'.$currentTimestamp.'",publickey="'.$publicKey.'",userID="'.$sessionUserID.'"'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paymentArray);
            $jsonString = curl_exec($ch); 
            curl_close($ch);
            $data       = json_decode($jsonString, true);
            return $data;

        }        
        
        /*
         * Get Payment details for particular payment ID
         */
        function getPaymentDetails($paymentID,$sessionUserID) {
            
            list($secretToken,$publicKey) = $this->getSecretToken();
            $currentTimestamp = time();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, system::getConfig()->getParam('mofilm', 'apiWebServiceUri')->getParamValue().'/v1/payment/'.$paymentID); 
            curl_setopt($ch,CURLOPT_HTTPHEADER,array( 'Accept: application/json','PAYMENT-API: Token="'.$secretToken.'",timestamp="'.$currentTimestamp.'",publickey="'.$publicKey.'",userID="'.$sessionUserID.'"'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_POST, false);
            $jsonString = curl_exec($ch); 
            curl_close($ch);
            $data       = json_decode($jsonString, true);
            return $data;
        }
        
        /*
         * Get Payment details for Multipart fee kind of payments to display on edit adhoc page
         */
        function getMultiPaymentDetails($paymentID,$sessionUserID) {
            
            list($secretToken,$publicKey) = $this->getSecretToken();
            $currentTimestamp = time();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, system::getConfig()->getParam('mofilm', 'apiWebServiceUri')->getParamValue().'/v1/payment/getMultiPayments/'.$paymentID); 
            curl_setopt($ch,CURLOPT_HTTPHEADER,array( 'Accept: application/json','PAYMENT-API: Token="'.$secretToken.'",timestamp="'.$currentTimestamp.'",publickey="'.$publicKey.'",userID="'.$sessionUserID.'"'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_POST, false);
            $jsonString = curl_exec($ch); 
            curl_close($ch);
            $data       = json_decode($jsonString, true);
            return $data;
        }
        
        /*
         * Update the status for adhoc fee payment
         */
        function doEditAdhocPayment($paymentArray,$sessionUserID) {
            
            list($secretToken,$publicKey) = $this->getSecretToken();
            $ch = curl_init();
            unset($paymentArray['Contributors']);
            $currentTimestamp = time();
            curl_setopt($ch, CURLOPT_URL, system::getConfig()->getParam('mofilm', 'apiWebServiceUri')->getParamValue().'/v1/payment/editStatusMultipart'); 
            curl_setopt($ch,CURLOPT_HTTPHEADER,array( 'Accept: application/json','PAYMENT-API: Token="'.$secretToken.'",timestamp="'.$currentTimestamp.'",publickey="'.$publicKey.'",userID="'.$sessionUserID.'"'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paymentArray);
            $jsonString = curl_exec($ch); 
            curl_close($ch);
            $data       = json_decode($jsonString, true);
            return $data;
        }
        
        /*
         * Update the particular column for payment
         */
        function doEditPayment($paymentArray,$sessionUserID) {
            list($secretToken,$publicKey) = $this->getSecretToken();
            $ch = curl_init();
            $currentTimestamp = time();
            curl_setopt($ch, CURLOPT_URL, system::getConfig()->getParam('mofilm', 'apiWebServiceUri')->getParamValue().'/v1/payment/'.$paymentArray['ID']); 
            curl_setopt($ch,CURLOPT_HTTPHEADER,array( 'Accept: application/json','PAYMENT-API: Token="'.$secretToken.'",timestamp="'.$currentTimestamp.'",publickey="'.$publicKey.'",userID="'.$sessionUserID.'"'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paymentArray);
            $jsonString = curl_exec($ch); 
            curl_close($ch);
            $data       = json_decode($jsonString, true);
            return $data;
        }
	
	/**
	 * Returns the object primary key value
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return parent::getPrimaryKey();
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		/**
		 * @todo change database and table to the ones required for paymentDetailsModel
		 */
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('%mofilm_content%').'.paymentDetails';

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
		return new mofilmPaymentDetails();
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		/**
		 * @todo set primary key for this object
		 */
		$this->setID($inPrimaryKey);
		$this->load();
		return $this;
	}
        
        function getSecretToken(){
            
                $query = '
			SELECT publicKey,privateKey
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.serviceKey WHERE clientName ="admin.mofilm.com" ';
                $oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
                $currentTimestamp = time();
                $secretToken = base64_encode(openssl_digest($res['publicKey'] . $currentTimestamp . $res['privateKey'], 'sha512'));
                return array($secretToken,$res['publicKey']);
        }
        
        function addConfirmPayment($paymentArray,$sessionUserID) {
            list($secretToken,$publicKey) = $this->getSecretToken();
            $PaymentString = http_build_query($paymentArray); 
            $ch = curl_init();
            $currentTimestamp = time();
            curl_setopt($ch, CURLOPT_URL, system::getConfig()->getParam('mofilm', 'apiWebServiceUri')->getParamValue().'/v1/payment/confirmPayment'); 
            curl_setopt($ch,CURLOPT_HTTPHEADER,array( 'Accept: application/json','PAYMENT-API: Token="'.$secretToken.'",timestamp="'.$currentTimestamp.'",publickey="'.$publicKey.'",userID="'.$sessionUserID.'"'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $PaymentString);
            $jsonString = curl_exec($ch); 
            curl_close($ch);
            $data  = json_decode($jsonString, true);
            return $data;

        }        
        
        
}