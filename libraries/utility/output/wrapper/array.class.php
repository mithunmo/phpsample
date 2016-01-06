<?php
/**
 * utilityOutputWrapperArray Class
 * 
 * Stored in utilityOutputWrapperArray.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityOutputWrapperArray
 * @version $Rev: 706 $
 */


/**
 * utilityOutputWrapperArray
 *
 * Wraps array elements as they are accessed. This class is used by
 * {@link utilityOutputWrapper}. You should not need to use it outside
 * of the output wrapper system.
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityOutputWrapperArray
 */
class utilityOutputWrapperArray implements Iterator {
	
	/**
	 * Holds array data
	 *
	 * @var array
	 */
    private $_Var = array();

    
    
    /**
     * Returns new utilityOutputWrapperArray
     *
     * @param array $array
     */
    public function __construct($array) {
        if (is_array($array)) {
            $this->_Var = $array;
        }
    }

    /**
     * Returns the original iterator that was wrapped
     *
     * @return array
     */
    public function getSeed() {
    	return $this->_Var;
    }

    /**
     * Resets array pointer to the start of the array
     * 
     * @return void
     */
    public function rewind() {
        reset($this->_Var);
    }

    /**
     * Returns current array item
     *
     * @return mixed
     */
    public function current() {
        $var = current($this->_Var);
        return utilityOutputWrapper::wrap($var);
    }

    /**
     * Returns current array key
     *
     * @return mixed
     */
    public function key() {
        $var = key($this->_Var);
        return utilityOutputWrapper::wrap($var);
    }

    /**
     * Returns next item in array
     *
     * @return mixed
     */
    public function next() {
        $var = next($this->_Var);
        return utilityOutputWrapper::wrap($var);
    }

    /**
     * Returns true if current item is valid
     *
     * @return boolean
     */
    public function valid() {
        $var = $this->current() !== false;
        return utilityOutputWrapper::wrap($var);
    }
}