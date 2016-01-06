<?php
/**
 * processor.iface.php
 * 
 * mvcImageProcessor interface
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcImageProcessor
 * @version $Rev: 650 $
 */


/**
 * mvcImageProcessor
 * 
 * Image processor interface declaration, used by the {@link mvcImageController} system.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcImageProcessor
 */
interface mvcImageProcessor {
	
	/**
	 * Renders an image to a file - does not output it
	 *
	 * @return void
	 */
	function render();
	
	/**
	 * Returns true if the image is already cached
	 *
	 * @return boolean
	 */
	function isCached();
	
	/**
	 * Set an array of options to the model
	 * 
	 * @param $inOptions
	 * @return mvcImageProcessor
	 */
	function setOptions(array $inOptions = array());
	
	/**
	 * Validates the options and ensures that they are as expected
	 *
	 * @return void
	 * @throws mvcModelException
	 */
	function validateOptions();
	
	/**
	 * Returns the image mime-type
	 * 
	 * @return string
	 */
	function getImageMimeType();
	
	/**
	 * Returns the path to the processed image for serving
	 * 
	 * @return string
	 */
	function getImageLocation();
}