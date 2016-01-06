<?php
/**
 * graphModel.class.php
 * 
 * graphModel class
 *
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category graphModel
 * @version $Rev: 11 $
 */


/**
 * graphModel class
 * 
 * Handles product cover images
 * 
 * @package websites_base
 * @subpackage controllers
 * @category graphModel
 */
class graphModel extends mvcImageModel {
	
	/**
	 * Array of permitted dimensions
	 *
	 * @var array
	 * @access protected
	 */
	protected $_AllowedDimensions = array(
		'370x250',
	);
	
	/**
	 * Default dimensions
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DefaultDimensions = '370x250';
	
	
	
	/**
	 * @see mvcImageProcesser::render()
	 */
	function render() {
		$this->buildImageLocation();
		
		if ( !$this->isCached() ) {
			if ( !file_exists(dirname($this->getImageLocation())) ) {
				@mkdir(dirname($this->getImageLocation()), 0755, true);
			}
			list($width, $height) = explode('x', $this->getImageDimensions());
			
			$data = array(
				'Movie1' => 201,
				'Movie2' => 112,
				'Movie3' => 52,
				'Movie4' => 48,
				'Movie5' => 37,
				'Movie6' => 22,
				'Movie7' => 18,
				'Movie8' => 11,
				'Movie9' => 5,
				'Movie10' => 1,
			);
			
			// Create a graph instance
			$graph = new Graph($width,$height);
			$graph->title->Show(false);
			$graph->SetScale('textint');
			$graph->Set90AndMargin(65, 5, 5, 5);
			$graph->SetImgFormat('png');
			$graph->xaxis->Hide(false);
			$graph->xaxis->HideLine(true);
			$graph->xaxis->HideLabels(false);
			$graph->xaxis->SetTickLabels(array_keys($data));
			$graph->yaxis->Hide(false);
			$graph->yaxis->HideLine(false);
			$graph->yaxis->HideLabels(true);
			
			// Create the linear plot
			$barplot = new BarPlot(array_values($data));
			$barplot->SetFillGradient('olivedrab1', 'olivedrab4', GRAD_VERT);
			$barplot->SetWidth(22);
			$barplot->value->Show();
			$barplot->value->SetAlign('left','center');
			$barplot->value->SetColor('black','black');
			$barplot->value->SetFormat('%d');
			
			// Add the plot to the graph
			$graph->Add($barplot);
			
			// Display the graph
			$graph->Stroke($this->getImageLocation());
		}
	}
	
	/**
	 * @see mvcImageProcesser::isCached()
	 */
	function isCached() {
		if ( file_exists($this->getImageLocation()) && is_readable($this->getImageLocation()) ) {
			if ( (time()-filemtime($this->getImageLocation())) < 300 ) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @see mvcImageProcesser::validateOptions()
	 */
	function validateOptions() {
		if ( !in_array($this->getImageDimensions(), $this->_AllowedDimensions) ) {
			$this->setOptions(array(self::OPTION_IMAGE_DIMENSIONS => $this->_DefaultDimensions));
		}
	}
	
	/**
	 * Builds the link to the source file as it will be stored
	 *
	 * @return void
	 */
	function buildImageLocation() {
		$base = system::getConfig()->getPathTemp().'/graphs/mofilm.com/';
		$target = utilityStringFunction::cleanDirSlashes($base.$this->getImageIdentifier().'-'.$this->getImageDimensions().'-image.png');
		
		$this->setImageLocation($target);
	}
}