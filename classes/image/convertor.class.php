<?php
/**
 * imageConvertor
 *
 * Stored in imageConvertor.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage image
 * @category imageConvertor
 * @version $Rev: 10 $
 */


/**
 * imageConvertor
 *
 * Converts an image source to the specified format and size, resampling and
 * applying a background colour if appropriate. This class will also enforce
 * particularly dimensions e.g. an image is resampled to 90x90, but is only 40
 * on one dimension, it will be centred and output as 90x90.
 * 
 * The convertor works on a single resource at a time. The resource can be a
 * file, binary data or a file handle (stream). The output can be directed to
 * a file, internally stored (which happens anyway) or to a target location.
 * To output data directly set the output location as a '-'. To not output any
 * data, supply an empty output location.
 * 
 * To preserve transparent graphics, you must use PNG as the output format. This
 * will preserve transparency anyway - otherwise you can enable output padding
 * and set the colour to 'transparent' (i.e. the string transparent). Using
 * transparency requires ImageMagick 6.3.8 or above, if it is not present a
 * parse error will result from the use of an undefined class constant.
 * 
 * The options can be set at construction time, or before calling process. Process
 * can take an image resource as a parameter if it has not been set yet. Via this
 * you can use imageConvertor in a loop by passing the image as the parameter.
 * You may need to update the output location and/or output filename.
 * 
 * @package mofilm
 * @subpackage image
 * @category imageConvertor
 */
class imageConvertor {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_OptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;
	
	/**
	 * The origin, input image either a resource, file location or stream
	 * 
	 * @var mixed
	 */
	const OPTION_INPUT_IMAGE = 'input.image';
	
	/**
	 * SHA1 hash of the input image data, set automatically
	 * 
	 * @var string
	 */
	const OPTION_INPUT_FILE_HASH = 'image.hash';
	
	/**
	 * Input file name for file resources, set automatically
	 * 
	 * @var string
	 */
	const OPTION_INPUT_FILE_NAME = 'input.name';
	
	/**
	 * Where the converted image should be stored, can be a resource
	 * 
	 * @var mixed
	 */
	const OPTION_OUTPUT_LOCATION = 'output.location';
	
	/**
	 * Should existing files be overwritten or not, default not
	 * 
	 * @var boolean
	 */
	const OPTION_OUTPUT_OVERWRITE_FILES = 'output.overwrite';
	
	/**
	 * The output filename, allows for some dynamic fields:
	 * 
	 * %DATE% - the current date
	 * %TIME% - the current time
	 * %HASH% - an SHA1 hash of the input binary
	 * %FILENAME% - the input filename (if given)
	 * 
	 * @var string
	 */
	const OPTION_OUTPUT_FILENAME = 'output.filename';
	
	/**
	 * The output format the image will be exported to, must be supported by iMagick
	 * 
	 * @var string
	 */
	const OPTION_OUTPUT_FORMAT = 'output.format';
	
	/**
	 * Output image width
	 * 
	 * @var integer
	 */
	const OPTION_OUTPUT_WIDTH = 'output.width';
	
	/**
	 * Output image height
	 * 
	 * @var integer
	 */
	const OPTION_OUTPUT_HEIGHT = 'output.height';
	
	/**
	 * For JPEG, how much compression to use 0 - lots, 100 - as little as possible
	 * 
	 * @var integer
	 */
	const OPTION_OUTPUT_QUALITY = 'output.quality';
	
	/**
	 * Boolean, true to size image to widthxheight, false to leave as is
	 * 
	 * @var boolean
	 */
	const OPTION_OUTPUT_PAD_IMAGE = 'output.pad.image';
	
	/**
	 * Image pad background colour, either word, hex or "transparent" for transparent (PNG only)
	 * 
	 * @var string
	 */
	const OPTION_OUTPUT_PAD_COLOUR = 'output.pad.colour';
	
	/**
	 * Stores $_ConvertedImageData
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ConvertedImageData;
	
	
	
	/**
	 * Creates a new imageConvertor instance
	 * 
	 * @param array $inOptions
	 */
	function __construct(array $inOptions = array()) {
		$this->reset();
		$this->setOptions($inOptions);
	}
	
	/**
	 * Resets the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_OptionsSet = null;
		$this->_ConvertedImageData = null;
		$this->setModified(false);
	}
	
	
	
	/**
	 * Processes the current image file, or the passed image resource
	 * 
	 * @param mixed $inImage
	 * @return mixed
	 */
	function process($inImage = null) {
		if ( $inImage !== null ) {
			$this->setInputImage($inImage);
		}
		
		$data = $this->getImageResource();
					
		$oImagick = new Imagick();
		$oImagick->readImageBlob($data);
		$oImagick->setImageColorspace(1);
		$oImagick->setImageFormat($this->getOutputFormat());
		
		if ( !$this->getOutputPadImage() && $this->getOutputFormat() == 'jpeg' ) {
			$oImagick->setCompression(Imagick::COMPRESSION_JPEG);
			$oImagick->setCompressionQuality($this->getOutputQuality());
		}
		
		$oImagick->scaleImage($this->getOutputWidth(), $this->getOutputHeight(), true);
		
		if ( $this->getOutputPadImage() ) {
			if ( $oImagick->getImageWidth() < $this->getOutputWidth() ) {
				$x = floor(($this->getOutputWidth()-$oImagick->getImageWidth())/2);
			}
			if ( $oImagick->getImageHeight() < $this->getOutputHeight() ) {
				$y = floor(($this->getOutputHeight()-$oImagick->getImageHeight())/2);
			}
			
			$canvas = new Imagick();
			if ( $this->getOutputPadColour() == 'transparent' ) {
				if ( strpos($this->getOutputFormat(), 'png') === false ) {
					$this->setOutputFormat('png32');
				}
				
				$oPixel = new ImagickPixel();
				if ( defined(Imagick::ALPHACHANNEL_TRANSPARENT) ) {
					$canvas->setImageAlphaChannel(Imagick::ALPHACHANNEL_TRANSPARENT);
				} else {
					$canvas->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
				}
			} else {
				$oPixel = new ImagickPixel($this->getOutputPadColour());
			}
			
			$canvas->newImage($this->getOutputWidth(), $this->getOutputHeight(), $oPixel);
			$canvas->setImageFormat($this->getOutputFormat());
			
			if ( $this->getOutputFormat() == 'jpeg' ) {
				$canvas->setCompression(Imagick::COMPRESSION_JPEG);
				$canvas->setCompressionQuality($this->getOutputQuality());
			}
			
			$canvas->compositeImage($oImagick, Imagick::COMPOSITE_OVER, $x, $y);
			
			$data = $canvas->getImageBlob();
			
			$canvas->destroy();
			unset($canvas);
		} else {
			$data = $oImagick->getImageBlob();
		}
		
		$oImagick->destroy();
		unset($oImagick);
		
		$this->setConvertedImageData($data);
		
		if ( $this->getOption(self::OPTION_OUTPUT_LOCATION) == '-' ) {
			return $data;
		} else {
			if ( !$this->getOutputLocation() ) {
				systemLog::notice('Image converted, data ready in convertor');
				return true;
			}
			
			if ( !file_exists($this->getOutputLocation()) ) {
				throw new Exception($this->getOutputLocation().' does not exist');
			}
			if ( !is_writable($this->getOutputLocation()) ) {
				throw new Exception($this->getOutputLocation().' is not writable');
			}
			
			$file = $this->getOutputLocation().system::getDirSeparator().$this->getOutputFilename();
			$bytes = file_put_contents($file, $data);
			systemLog::notice("Wrote $bytes bytes to $file");
			return ($bytes > 0);
		}
	}
	
	/**
	 * Returns the image data from the specified input image
	 * 
	 * @return string
	 */
	function getImageResource() {
		systemLog::info('Attempting to locate image data');
		
		$data = false;
		$image = $this->getOption(self::OPTION_INPUT_IMAGE, null);
		if ( !$image ) {
			throw new Exception('No image resource to process');
		}
		
		if ( is_resource($image) ) {
			systemLog::notice('Image resource supplied, extracting stream data');
			$meta = stream_get_meta_data($image);
			switch ( $meta['wrapper_type'] ) {
				case 'plainfile':
					$data = stream_get_contents($image);
				break;
				
				default:
					systemLog::error('Wrapper: '.$meta['wrapper_type'].' has not been implemented yet');
					throw new Exception('Unhandled stream type: '.$meta['wrapper_type']);
			}
		} elseif ( is_file($image) ) {
			systemLog::notice('Image is a file location, attempting to locate');
			if ( !is_readable($image) ) {
				throw new Exception('Unable to read file @ '.$image);
			}
			
			$info = pathinfo($image);
			$this->setOption(self::OPTION_INPUT_FILE_NAME, basename($image, '.'.$info['extension']));
			
			$data = file_get_contents($image);
		} elseif ( preg_match('/[\x00-\x08\x0E-\x1F\x7F]/', $image) ) {
			systemLog::notice('Image is a binary string');
			$data = $image;
		} else {
			systemLog::error('No image resource to handle!');
			throw new Exception('Unable to handle supplied image resource');
		}
		
		if ( !$data ) {
			throw new Exception('Loaded image resource, but no data found');
		} else {
			$this->setOption(self::OPTION_INPUT_FILE_HASH, sha1($data));
			return $data;
		}
	}
	
	

	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->_OptionsSet instanceof baseOptionsSet ) {
			$modified = $this->_OptionsSet->isModified() || $modified;
		}
		return $modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return imageConvertor
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns $_ConvertedImageData
	 *
	 * @return string
	 */
	function getConvertedImageData() {
		return $this->_ConvertedImageData;
	}
	
	/**
	 * Set $_ConvertedImageData to $inConvertedImageData
	 *
	 * @param string $inConvertedImageData
	 * @return imageConvertor
	 */
	function setConvertedImageData($inConvertedImageData) {
		if ( $inConvertedImageData !== $this->_ConvertedImageData ) {
			$this->_ConvertedImageData = $inConvertedImageData;
			$this->setModified();
		}
		return $this;
	}
	
	

	/**
	 * Returns $_OptionsSet
	 *
	 * @return baseOptionsSet
	 */
	function getOptionsSet() {
		if ( !$this->_OptionsSet instanceof baseOptionsSet ) {
			$this->_OptionsSet = new baseOptionsSet();
		}
		return $this->_OptionsSet;
	}
	
	/**
	 * Set $_OptionsSet to $inOptionsSet
	 *
	 * @param baseOptionsSet $inOptionsSet
	 * @return imageConvertor
	 */
	function setOptionsSet(baseOptionsSet $inOptionsSet) {
		if ( $inOptionsSet !== $this->_OptionsSet ) {
			$this->_OptionsSet = $inOptionsSet;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the option value for $inOption, or $inDefault if not found
	 * 
	 * @param string $inOption
	 * @param mixed $inDefault
	 * @return mixed
	 */
	function getOption($inOption, $inDefault = null) {
		return $this->getOptionsSet()->getOptions($inOption, $inDefault);
	}
	
	/**
	 * Sets a single option $inOption to $inValue
	 * 
	 * @param string $inOption
	 * @param mixed $inValue
	 * @return imageConvertor
	 */
	function setOption($inOption, $inValue) {
		$this->getOptionsSet()->setOptions(array($inOption => $inValue));
		return $this;
	}
	
	/**
	 * Sets an array of options
	 * 
	 * @param array $inOptions
	 * @return imageConvertor
	 */
	function setOptions(array $inOptions) {
		$this->getOptionsSet()->setOptions($inOptions);
		return $this;
	}
	
	
	
	/**
	 * Sets the input image
	 * 
	 * @param mixed $inImage
	 * @return imageConvertor
	 */
	function setInputImage($inImage) {
		return $this->setOption(self::OPTION_INPUT_IMAGE, $inImage);
	}
	
	/**
	 * Returns the output location, system tmp if not set
	 * 
	 * @return string
	 */
	function getOutputLocation() {
		return $this->getOption(self::OPTION_OUTPUT_LOCATION, sys_get_temp_dir());
	}
	
	/**
	 * Set the output location (folder)
	 * 
	 * @param string $inLocation
	 * @return imageConvertor
	 */
	function setOutputLocation($inLocation) {
		return $this->setOption(self::OPTION_OUTPUT_LOCATION, $inLocation);
	}
	
	/**
	 * Returns the output filename
	 * 
	 * @return string
	 */
	function getOutputFilename() {
		$filename = $this->getOption(self::OPTION_OUTPUT_FILENAME, '%HASH%');
		$info = pathinfo($filename);
		$filename = basename($filename, '.'.$info['extension']);
		
		$filename = str_replace(
			array('%DATE%', '%TIME%', '%FILENAME%', '%HASH%'),
			array(date('Ymd'), date('His'), $this->getOption(self::OPTION_INPUT_FILE_NAME, ''), $this->getOption(self::OPTION_INPUT_FILE_HASH)),
			$filename
		);
		
		return $filename.$this->getOutputFileExtension();
	}
	
	/**
	 * Returns the output file extension
	 * 
	 * @return string
	 */
	function getOutputFileExtension() {
		switch ( $this->getOutputFormat() ) {
			case 'jpeg':
			case 'jpeg2000':
				return '.jpg';
			break;
			
			case 'png24':
			case 'png32':
			case 'png8':
				return '.png';
			break;
			
			default:
				return '.'.$this->getOutputFormat(); 
		}
	}
	
	/**
	 * Set the output filename
	 * 
	 * @param string $inFilename
	 * @return imageConvertor
	 */
	function setOutputFilename($inFilename) {
		return $this->setOption(self::OPTION_OUTPUT_FILENAME, $inFilename);
	}
	
	/**
	 * Returns the output format, default jpeg
	 * 
	 * @return string
	 */
	function getOutputFormat() {
		return strtolower($this->getOption(self::OPTION_OUTPUT_FORMAT, 'jpeg'));
	}
	
	/**
	 * Set the output format (jpeg, png, gif, tiff, etc)
	 *  
	 * @param string $inFormat
	 * @return imageConvertor
	 */
	function setOutputFormat($inFormat) {
		return $this->setOption(self::OPTION_OUTPUT_FORMAT, $inFormat);
	}
	
	/**
	 * Returns the output image height, default 150
	 * 
	 * @return integer
	 */
	function getOutputHeight() {
		return $this->getOption(self::OPTION_OUTPUT_HEIGHT, 150);
	}

	/**
	 * Set the output image height
	 *  
	 * @param integer $inHeight
	 * @return imageConvertor
	 */
	function setOutputHeight($inHeight) {
		return $this->setOption(self::OPTION_OUTPUT_HEIGHT, $inHeight);
	}
	
	/**
	 * Returns the output image width, default 150
	 * 
	 * @return integer
	 */
	function getOutputWidth() {
		return $this->getOption(self::OPTION_OUTPUT_WIDTH, 150);
	}
	
	/**
	 * Set the output image width
	 *  
	 * @param integer $inWidth
	 * @return imageConvertor
	 */
	function setOutputWidth($inWidth) {
		return $this->setOption(self::OPTION_OUTPUT_WIDTH, $inWidth);
	}
	
	/**
	 * Returns true if output should overwrite files (if writing to file system)
	 * 
	 * @return boolean
	 */
	function getOutputOverwriteFiles() {
		return $this->getOption(self::OPTION_OUTPUT_OVERWRITE_FILES, false);
	}

	/**
	 * Set whether files should be overwritten or not
	 *  
	 * @param boolean $inFlag
	 * @return imageConvertor
	 */
	function setOutputOverwriteFiles($inFlag) {
		return $this->setOption(self::OPTION_OUTPUT_OVERWRITE_FILES, $inFlag);
	}
	
	/**
	 * Returns the padding colour, default 'white'
	 * 
	 * @return string
	 */
	function getOutputPadColour() {
		return $this->getOption(self::OPTION_OUTPUT_PAD_COLOUR, 'white');
	}
	
	/**
	 * Set the output padding background colour
	 *  
	 * @param string $inColour
	 * @return imageConvertor
	 */
	function setOutputPadColour($inColour) {
		return $this->setOption(self::OPTION_OUTPUT_PAD_COLOUR, $inColour);
	}
	
	/**
	 * Should images be padded to the specified width and height, default true
	 * 
	 * @return boolean
	 */
	function getOutputPadImage() {
		return $this->getOption(self::OPTION_OUTPUT_PAD_IMAGE, true);
	}
	
	/**
	 * Set whether images should be padded to the set width and height
	 *  
	 * @param boolean $inFlag
	 * @return imageConvertor
	 */
	function setOutputPadImage($inFlag) {
		return $this->setOption(self::OPTION_OUTPUT_PAD_IMAGE, $inFlag);
	}
	
	/**
	 * Output image compression level (quality) to apply to JPEGs, default 90 (i.e. high quality)
	 * 
	 * @return integer
	 */
	function getOutputQuality() {
		return $this->getOption(self::OPTION_OUTPUT_QUALITY, 90);
	}

	/**
	 * Set the output image quality
	 *  
	 * @param integer $inQuality
	 * @return imageConvertor
	 */
	function setOutputQuality($inQuality) {
		return $this->setOption(self::OPTION_OUTPUT_QUALITY, $inQuality);
	}
}