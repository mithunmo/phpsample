<?php
/**
 * commsOutboundSearch
 *
 * Stored in search.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package mofilm
 * @subpackage outbound
 * @category commsOutboundSearch
 * @version $Rev: 10 $
 */


/**
 * commsOutboundSearch Class
 *
 * The main user search system.
 *
 * @package mofilm
 * @subpackage outbound
 * @category commsOutboundSearch
 */
class commsOutboundSearch extends baseSearch {

	const ORDERBY_ID = 'outboundMessages.messageID';
	const ORDERBY_DATE_CREATED = 'outboundMessages.createDate';
	const ORDERBY_DATE_SCHEDULED = 'outboundMessages.scheduledDate';
	const ORDERBY_DATE_SENT = 'outboundMessages.sentDate';
	
	/**
	 * Stores $_MessageID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MessageID;
	
	/**
	 * Stores $_OutboundTypeID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_OutboundTypeID;
	
	/**
	 * Stores $_GatewayID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_GatewayID;
	
	/**
	 * Stores $_GatewayAccountID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_GatewayAccountID;
	
	/**
	 * Stores $_CustomerID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_CustomerID;
	
	/**
	 * Stores $_Recipient
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Recipient;
	
	/**
	 * Stores $_Originator
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Originator;
	
	/**
	 * Stores $_StatusID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_StatusID;
	
	/**
	 * Stores $_Params
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Params;
	
	/**
	 * Stores $_OnlyBillableMessages
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyBillableMessages;
	
	/**
	 * Stores $_OnlySentMessages
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlySentMessages;
	
	/**
	 * Stores $_OnlyAcknowledgedMessages
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyAcknowledgedMessages;
	
	/**
	 * Stores $_LoadObjectDetails
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LoadObjectDetails;
	
	
	
	/**
	 * @see baseSearch::reset()
	 */
	function reset() {
		parent::reset();
		
		$this->_MessageID = null;
		$this->_OutboundTypeID = null;
		$this->_GatewayID = null;
		$this->_GatewayAccountID = null;
		$this->_CustomerID = null;
		$this->_Recipient = null;
		$this->_Originator = null;
		$this->_StatusID = null;
		$this->_Params = array();
		
		$this->_OnlyBillableMessages = false;
		$this->_OnlySentMessages = false;
		$this->_OnlyAcknowledgedMessages = false;
		$this->_LoadObjectDetails = true;

		$this->_OrderBy = self::ORDERBY_DATE_CREATED;
		$this->_AllowedOrderBy = array(self::ORDERBY_ID, self::ORDERBY_DATE_CREATED, self::ORDERBY_DATE_SCHEDULED, self::ORDERBY_DATE_SENT);
	}

	/**
	 * @see baseSearch::initialise()
	 */
	function initialise() {
		parent::initialise();
	}

	/**
	 * Runs the search using the supplied data
	 *
	 * @return wurflResultSet
	 */
	function search() {
		if ( $this->canSearchRun() ) {
			if ( $this->getMessageID() ) {
				return new commsOutboundSearchResult(array(commsOutboundManager::getInstanceByID($this->getMessageID())), 1, $this);
			}

			$query = '';
			$this->buildSelect($query);
			$this->buildWhere($query);
			$this->buildOrderBy($query);
			$this->buildLimit($query);
			
			$count = 0;
			$list = array();

			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$tmp = array();
				foreach ( $oStmt as $row ) {
					$tmp[] = $row['messageID'];
				}
				
				$count = dbManager::getInstance()->query('SELECT FOUND_ROWS() AS Results')->fetchColumn();
				if ( count($tmp) > 0 ) {
					$oManager = commsOutboundManager::getInstance();
					$oManager->setLoadObjectDetails($this->getLoadObjectDetails());
					$list = $oManager->loadMessagesByArray($tmp);
				}
			}
			$oStmt->closeCursor();

			return new commsOutboundSearchResult($list, $count, $this);
		}
		/*
		 * Always return empty result set
		 */
		return new commsOutboundSearchResult(array(), 0, $this);
	}

	/**
	 * @see baseSearchInterface::canSearchRun()
	 */
	function canSearchRun() {
		$return = true;
		if (
			!$this->getGatewayAccountID() && !$this->getGatewayID() && !$this->getMessageID() &&
			!$this->getOriginator() && !$this->getOutboundTypeID() && !$this->getRecipient() &&
			!$this->getStatusID() && !$this->getCustomerID() && !$this->getParamsCount()
		) {
			$return = false;
		}
		return $return;
	}

	/**
	 * @see baseSearchInterface::buildSelect()
	 */
	function buildSelect(&$inQuery) {
		$inQuery = 'SELECT SQL_CALC_FOUND_ROWS outboundMessages.messageID ';
		$inQuery .= ' FROM '.system::getConfig()->getDatabase('comms').'.outboundMessages ';
		
		if ( $this->getParamsCount() > 0 ) {
			$inQuery .= ' INNER JOIN '.system::getConfig()->getDatabase('comms').'.outboundMessageParams USING (messageID) ';
		}
	}

	/**
	 * @see baseSearchInterface::buildWhere()
	 */
	function buildWhere(&$inQuery) {
		$where = array();
		if ( $this->getGatewayAccountID() ) {
			$where[] = 'outboundMessages.gatewayAccountID = '.dbManager::getInstance()->quote($this->getGatewayAccountID());
		}
		if ( $this->getGatewayID() ) {
			$where[] = 'outboundMessages.gatewayID = '.dbManager::getInstance()->quote($this->getGatewayID());
		}
		if ( $this->getCustomerID() ) {
			$where[] = 'outboundMessages.customerID = '.dbManager::getInstance()->quote($this->getCustomerID());
		}
		if ( $this->getRecipient() ) {
			$where[] = 'outboundMessages.recipient = '.dbManager::getInstance()->quote($this->getRecipient());
		}
		if ( $this->getOriginator() ) {
			$where[] = 'outboundMessages.originator = '.dbManager::getInstance()->quote($this->getOriginator());
		}
		if ( $this->getOutboundTypeID() ) {
			$where[] = 'outboundMessages.outboundTypeID = '.dbManager::getInstance()->quote($this->getOutboundTypeID());
		}
		if ( $this->getStatusID() ) {
			$where[] = 'outboundMessages.statusID = '.dbManager::getInstance()->quote($this->getStatusID());
		}
		
		if ( $this->getParamsCount() > 0 ) {
			$params = array();
			foreach ( $this->getParams() as $paramName => $paramValue ) {
				if ( strlen($paramName) > 0 && $paramValue ) {
					$params[] = '(outboundMessageParams.paramName = '.dbManager::getInstance()->quote($paramName).' AND outboundMessageParams.paramValue = '.dbManager::getInstance()->quote($paramValue).')';
				}
			}
			
			if ( count($params) > 0 ) {
				$where[] = '('.implode(' AND ', $params).')';
			}
		}
		
		if ( $this->getOnlyAcknowledgedMessages() ) {
			$where[] = '(outboundMessages.acknowledgedDate IS NOT NULL || outboundMessages.acknowledgedDate != "0000-00-00 00:00:00")';
		}
		if ( $this->getOnlyBillableMessages() ) {
			$where[] = 'outboundMessages.charge > 0';
		}
		if ( $this->getOnlySentMessages() ) {
			$where[] = '(outboundMessages.sentDate IS NOT NULL || outboundMessages.acknowledgedDate != "0000-00-00 00:00:00")';
		}
		
		if ( count($where) > 0 ) {
			$join = $this->getWhereType() == self::WHERE_USING_OR ? ' OR ' : ' AND ';
			$inQuery .= ' WHERE '.implode($join, $where);
		}
	}
	
	
	
	/**
	 * Returns $_MessageID
	 *
	 * @return integer
	 */
	function getMessageID() {
		return $this->_MessageID;
	}
	
	/**
	 * Set $_MessageID to $inMessageID
	 *
	 * @param integer $inMessageID
	 * @return commsOutboundSearch
	 */
	function setMessageID($inMessageID) {
		if ( $inMessageID !== $this->_MessageID ) {
			$this->_MessageID = $inMessageID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_OutboundTypeID
	 *
	 * @return integer
	 */
	function getOutboundTypeID() {
		return $this->_OutboundTypeID;
	}
	
	/**
	 * Set $_OutboundTypeID to $inOutboundTypeID
	 *
	 * @param integer $inOutboundTypeID
	 * @return commsOutboundSearch
	 */
	function setOutboundTypeID($inOutboundTypeID) {
		if ( $inOutboundTypeID !== $this->_OutboundTypeID ) {
			$this->_OutboundTypeID = $inOutboundTypeID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_GatewayID
	 *
	 * @return integer
	 */
	function getGatewayID() {
		return $this->_GatewayID;
	}
	
	/**
	 * Set $_GatewayID to $inGatewayID
	 *
	 * @param integer $inGatewayID
	 * @return commsOutboundSearch
	 */
	function setGatewayID($inGatewayID) {
		if ( $inGatewayID !== $this->_GatewayID ) {
			$this->_GatewayID = $inGatewayID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_GatewayAccountID
	 *
	 * @return integer
	 */
	function getGatewayAccountID() {
		return $this->_GatewayAccountID;
	}
	
	/**
	 * Set $_GatewayAccountID to $inGatewayAccountID
	 *
	 * @param integer $inGatewayAccountID
	 * @return commsOutboundSearch
	 */
	function setGatewayAccountID($inGatewayAccountID) {
		if ( $inGatewayAccountID !== $this->_GatewayAccountID ) {
			$this->_GatewayAccountID = $inGatewayAccountID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CustomerID
	 *
	 * @return integer
	 */
	function getCustomerID() {
		return $this->_CustomerID;
	}
	
	/**
	 * Set $_CustomerID to $inCustomerID
	 *
	 * @param integer $inCustomerID
	 * @return commsOutboundSearch
	 */
	function setCustomerID($inCustomerID) {
		if ( $inCustomerID !== $this->_CustomerID ) {
			$this->_CustomerID = $inCustomerID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Recipient
	 *
	 * @return string
	 */
	function getRecipient() {
		return $this->_Recipient;
	}
	
	/**
	 * Set $_Recipient to $inRecipient
	 *
	 * @param string $inRecipient
	 * @return commsOutboundSearch
	 */
	function setRecipient($inRecipient) {
		if ( $inRecipient !== $this->_Recipient ) {
			$this->_Recipient = $inRecipient;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Originator
	 *
	 * @return string
	 */
	function getOriginator() {
		return $this->_Originator;
	}
	
	/**
	 * Set $_Originator to $inOriginator
	 *
	 * @param string $inOriginator
	 * @return commsOutboundSearch
	 */
	function setOriginator($inOriginator) {
		if ( $inOriginator !== $this->_Originator ) {
			$this->_Originator = $inOriginator;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_StatusID
	 *
	 * @return integer
	 */
	function getStatusID() {
		return $this->_StatusID;
	}
	
	/**
	 * Set $_StatusID to $inStatusID
	 *
	 * @param integer $inStatusID
	 * @return commsOutboundSearch
	 */
	function setStatusID($inStatusID) {
		if ( $inStatusID !== $this->_StatusID ) {
			$this->_StatusID = $inStatusID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Params
	 *
	 * @return array
	 */
	function getParams() {
		return $this->_Params;
	}
	
	/**
	 * Returns the number of parameters in the search
	 * 
	 * @return integer
	 */
	function getParamsCount() {
		return count($this->_Params);
	}
	
	/**
	 * Adds a param to the search criteria
	 * 
	 * @param string $inParamName
	 * @param mixed $inParamValue
	 * @return commsOutboundSearch
	 */
	function addParam($inParamName, $inParamValue) {
		$this->_Params[$inParamName] = $inParamValue;
		$this->setModified();
		return $this;
	}
	
	/**
	 * Set $_Params to $inParams
	 *
	 * @param array $inParams
	 * @return commsOutboundSearch
	 */
	function setParams(array $inParams) {
		if ( $inParams !== $this->_Params ) {
			$this->_Params = $inParams;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_OnlyBillableMessages
	 *
	 * @return boolean
	 */
	function getOnlyBillableMessages() {
		return $this->_OnlyBillableMessages;
	}
	
	/**
	 * Set $_OnlyBillableMessages to $inOnlyBillableMessages
	 *
	 * @param boolean $inOnlyBillableMessages
	 * @return commsOutboundSearch
	 */
	function setOnlyBillableMessages($inOnlyBillableMessages) {
		if ( $inOnlyBillableMessages !== $this->_OnlyBillableMessages ) {
			$this->_OnlyBillableMessages = $inOnlyBillableMessages;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_OnlySentMessages
	 *
	 * @return boolean
	 */
	function getOnlySentMessages() {
		return $this->_OnlySentMessages;
	}
	
	/**
	 * Set $_OnlySentMessages to $inOnlySentMessages
	 *
	 * @param boolean $inOnlySentMessages
	 * @return commsOutboundSearch
	 */
	function setOnlySentMessages($inOnlySentMessages) {
		if ( $inOnlySentMessages !== $this->_OnlySentMessages ) {
			$this->_OnlySentMessages = $inOnlySentMessages;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_OnlyAcknowledgedMessages
	 *
	 * @return boolean
	 */
	function getOnlyAcknowledgedMessages() {
		return $this->_OnlyAcknowledgedMessages;
	}
	
	/**
	 * Set $_OnlyAcknowledgedMessages to $inOnlyAcknowledgedMessages
	 *
	 * @param boolean $inOnlyAcknowledgedMessages
	 * @return commsOutboundSearch
	 */
	function setOnlyAcknowledgedMessages($inOnlyAcknowledgedMessages) {
		if ( $inOnlyAcknowledgedMessages !== $this->_OnlyAcknowledgedMessages ) {
			$this->_OnlyAcknowledgedMessages = $inOnlyAcknowledgedMessages;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_LoadObjectDetails
	 *
	 * @return boolean
	 */
	function getLoadObjectDetails() {
		return $this->_LoadObjectDetails;
	}
	
	/**
	 * Set $_LoadObjectDetails to $inLoadObjectDetails
	 *
	 * @param boolean $inLoadObjectDetails
	 * @return commsOutboundSearch
	 */
	function setLoadObjectDetails($inLoadObjectDetails) {
		if ( $inLoadObjectDetails !== $this->_LoadObjectDetails ) {
			$this->_LoadObjectDetails = $inLoadObjectDetails;
			$this->setModified();
		}
		return $this;
	}
}