<?php
/**
 * utilityOutputWrapperIterator Class
 * 
 * Stored in utilityOutputWrapperIterator.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityOutputWrapperIterator
 * @version $Rev: 706 $
 */


/**
 * utilityOutputWrapperIterator
 *
 * Wraps Iterator object values as they are accessed. This class is used
 * by {@link utilityOutputWrapper}. You should not need to use it outside
 * of the output wrapper system.
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityOutputWrapperIterator
 */
class utilityOutputWrapperIterator implements Iterator {
	
	/**
	 * Holds an iterator array
	 *
	 * @var Iterator
	 */
    private $_Var;

    
    
    /**
     * Returns new utilityOutputWrapperIterator
     *
     * @param Iterator $array
     */
    public function __construct(Iterator $array) {
		$this->_Var = $array;
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
     * Rewinds array to begining
     *
     * @return void
     */
	public function rewind() {
        $this->_Var->rewind();
    }

    /**
     * Returns current item
     *
     * @return mixed
     */
    public function current() {
         return utilityOutputWrapper::wrap($this->_Var->current());
    }

    /**
     * Returns current key
     *
     * @return mixed
     */
    public function key() {
        return utilityOutputWrapper::wrap($this->_Var->key());
    }

    /**
     * Returns next item from iterator
     *
     * @return mixed
     */
    public function next() {
         return utilityOutputWrapper::wrap($this->_Var->next());
    }

    /**
     * Returns true if iterator is valid
     *
     * @return boolean
     */
    public function valid() {
        return utilityOutputWrapper::wrap($this->_Var->valid());
    }
}