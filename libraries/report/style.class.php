<?php
/**
 * reportStyle
 * 
 * Stored in reportStyle.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportStyle
 * @version $Rev: 707 $
 */


/**
 * reportStyle
 * 
 * The reportStyle contains the formatting rules for things like colours,
 * fonts and font sizes for the reports. Not all of these settings affect
 * all output types.
 * 
 * Colours should be specified in HTML hexadecimal format e.g. 6699cc.
 * This will be converted into either an RGB palette or the format required
 * for the report.
 * 
 * The report style is split into several sections:
 * 
 * <ul>
 *   <li>Page - the page size and margins (PDF)</li>
 *   <li>Title - the report title</li>
 *   <li>Description - the report description and range</li>
 *   <li>Heading - the data headings</li>
 *   <li>Data - the data entries</li>
 * </ul>
 * 
 * Each part (except 'page') has the same components. A default style is
 * included and will be used where supported in the writers. You will very
 * likely want to customise the colours, fonts etc for your reports.
 * 
 * Styles can be set either a section at a time, or by setting just a single
 * attribute. Both methods support method chaining.
 * 
 * <code>
 * // setting up a basic title style that is black and ~16pt
 * $oStyle = new reportStyle();
 * $oStyle->setStyles(
 *     reportStyle::SECTION_TILE,
 *     array(
 *         reportStyle::STYLE_ATTRIBUTE_COLOUR => '000000',
 *         reportStyle::STYLE_ATTRIBUTE_FONT_SIZE => '16',
 *     )
 * );
 * 
 * // the above could be done via the following
 * $oStyle
 *     ->setStyleAttribute(reportStyle::SECTION_TITLE, reportStyle::STYLE_ATTRIBUTE_COLOUR, '000000')
 *     ->setStyleAttribute(reportStyle::SECTION_TITLE, reportStyle::STYLE_ATTRIBUTE_FONT_SIZE, '16');
 * </code>
 * 
 * @package scorpio
 * @subpackage report
 * @category reportStyle
 */
class reportStyle {
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * Stores the style information
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_Styles;
	
	const SECTION_PAGE = 'page';
	const SECTION_TITLE = 'title';
	const SECTION_DESC = 'desc';
	const SECTION_HEADING = 'heading';
	const SECTION_DATA = 'data';
	
	/**
	 * Page orientation either L - landscape or P - portrait
	 * 
	 * @var string
	 */
	const STYLE_ATTRIBUTE_PAGE_ORIENTATION = 'attr.pageOrientation';
	/**
	 * The page size, one of: a3, a4, a5, letter or legal
	 * 
	 * @var string
	 */
	const STYLE_ATTRIBUTE_PAGE_SIZE = 'attr.pageSize';
	/**
	 * Page margin size in cms, single value used for all margins
	 * 
	 * @var integer
	 */
	const STYLE_ATTRIBUTE_PAGE_MARGIN = 'attr.pageMargin';
	
	/**
	 * Colours, always specified in HTML hex format e.g. aabbcc
	 * 
	 * Notes:
	 * colour is for the font colour
	 * background is used in XLS, PDF and HTML for the background
	 * 
	 * @var string
	 */
	const STYLE_ATTRIBUTE_COLOUR = 'attr.colour';
	const STYLE_ATTRIBUTE_BACKGROUND_COLOUR = 'attr.backgroundColour';
	/**
	 * Alternative background colour, used for zebra-striping the data
	 * 
	 * @var string
	 */
	const STYLE_ATTRIBUTE_ALT_BACKGROUND_COLOUR = 'attr.altBackgroundColour';
	
	/**
	 * Font family, applies to HTML, XLS and PDF can be any supported font
	 * 
	 * Examples:
	 * Courier, Courier-Bold, Courier-Oblique, Courier-BoldOblique, Helvetica, Helvetica-Bold,
	 * Helvetica-Oblique, Helvetica-BoldOblique, Times-Roman, Times-Bold, Times-Italic,
	 * Times-BoldItalic, Symbol, ZapfDingbats
	 * 
	 * Font size is measured in points in all cases. Do not provide units.
	 *
	 * @var string
	 */
	const STYLE_ATTRIBUTE_FONT = 'attr.font';
	/**
	 * Toggle, true for bold, false not
	 * 
	 * @var boolean
	 */
	const STYLE_ATTRIBUTE_FONT_BOLD = 'attr.fontBold';
	/**
	 * Toggle, true for italic, false not
	 * 
	 * @var boolean
	 */
	const STYLE_ATTRIBUTE_FONT_ITALIC = 'attr.fontItalic';
	/**
	 * Toggle, true for underline, false not
	 * 
	 * @var boolean
	 */
	const STYLE_ATTRIBUTE_FONT_UNDERLINE = 'attr.fontUnderline';
	/**
	 * Font size, specified in pts - the normal size as used in Excel, Word etc.
	 * 
	 * Note: no units are necessary, just the integer value between ~8 and 20.
	 * 
	 * @var integer
	 */
	const STYLE_ATTRIBUTE_FONT_SIZE = 'attr.fontSize';
	
	/**
	 * Border style to apply, T - top, B - bottom, L - left, R - right, A - all
	 * 
	 * Border attributes can be stacked, so for bottom right, use BR etc.
	 * 
	 * @var string
	 */
	const STYLE_ATTRIBUTE_BORDER = 'attr.border';
	/**
	 * Border size to use, whole integer e.g. 1, 2 etc.
	 * 
	 * In HTML this is the border width in pixels, in XLS this is the Excel
	 * width. PDF only has a single width so this value is ignored.
	 * 
	 * @var integer
	 */
	const STYLE_ATTRIBUTE_BORDER_SIZE = 'attr.borderSize';
	/**
	 * Border colour, specified as a HTML hex code
	 * 
	 * @var string
	 */
	const STYLE_ATTRIBUTE_BORDER_COLOUR = 'attr.borderColour';
	
	
	
	/**
	 * Creates a new report style
	 * 
	 * @return reportStyle
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Resets the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_Styles = array(
			self::SECTION_PAGE => array(
				self::STYLE_ATTRIBUTE_PAGE_MARGIN => '1',
				self::STYLE_ATTRIBUTE_PAGE_ORIENTATION => 'L',
				self::STYLE_ATTRIBUTE_PAGE_SIZE => 'letter',
			),
			self::SECTION_TITLE => array(
				self::STYLE_ATTRIBUTE_BACKGROUND_COLOUR => 'ffffff',
				self::STYLE_ATTRIBUTE_BORDER => '',
				self::STYLE_ATTRIBUTE_BORDER_COLOUR => '',
				self::STYLE_ATTRIBUTE_BORDER_SIZE => 0,
				self::STYLE_ATTRIBUTE_COLOUR => '000000',
				self::STYLE_ATTRIBUTE_FONT => 'Arial',
				self::STYLE_ATTRIBUTE_FONT_BOLD => true,
				self::STYLE_ATTRIBUTE_FONT_ITALIC => false,
				self::STYLE_ATTRIBUTE_FONT_SIZE => 16,
				self::STYLE_ATTRIBUTE_FONT_UNDERLINE => false,
			),
			self::SECTION_DESC => array(
				self::STYLE_ATTRIBUTE_BACKGROUND_COLOUR => 'ffffff',
				self::STYLE_ATTRIBUTE_BORDER => '',
				self::STYLE_ATTRIBUTE_BORDER_COLOUR => '',
				self::STYLE_ATTRIBUTE_BORDER_SIZE => 0,
				self::STYLE_ATTRIBUTE_COLOUR => '999999',
				self::STYLE_ATTRIBUTE_FONT => 'Arial',
				self::STYLE_ATTRIBUTE_FONT_BOLD => false,
				self::STYLE_ATTRIBUTE_FONT_ITALIC => false,
				self::STYLE_ATTRIBUTE_FONT_SIZE => 10,
				self::STYLE_ATTRIBUTE_FONT_UNDERLINE => false,
			),
			self::SECTION_HEADING => array(
				self::STYLE_ATTRIBUTE_BACKGROUND_COLOUR => '6699cc',
				self::STYLE_ATTRIBUTE_BORDER => 'B',
				self::STYLE_ATTRIBUTE_BORDER_COLOUR => '000000',
				self::STYLE_ATTRIBUTE_BORDER_SIZE => 2,
				self::STYLE_ATTRIBUTE_COLOUR => 'ffffff',
				self::STYLE_ATTRIBUTE_FONT => 'Arial',
				self::STYLE_ATTRIBUTE_FONT_BOLD => true,
				self::STYLE_ATTRIBUTE_FONT_ITALIC => false,
				self::STYLE_ATTRIBUTE_FONT_SIZE => 10,
				self::STYLE_ATTRIBUTE_FONT_UNDERLINE => false,
			),
			self::SECTION_DATA => array(
				self::STYLE_ATTRIBUTE_ALT_BACKGROUND_COLOUR => 'cdcdcd',
				self::STYLE_ATTRIBUTE_BACKGROUND_COLOUR => 'ffffff',
				self::STYLE_ATTRIBUTE_BORDER => 'B',
				self::STYLE_ATTRIBUTE_BORDER_COLOUR => 'cdcdcd',
				self::STYLE_ATTRIBUTE_BORDER_SIZE => 1,
				self::STYLE_ATTRIBUTE_COLOUR => '000000',
				self::STYLE_ATTRIBUTE_FONT => 'Arial',
				self::STYLE_ATTRIBUTE_FONT_BOLD => false,
				self::STYLE_ATTRIBUTE_FONT_ITALIC => false,
				self::STYLE_ATTRIBUTE_FONT_SIZE => 10,
				self::STYLE_ATTRIBUTE_FONT_UNDERLINE => false,
			),
		);
		$this->_Modified = false;
	}
	
	
	
	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set $_Modified to $inStatus
	 *
	 * @param boolean $inStatus
	 * @return reportStyle
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}
	
	/**
	 * Returns an array of just the sections with defined styles
	 * 
	 * @return array
	 */
	function getStyleSections() {
		return array_keys($this->_Styles);
	}
	
	/**
	 * Returns the styles for the specified section, or if null, all styles
	 * 
	 * @param string $inSection
	 * @return array
	 */
	function getStyles($inSection = null) {
		if ( $inSection === null ) {
			return $this->_Styles;
		}
		if ( array_key_exists($inSection, $this->_Styles) ) {
			return $this->_Styles[$inSection];
		} else {
			return array();
		}
	}
	
	/**
	 * Set the styles for a section
	 * 
	 * @param string $inSection
	 * @param array $inStyles
	 * @return reportStyle
	 */
	function setStyles($inSection, array $inStyles = array()) {
		$sections = array(self::SECTION_PAGE, self::SECTION_TITLE, self::SECTION_DESC, self::SECTION_HEADING, self::SECTION_DATA);
		if ( !$inSection || !in_array($inSection, $sections) ) {
			throw new reportException('Missing or invalid style section, must be one of '.implode(', ', $sections));
		}
		
		if ( count($inStyles) > 0 ) {
			foreach ( $inStyles as $style => $value ) {
				$this->_Styles[$inSection][$style] = $value;
			}
		}
		return $this;
	}
	
	/**
	 * Returns the value for the style, or $inDefault if not set
	 * 
	 * @param string $inSection
	 * @param string $inStyleAttribute
	 * @param mixed $inDefault
	 * @return mixed
	 */
	function getStyleAttribute($inSection, $inStyleAttribute, $inDefault = null) {
		if ( isset($this->_Styles[$inSection][$inStyleAttribute]) ) {
			return $this->_Styles[$inSection][$inStyleAttribute];
		}
		return $inDefault;
	}
	
	/**
	 * Sets the style attribute for the section
	 * 
	 * @param string $inSection
	 * @param string $inStyleAttribute
	 * @param mixed $inValue
	 * @return reportstyle
	 */
	function setStyleAttribute($inSection, $inStyleAttribute, $inValue) {
		$this->_Styles[$inSection][$inStyleAttribute] = $inValue;
		return $this;
	}
	
	/**
	 * Returns the style colour attribute as an RGB array
	 * 
	 * @param string $inSection
	 * @param string $inStyleAttribute
	 * @param string $inDefault
	 * @return array[r, g, b]
	 */
	function getStyleColourAsRgb($inSection, $inStyleAttribute, $inDefault) {
		return $this->convertHexToRgb(
			$this->getStyleAttribute($inSection, $inStyleAttribute, $inDefault)
		);
	}
	
	/**
	 * Converts the hex colour code to RGB, returning an array
	 * 
	 * @param string $inHex
	 * @return array[r, g, b]
	 */
	function convertHexToRgb($inHex) {
		$hex = str_replace('#', '', $inHex);
		$colour = array('r' => 0, 'g' => 0, 'b' => 0);
		if ( strlen($hex) == 3 ) {
			$colour['r'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
			$colour['g'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
			$colour['b'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
		} elseif ( strlen($hex) == 6 ) {
			$colour['r'] = hexdec(substr($hex, 0, 2));
			$colour['g'] = hexdec(substr($hex, 2, 2));
			$colour['b'] = hexdec(substr($hex, 4, 2));
		}
		return $colour;
	}
}