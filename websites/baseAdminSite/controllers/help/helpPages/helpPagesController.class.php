<?php
/**
 * helpPagesController
 *
 * Stored in helpPagesController.class.php
 * 
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpPagesController
 * @version $Rev: 624 $
 */


/**
 * helpPagesController
 *
 * helpPagesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpPagesController
 */
class helpPagesController extends mvcDaoController {
    
    const ACTION_SHOW_HELP = 'showHelp';
    const ACTION_SHOW_HELP_SELECTION = 'showHelpSelection';

    /**
     * @see mvcControllerBase::initialise()
     */
    function initialise() {
	    parent::initialise();
		
	    $this->setControllerView('helpPagesView');
	    $this->getControllerActions()->addAction(self::ACTION_SEARCH);
	    $this->getControllerActions()->addAction(self::ACTION_SHOW_HELP);
	    $this->getControllerActions()->addAction(self::ACTION_SHOW_HELP_SELECTION);
        
	    $this->getMenuItems()
			->getItem(self::ACTION_VIEW)
				->addItem(
					new mvcControllerMenuItem(
						$this->buildUriPath(self::ACTION_SEARCH), 'Search', self::IMAGE_ACTION_SEARCH, 'Search', false, mvcControllerMenuItem::PATH_TYPE_URI, true
					)
				);
    }
        
    /**
     * @see mvcControllerBase::launch()
     */
    function launch() {
	    switch ($this->getAction()) {
		    case self::ACTION_SHOW_HELP:
			    $this->actionShowHelp();
			break;
		    case self::ACTION_SHOW_HELP_SELECTION:
			$this->actionShowHelpSelection();
			break;

		    default:
				parent::launch();
	    }
    }

    /**
     * Handles listing objects and search options
     *
     * @return void
     */
    function actionView() {
	    $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
	    $this->getInputManager()->addFilter('searchValue', utilityInputFilter::filterString());
	    $data = $this->getInputManager()->doFilter();

	    $this->setSearchOptionFromRequestData($data, 'DomainName');
	    $this->setSearchOptionFromRequestData($data, 'searchValue');

	    parent::actionView();
	}

    /**
     * Override default new method to inject pre-built strings
     *
     * @return void
     */
    function actionNew() {
	    $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
	    $data = $this->getInputManager()->doFilter();
	    $this->addInputToModel($data, $this->getModel());

	    $msg = 'User is creating a new '.get_class($this->getModel());
	    $this->buildActivityLog($msg)->save();

	    $oView = new $this->_ControllerView($this);
	    $oView->showDaoPage();
	}
        
    /**
     * Handels help page contents based on domain name and reference
     * 
     * @return void
     */
    function actionShowHelp() {
	    $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
	    $data = $this->getInputManager()->doFilter();
		
	    $this->addInputToModel($data, $this->getModel());
	    
	    $oView = new helpPagesView($this);	    
	    $oView->showHelpContents();
    }
    
    /**
     * Handels help page titles basen on tage selection
     * 
     * @return void
     */
    function actionShowHelpSelection() {
	    $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
	    $data = $this->getInputManager()->doFilter();

	    $this->addInputToModel($data, $this->getModel());
	    
	    $oView = new helpPagesView($this);
	    $oView->showHelpContentsSelection();
    }
        
    /**
     * @see mvcControllerBase::addInputFilters()
     */
    function addInputFilters() {
	    $this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
	    $this->getInputManager()->addFilter('ID', utilityInputFilter::filterInt());
	    $this->getInputManager()->addFilter('DomainName', utilityInputFilter::filterString());
	    $this->getInputManager()->addFilter('Reference', utilityInputFilter::filterString());
	    $this->getInputManager()->addFilter('Tags', utilityInputFilter::filterString());
	    $this->getInputManager()->addFilter('Title', utilityInputFilter::filterString());
	    $this->getInputManager()->addFilter('Language', utilityInputFilter::filterString());
	    $this->getInputManager()->addFilter('RelatedHelpTitles', utilityInputFilter::filterStringArray());
	    $this->getInputManager()->addFilter('searchValue', utilityInputFilter::filterString());
	    $this->getInputManager()->addFilter('tagID', utilityInputFilter::filterInt());
	    $this->getInputManager()->addFilter('tagName', utilityInputFilter::filterString());
    }
	
    /**
     * @see mvcControllerBase::addInputToModel()
     *
     * @param array $inData
     * @param helpPagesModel $inModel
     */
    function addInputToModel($inData, $inModel) {
	    $inModel->setID($inData['ID']);
	    $inModel->setDomainName($inData['DomainName']);
	    $inModel->setReference($inData['Reference']);
	    $inModel->setTitle($inData['Title']);
	    $inModel->setContent(stripslashes(trim($_POST['Content'])));
	    $inModel->setLanguage($inData['Language']);
	    $inModel->setTagID($inData['tagID']);
	    $inModel->setTagName($inData['tagName']);

		if ( array_key_exists('Tags', $inData) && strlen($inData['Tags']) > 0 ) {
			$inModel->getTagSet()->reset();

			$tags = explode(',', $inData['Tags']);
			foreach ( $tags as $tag ) {
				$oTag = mofilmSystemHelpTags::getInstanceByTag(trim($tag));
				if ( !$oTag->getID() ) {
					$oTag->setTag(trim($tag));
				}

				$inModel->getTagSet()->addObject($oTag);
			}
		}

		if ( array_key_exists('RelatedHelpTitles', $inData) && is_array($inData['RelatedHelpTitles']) ) {
			$inModel->getRelatedSet()->reset();

			foreach ( $inData['RelatedHelpTitles'] as $helpPageID ) {
				$oHelpPage = mofilmSystemHelpPageManager::getInstanceById($helpPageID);
				if ( $oHelpPage instanceof mofilmSystemHelpPages ) {
					$inModel->getRelatedSet()->addObject($oHelpPage);
				}
			}
		}
    }
	
    /**
     * Builds the model
     *
     * @return void
     */
    function buildModel() {
	    $oModel = new helpPagesModel();
	    $oModel->setSiteConfig($this->getRequest()->getDistributor()->getSiteConfig());
	    $this->setModel($oModel);
    }
}