<?php
/**
 * helpPagesView.class.php
 *
 * helpPagesView class
 *
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpPagesView
 * @version $Rev: 624 $
 */


/**
 * helpPagesView class
 *
 * Provides the "helpPagesView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpPagesView
 */
class helpPagesView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'help');

		$this->addCssResource(
			new mvcViewCss('impromptu_css', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/impromptu.css')
		);
	}


	/**
	 * Builds the form for editing the object
	 *
	 * @return void
	 */
	function buildForm() {
		switch ( $this->getController()->getAction() ) {
			case helpPagesController::ACTION_SHOW_HELP:
				// cancel form building
			break;

			default:
				parent::buildForm();
			break;
		}
	}

	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('helpPagesList');
	}

	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		$this->addJavascriptResource(new mvcViewJavascript('tinymce', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/jquery.tinymce.js'));
		$this->addJavascriptResource(new mvcViewJavascript('tinymce_popup', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/plugins/browser/fileBrowser.js'));

		/*
		 * Add tag auto-complete
		 */
		$tags = array();
		foreach ( mofilmSystemHelpTags::listOfObjects() as $oTag ) {
			$tags[] = sprintf('"%s"', $oTag->getTag());
		}

		$this->addJavascriptResource(
			new mvcViewJavascript(
				'tagAutoCompleteData', mvcViewJavascript::TYPE_INLINE, 'var availableTags = ['.implode(', ', $tags).'];'
			)
		);
		$this->addJavascriptResource(
			new mvcViewJavascript(
				'tagAutoCompleteInit', mvcViewJavascript::TYPE_INLINE, '
				function split(val) {
					return val.split(/,\s*/);
				}
				function extractLast(term) {
					return split(term).pop();
				}
				$("#HelpPageTags")
					// dont navigate away from the field on tab when selecting an item
					.bind("keydown", function(event) {
						if ( event.keyCode === $.ui.keyCode.TAB && $(this).data("autocomplete").menu.active ) {
							event.preventDefault();
						}
					})
					.autocomplete({
						minLength: 0,
						source: function(request, response) {
							// delegate back to autocomplete, but extract the last term
							response($.ui.autocomplete.filter(
								availableTags, extractLast(request.term)
							));
						},
						focus: function() {
							return false;
						},
						select: function(event, ui) {
							var terms = split(this.value);
							// remove the current input
							terms.pop();
							// add the selected item
							terms.push(ui.item.value);
							// add placeholder to get the comma-and-space at the end
							terms.push("");
							this.value = terms.join(", ");
							return false;
						}
					});'
			)
		);

		return $this->getTpl('helpPagesForm');
	}
	
	/**
	 * Renders the help page content
	 *
	 * @return void
	 */
	function showHelpContents() {
		$this->getEngine()->assign('oHelpPage', utilityOutputWrapper::wrap($this->getModel()->getHelpPage()));

		$this->render($this->getTpl('showHelp'));
	}
	
	/**
	 * Renders the help titles for specific tag
	 * 
	 * @return void
	 */
	function showHelpContentsSelection() {
		$this->getEngine()->assign('oHelpPageSelections', utilityOutputWrapper::wrap($this->getModel()->getHelpTagSelection()));
		$this->getEngine()->assign('helpTag', utilityOutputWrapper::wrap($this->getModel()->getTagName()));
	    
		$this->render($this->getTpl('showHelpSelection'));
	}
}