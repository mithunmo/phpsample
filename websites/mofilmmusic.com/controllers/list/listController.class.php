<?php
/**
 * listController
 *
 * Stored in listController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category listController
 * @version $Rev: 736 $
 */


/**
 * listController
 *
 * listController class
 * 
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category listController
 */
class listController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_MOOD = 'mood';
	const ACTION_GENRE = 'genre';
	const ACTION_INSTRUMENT = 'instrument';
	const ACTION_SUB = 'sub';
	const ACTION_STYLE = 'style';
	const ACTION_TEMPO = "tempo";
	
	
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();

                $catalog = $this->getRequest()->getDistributor()->getSiteConfig()->getParam('site', 'momusic', "momusic")->getParamValue();      
                $this->getModel()->setCatalog($catalog);
     
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_MOOD);
		$this->getControllerActions()->addAction(self::ACTION_GENRE);
		$this->getControllerActions()->addAction(self::ACTION_SUB);
		$this->getControllerActions()->addAction(self::ACTION_INSTRUMENT);
		$this->getControllerActions()->addAction(self::ACTION_STYLE);
		$this->getControllerActions()->addAction(self::ACTION_TEMPO);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		
		if ( $this->getAction() == self::ACTION_MOOD ) {
			
			$id = $this->getActionFromRequest(false, 1);
                        $this->getModel()->setID($id);
			if ( $id == 2){
                            $mood = $this->getActionFromRequest(false, 2);
                            $this->getModel()->setMood($mood);
                        }
			
			
			$oView = new listView($this);
			$oView->showMoodListPage();			
			
		} elseif ( $this->getAction() == self::ACTION_GENRE ) {
			
			$id = $this->getActionFromRequest(false, 1);
			$this->getModel()->setID($id);

			
			$oView = new listView($this);
			$oView->showGenreListPage();			
			
		} elseif ( $this->getAction() == self::ACTION_SUB ) {

			$id = $this->getActionFromRequest(false, 1);
			$this->getModel()->setID($id);
			
			$oView = new listView($this);
			$oView->showSubListPage();			
			
		} elseif ( $this->getAction()== self::ACTION_INSTRUMENT ) {
			$id = $this->getActionFromRequest(false, 1);
			$this->getModel()->setID($id);

			
			$oView = new listView($this);
			$oView->showInstListPage();			
			
		} elseif ( $this->getAction()== self::ACTION_STYLE ) {
			$id = $this->getActionFromRequest(false, 1);
			$this->getModel()->setID($id);

			
			$oView = new listView($this);
			$oView->showStyleListPage();			
			
		} elseif ( $this->getAction() == self::ACTION_TEMPO ) {
			$oView = new listView($this);
			$oView->showTempoPage();						
		}
		
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return listModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new listModel();
		$this->setModel($oModel);
	}
}