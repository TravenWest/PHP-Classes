<?php

/**
 *   ----------------------------------------------------------------------------------------------
 *   Class Information
 *   ----------------------------------------------------------------------------------------------
 *   Project:  QuadFramework
 *   Package:  Exceptions.php
 *   Authors:  Traven West <me@travenwest.com>
 *   Version:  1.0.4
 *   License:  BSD License "New or Revised" License
 *   ----------------------------------------------------------------------------------------------
 *   Class Description
 *   ----------------------------------------------------------------------------------------------
 *   This class is for exception handling. It has support for throwing error that are specifically
 *   targeted at members, and even capable at throwing multiple messages together in one single
 *   exception (this latter behavior mentioned is primarily used as a convienence). 
 *   ----------------------------------------------------------------------------------------------
 */
 
class QuadFramework_Exception extends Exception
{
    
	protected $_userPrintable = false;
	protected $_messages = null;
    
    
	public function __construct($message, $userPrintable = false)
	{
		$this->_userPrintable = (boolean)$userPrintable;

		if (is_array($message) && count($message) > 0)
		{
			$this->_messages = $message;
			$message = reset($message);
		}

		parent::__construct($message);
	}
    
	
	public function isUserPrintable()
	{
		return $this->_userPrintable;
	}
    
	
	public function getMessages()
	{
		if (is_array($this->_messages))
		{
			return $this->_messages;
		}
		else
		{
			return $this->getMessage();
		}
	}
}