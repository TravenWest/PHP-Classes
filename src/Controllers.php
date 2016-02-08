<?php

/**
 *   ----------------------------------------------------------------------------------------------
 *   Class Information
 *   ----------------------------------------------------------------------------------------------
 *   Project:  QuadFramework 
 *   Package:  Controllers.php
 *   Authors:  Traven West <me@travenwest.com>
 *   Version:  1.0.4
 *   License:  BSD Clause 3 "New or Revised" License
 *   ----------------------------------------------------------------------------------------------
 *   Class Description
 *   ----------------------------------------------------------------------------------------------
 *   This is the abstract base class for controllers. The controllers should implement methods 
 *   named actionX with no arguments. These will be called by the dispatcher based on the requested
 *   route. They should return the object returned by {responseReroute()}, {@responseError()}, or 
 *   {responseView()},. All responses can take parameters that will be passed to the container view
 *   (E.g. two-phase view), if there is one.
 *   ----------------------------------------------------------------------------------------------
 */

 
 abstract class QuadFramework_Controller
{
    
    protected $_request;
    
    
    protected $_response;
    
    
    protected $_routeMatch;
    
    
    protected $_input;
    
    
    protected static $_executed = array();
    
    
    public function __construct(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
	{
		$this->_request = $request;
		$this->_response = $response;
	}
    
    
    public function getRequest()
	{
		return $this->_request;
	}
    
    
    public function getInput()
	{
		return $this->_input;
	}
    
    
    public function getResponseType()
	{
		return $this->_routeMatch->getResponseType();
	}
    
    
    public function getRouteMatch()
	{
		return $this->_routeMatch;
	}
    
    
    public function responseReroute($controllerName, $action, array $containerParams = array())
	{
		$controllerResponse->controllerName = $controllerName;
		$controllerResponse->action = $action;
		$controllerResponse->containerParams = $containerParams;

		return $controllerResponse;
	}
    
    
    public function responseError($error, $responseCode = 200, array $containerParams = array())
	{
		$controllerResponse->errorText = $error;
		$controllerResponse->responseCode = $responseCode;
		$controllerResponse->containerParams = $containerParams;

		return $controllerResponse;
	}
    
    
	public function responseMessage($message, array $containerParams = array())
	{
		$controllerResponse->message = $message;
		$controllerResponse->containerParams = $containerParams;

		return $controllerResponse;
	}
    
    
	public function responseView($viewName, $templateName = '', array $params = array(), array $containerParams = array())
	{
		$controllerResponse->viewName = $viewName;
		$controllerResponse->templateName = $templateName;
		$controllerResponse->params = $params;
		$controllerResponse->containerParams = $containerParams;

		return $controllerResponse;
	}
		if ($inputString = $this->_input->filterSingle($varname, QuadFramework_Input::STRING))
		{
			try
			{
				return new QuadFramework_Input(QuadFramework_Application::parseQueryString($inputString));
			}
			catch (Exception $e)
			{
				$errorPhraseKey = 'string_could_not_be_converted_to_input';

				if ($throw)
				{
					throw $this->responseException(
						$this->responseError(new QuadFramework_Phrase($errorPhraseKey))
					);
				}
			}
		}

		return false;
	}

		if (!is_array($checkIps))
		{
			$checkIps = array($checkIps);
		}

		foreach ($checkIps AS $ip)
		{
			$ipClassABlock = intval($ip);
			$long = sprintf('%u', ip2long($ip));

			if (isset($ipList[$ipClassABlock]))
			{
				foreach ($ipList[$ipClassABlock] AS $range)
				{
					if ($long >= $range[0] && $long <= $range[1])
					{
						return true;
					}
				}
			}
		}

		return false;
	
}