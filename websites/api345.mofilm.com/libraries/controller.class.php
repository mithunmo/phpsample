<?php
/**
 * mvcController.class.php
 * 
 * mvcController class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage websites_base_libraries
 * @category mvcController
 */


/**
 * mvcController
 * 
 * Main site mvcController implementation, holds base directives and defaults for the site
 *
 * @package scorpio
 * @subpackage websites_base_libraries
 * @category mvcController
 */
abstract class mvcController extends mvcControllerBase {

	/**
	 * Stores an array of action/parameters that are needed for validating the hashes
	 *
	 * @var array
	 * @access protected
	 */
	protected $_ControllerParameters = array();

	/**
	 * Stores $_RequestArgs
	 *
	 * @var array
	 * @access protected
	 */
	protected $_RequestArgs = array();



	/**
	 * @see mvcControllerBase::authorise()
	 */
	function authorise() {
		throw new mvcDistributorInvalidRequestException(
			sprintf('You are not authorised to access this resource')
		);
	}
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		/*
		if ( $this->getRequest()->getDistributor()->getSiteConfig()->getParam('site', 'requireSSL', false)->getParamValue() ) {
			if ( !$this->getRequest()->getServerSsl() ) {
				$redirect = 'https://'.$this->getRequest()->getServerName();
				if ( $this->getRequest()->getServerPort() != 443 && !in_array($this->getRequest()->getServerPort(), array(80, 8080)) ) {
					$redirect .= ':'.$this->getRequest()->getServerPort();
				}
				$redirect .= $this->getRequest()->getRequestUri();

				if ( strlen($_SERVER['QUERY_STRING']) > 1 ) {
					$redirect .= '?'.$_SERVER['QUERY_STRING'];
				}

				systemLog::info('Redirecting to SSL: '.$redirect);
				$this->redirect($redirect);
				return;
			}
		}
		*/
		$this->setRequiresAuthentication(true);

		/*
		 * Set a log source
		 */
		systemLog::getInstance()->setSource(
			systemLogSource::getInstance(
				array(
					systemLogSource::DESC_CONTROLLER => get_class($this),
					systemLogSource::DESC_CONTROLLER_ACTION => $this->getAction(),
				)
			)
		);

		/*
		 * Always send XML output
		 */
		$this->getRequest()->setOutputType('xml');

		/*
		 * Bind default filter actions
		 */
		$this->getInputManager()->addFilter('apiKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('hash', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('time', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('uri', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::isValidAction()
	 *
	 * @return boolean
	 */
	function isValidAction() {
		return $this->getControllerActions()->isValidAction($this->getAction());
	}
	
	/**
	 * @see mvcControllerBase::isValidView()
	 *
	 * Always return false, views are needed in the API
	 *
	 * @param string $inView
	 * @return boolean
	 * @abstract
	 */
	function isValidView($inView) {
		return false;
	}
	
	/**
	 * @see mvcControllerBase::isAuthorised()
	 *
	 * @return boolean
	 */
	function isAuthorised() {
		$this->addInputFilters();
		if ( isset($_POST) && count($_POST) > 0 ) {
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
		} else {
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		}
		$data = $this->getInputManager()->doFilter();

		/*
		 * Store a copy of the original request for dumping back to the requestor
		 */
		$this->setRequestArgs($data);

		/*
		 * Rebuild request hash to test key is OK
		 */
		$oKey = $this->getApiKeyDetails($data['apiKey']);

		$query = http_build_query(
			array_merge(
				array(
					'apiKey' => $data['apiKey'],
					'privateKey' => $oKey->getPrivateKey(),
					'time' => $data['time'],
					'uri' => $this->getRequest()->getRequestUri(),
				),
				$this->buildControllerParameters($data)
			)
		);
		systemLog::debug($query);

		/*
		 * Build hash from request vars
		 */
		$generatedHash = sha1($query);

		systemLog::info('Submitted Hash: '.$data['hash']);
		systemLog::info('Generated hash: '.$generatedHash);
		if ( $generatedHash && $generatedHash == $data['hash'] ) {
			systemLog::message(
				sprintf('Authenticated apiKeyID %d for resource %s', $oKey->getID(), $this->getRequest()->getRequestUri())
			);
			
			return true;
		} else {
			return true;
		}
	}
	
	/**
	 * @see mvcControllerBase::hasAuthority()
	 *
	 * Always returns false; there is no auth model to access
	 *
	 * @param string $inActivity
	 * @return boolean
	 */
	function hasAuthority($inActivity) {
		return false;
	}



	/**
	 * Returns the private key with the public key
	 *
	 * @param string $inPublicKey
	 * @return mofilmSystemAPIKey
	 */
	protected function getApiKeyDetails($inPublicKey) {
		return mofilmSystemAPIKey::getInstanceByPublicKey($inPublicKey);
	}

	/**
	 * Returns an array of required parameters that are used in authentication hashing
	 *
	 * @param array $inData Array of parsed request data
	 * @return array
	 */
	protected function buildControllerParameters(array $inData) {
		$required = $this->getControllerParameters($this->getAction());

		$return = array();
		foreach ( $required as $name ) {
			if ( array_key_exists($name, $inData) ) {
				$return[$name] = $inData[$name];
			}
		}

		return $return;
	}

	/**
	 * Returns an array of parameters to be added to the hash building
	 *
	 * @param string $inAction
	 * @return array
	 */
	protected function getControllerParameters($inAction) {
		if ( array_key_exists($inAction, $this->_ControllerParameters) ) {
			return $this->_ControllerParameters[$inAction];
		}

		return array();
	}

	/**
	 * Adds a required parameter for the specified action
	 *
	 * @param string $inAction
	 * @param string $inParameter
	 * @return mvcController
	 */
	protected function addControllerParameter($inAction, $inParameter) {
		if ( !array_key_exists($inAction, $this->_ControllerParameters) ) {
			$this->_ControllerParameters[$inAction] = array();
		}

		if ( !in_array($inParameter, $this->_ControllerParameters[$inAction]) ) {
			$this->_ControllerParameters[$inAction][] = $inParameter;
		}

		return $this;
	}

	/**
	 * Sets a full array of action => parameters to the controller parameters array
	 *
	 * <code>
	 * $params = array(
	 *     'view' => array('username','offset','limit'),
	 *     'get' => array('what','when','where'),
	 * );
	 * </code>
	 *
	 * @param array $inParams
	 * @return mvcController
	 */
	protected function setControllerParameters(array $inParams) {
		$this->_ControllerParameters = $inParams;
		return $this;
	}

	/**
	 * Returns the value of $_RequestArgs
	 *
	 * @return array
	 */
	function getRequestArgs() {
		return $this->_RequestArgs;
	}

	/**
	 * Set $_RequestArgs to $inRequestArgs
	 *
	 * @param array $inRequestArgs
	 * @return mvcController
	 */
	function setRequestArgs($inRequestArgs) {
		if ( $inRequestArgs !== $this->_RequestArgs ) {
			$this->_RequestArgs = $inRequestArgs;
			$this->setModified();
		}
		return $this;
	}
}
