<?php
/**
 * systemEvent
 *
 * Stored in event.class.php
 * 
 * Based on the sfEvent component of symfony-components.
 * 
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *  
 * Copyright (c) 2008-2009 Fabien Potencier
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @ package    symfony
 * @ subpackage event_dispatcher
 * @ author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @ version    SVN: $Id: sfEvent.class.php 8698 2008-04-30 16:35:28Z fabien $
 *
 * @package scorpio
 * @subpackage system
 * @category systemEvent
 * @version $Rev: 736 $
 */


/**
 * systemEvent
 *
 * An event that is raised and sent to the event dispatcher.
 *
 * @package scorpio
 * @subpackage system
 * @category systemEvent
 */
class systemEvent implements ArrayAccess {
	
	/**
	 * The return value for this event
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $value = null;
	
	/**
	 * Has the event been completely processed?
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $processed = false;
	
	/**
	 * The object that triggered the event
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $subject = null;
	
	/**
	 * Name of the event
	 * 
	 * @var string
	 * @access protected
	 */
	protected $name = '';
	
	/**
	 * Parameters passed to this event
	 * 
	 * @var array
	 * @access protected
	 */
	protected $parameters = null;
	
	
	
	/**
	 * Constructs a new systemEvent.
	 *
	 * @param mixed   $subject      The subject
	 * @param string  $name         The event name
	 * @param array   $parameters   An array of parameters
	 */
	public function __construct($subject, $name, $parameters = array()) {
		$this->subject = $subject;
		$this->name = $name;
		$this->parameters = $parameters;
	}
	
	
	
	/**
	 * Returns the subject.
	 *
	 * @return mixed The subject
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * Returns the event name.
	 *
	 * @return string The event name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the return value for this event.
	 *
	 * @param mixed $value The return value
	 * @return systemEvent
	 */
	public function setReturnValue($value) {
		if ( $value !== $this->value ) {
			$this->value = $value;
		}
		return $this;
	}

	/**
	 * Returns the return value.
	 *
	 * @return mixed The return value
	 */
	public function getReturnValue() {
		return $this->value;
	}

	/**
	 * Sets the processed flag.
	 *
	 * @param Boolean $processed The processed flag value
	 * @return systemEvent
	 */
	public function setProcessed($processed) {
		$this->processed = (boolean) $processed;
		return $this;
	}

	/**
	 * Returns whether the event has been processed by a listener or not.
	 *
	 * @return Boolean true if the event has been processed, false otherwise
	 */
	public function isProcessed() {
		return $this->processed;
	}

	/**
	 * Returns the event parameters.
	 *
	 * @return array The event parameters
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * Returns true if the parameter exists (implements the ArrayAccess interface).
	 *
	 * @param  string  $name  The parameter name
	 * @return Boolean true if the parameter exists, false otherwise
	 */
	public function offsetExists($name) {
		return array_key_exists($name, $this->parameters);
	}

	/**
	 * Returns a parameter value (implements the ArrayAccess interface).
	 *
	 * @param  string  $name  The parameter name
	 * @return mixed  The parameter value
	 * @throws InvalidArgumentException
	 */
	public function offsetGet($name) {
		if ( !array_key_exists($name, $this->parameters) ) {
			throw new InvalidArgumentException(sprintf('The event "%s" has no "%s" parameter.', $this->name, $name));
		}
		
		return $this->parameters[$name];
	}

	/**
	 * Sets a parameter (implements the ArrayAccess interface).
	 *
	 * @param string  $name   The parameter name
	 * @param mixed   $value  The parameter value 
	 */
	public function offsetSet($name, $value) {
		$this->parameters[$name] = $value;
	}

	/**
	 * Removes a parameter (implements the ArrayAccess interface).
	 *
	 * @param string $name    The parameter name
	 */
	public function offsetUnset($name) {
		unset($this->parameters[$name]);
	}
}