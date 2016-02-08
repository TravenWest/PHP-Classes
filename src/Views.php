<?php

/**
 *   ----------------------------------------------------------------------------------------------
 *   Class Information
 *   ----------------------------------------------------------------------------------------------
 *   Project:  QuadFramework 
 *   Package:  Views.php
 *   Authors:  Traven West <me@travenwest.com>
 *   Version:  1.0.4
 *   License:  BSD Clause 3 "New or Revised" License
 *   ----------------------------------------------------------------------------------------------
 *   Class Description
 *   ----------------------------------------------------------------------------------------------
 *   This is the abstract class for Views. A child of this class is not required if you only want
 *   to render HTML template without anymore processing. I'll give an idea of how to use this class
 *   when creating children classes.
 *   
 *   Views must implement the renderX methods, where X represents the response type they handle (eg
 *   renderHTML and renderJSON). These methods should take no arguements and should return a string
 *   if they successfully rendered content, or even false if the content is really unrepresentable.
 *   ----------------------------------------------------------------------------------------------
 */

abstract class QuadFramework_View
{

	protected $_renderer;
    
    
	protected $_response;
    
    
    protected $_templateName;
    
    
    protected $_params = array();
    
    
    public function __construct()
	{
		$this->_renderer     = $renderer;
		$this->_response     = $response;
		$this->_templateName = $templateName;

		if ($params)
		{
			$this->setParams($params);
		}
	}
    
    
    public function setParameters(array $params)
	{
		$this->_params = array_merge($this->_params, $params);
	}
    
    
    public function getParameters()
	{
		return $this->_params;
	}
    
    
    public function getTemplateName()
	{
		return $this->_templateName;
	}
    
    public function prepareParams() {}
    
}