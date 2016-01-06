<?php
/**
 * utilityXmlFunction Class
 * 
 * Stored in xmlFunction.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityXmlFunction
 * @version $Rev: 706 $
 */


/**
 * utilityXmlFunction Class
 * 
 * Provides some utility methods for fetching values from XML objects. These
 * are designed to be used with SimpleXMLElement objects and are generally
 * used when dealing with parsing large XML documents that may contain
 * non-standard strings etc. The individual methods contain additional
 * information and notes.
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityXmlFunction
 */
class utilityXmlFunction {
	
	/**
	 * Returns the value from $inElement, or $inDefault if there is nothing in the element
	 *
	 * @param SimpleXMLElement $inXML
	 * @param string $inElement Element name to check
	 * @param mixed $inDefault (optional) Default value if element not present
	 * @return mixed
	 * @static
	 */
	static function getValue(SimpleXMLElement $inXML, $inElement, $inDefault = null) {
		$return = '';
		if ( isset($inXML->$inElement) ) {
			if ( count($inXML->$inElement) > 1 ) {
				systemLog::notice("Multiple values for ($inElement) using default ($inDefault)");
			} else {
				$return = trim((string) $inXML->$inElement);
			}
		}
		if ( strlen($return) == 0 ) {
			return $inDefault;
		} else {
			return $return;
		}
	}
	
	/**
	 * Returns an attribute value from $inXML, or $inDefault if the attribute is not set.
	 * 
	 * This function will first check if the XML object has the attribute, and if not
	 * will call ->attributes() and then check the return state.
	 *
	 * @param SimpleXMLElement $inXML
	 * @param string $inAttribute Attribute name
	 * @param mixed $inDefault (optional) Default value if attribute not present
	 * @return mixed
	 * @static
	 */
	static function getAttribute(SimpleXMLElement $inXML, $inAttribute, $inDefault = null) {
		$return = '';
		if ( isset($inXML[$inAttribute]) ) {
			$return = trim((string) $inXML[$inAttribute]);
		} else {
			$xml = $inXML->attributes();
			if ( isset($xml[$inAttribute]) ) {
				$return = trim((string) $xml[$inAttribute]);
			}
		}
		
		if ( strlen($return) == 0 ) {
			return $inDefault;
		} else {
			return $return;
		}
	}
	
	/**
	 * Returns a boolean value from $inXML, or $inDefault if match not found
	 *
	 * @param SimpleXMLElement $inXML
	 * @param string $inElement
	 * @param mixed $inDesiredValue
	 * @param mixed $inDefault
	 * @return boolean
	 * @static
	 */
	static function getBooleanValue(SimpleXMLElement $inXML, $inElement, $inDesiredValue = null, $inDefault = false) {
		if ( !isset($inXML->$inElement) ) {
			return $inDefault;
		}
		$value = self::getValue($inXML, $inElement);
		if ( $value !== null ) {
			if ( $inDesiredValue !== null ) {
				return (bool) ($value == $inDesiredValue);
			} else {
				return true;
			}
		} else {
			return $inDefault;
		}
	}
	
	/**
	 * Returns an array from the XML element, handles array elements and separated strings via $inSeparators
	 * 
	 * For example a list of artists is separated by the / character, this method will return
	 * an array with each artist as an entry (including duplicates). Alternative if $inXML->artists
	 * contains multiple elements, this will be iterated to create the same array.
	 * 
	 * This method always returns an array
	 *
	 * @param SimpleXMLElement $inXML
	 * @param string $inElement Element name to process
	 * @param array $inSeparators An array of characters used as delimiters, default ,;_/\|
	 * @return array
	 * @static 
	 */
	static function createArrayFromXmlElement(SimpleXMLElement $inXML, $inElement, $inSeparators = array(',',';','_','/','\\','|')) {
		$return = array();
		if ( isset($inXML->$inElement) ) {
			if ( count($inXML->$inElement) > 1 ) {
				$cnt = count($inXML->$inElement);
				for ( $i=0; $i<$cnt; $i++ ) {
					$return[] = trim((string) $inXML->$inElement[$i]);
				}
			} else {
				$string = self::getValue($inXML, $inElement);
				if ( $string !== null ) {
					$return = explode('/', str_replace($inSeparators, '/', $string));
				}
			}
			if ( count($return) > 0 ) {
				array_walk($return, 'utilityStringFunction::trim');
			}
		}
		return $return;
	}
}