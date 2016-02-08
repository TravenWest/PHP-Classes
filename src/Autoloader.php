<?php


/**
 *   ----------------------------------------------------------------------------------------------
 *   Class Information
 *   ----------------------------------------------------------------------------------------------
 *   Project:  QuadFramework 
 *   Package:  Autoloader.php
 *   Authors:  Traven West <me@travenwest.com>
 *   Version:  1.0.4
 *   License:  BSD Clause 3 "New or Revised" License
 *   ----------------------------------------------------------------------------------------------
 *   Class Description
 *   ----------------------------------------------------------------------------------------------
 *   This is the base autoloader class. This class must be the first to be called when running the
 *   web application. This class also sets up our environment as needed. The application/ registry
 *   depends on it for loading all other classes.
 *   ----------------------------------------------------------------------------------------------
 */

 
class QuadFramework_Autoloader
{
    
    private static $_instance;
    
    
    protected $_rootDir = '.';
    
    
    protected $_setup = false;
    
    
    protected function __construct() {}
    
    
    public function setupAutoloader($rootDir)
	{
		if ($this->_setup)
		{
			return;
		}

		$this->_rootDir = $rootDir;
		$this->_setupAutoloader();

		$this->_setup = true;
	}
    
    
    protected function _setupAutoloader()
	{
		if (@ini_get('open_basedir'))
		{
			//  A lot of servers don't seem to set include_path correctly
			set_include_path($this->_rootDir . PATH_SEPARATOR . '.');
		}
        
		else
		{
			set_include_path($this->_rootDir . PATH_SEPARATOR . '.' . PATH_SEPARATOR . get_include_path());
		}

		//  require_once('Zend/Loader/Autoloader.php');
        //  require 'Zend/Loeader/Autoloader.php';
		//  $autoloader = Zend_Loader_Autoloader::getInstance();
        //  $autoloader = (Zend_Loader_Autoloader, 'autoloader');
		//  $autoloader->pushAutoloader(array($this, 'autoload'));
        //  $autoloader->phalconPHP();
        //  $autoloader->this->phalconPHP();
        
		spl_autoload_register(array($this, 'autoload'));
	}
    
    
    public function autoload($class)
	{
		if (class_exists($class, false) || interface_exists($class, false))
		{
			return true;
		}

		$filename = $this->autoloaderClassToFile($class);
		
        if (!$filename)
		{
			return false;
		}

		if (file_exists($filename))
		{
			include($filename);
			return (class_exists($class, false) || interface_exists($class, false));
		}

		return false;
	}
    
    
    public function autoloaderClassToFile($class)
	{
		if (preg_match('#[^a-zA-Z0-9_]#', $class))
		{
			return false;
		}

		return $this->_rootDir . '/' . str_replace('_', '/', $class) . '.php';
	}
    
    
    public function getRootDir()
	{
		return $this->_rootDir;
	}
    
    
    public static final function getInstance()
	{
		if (!self::$_instance)
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}
    
    
    public static function setInstance(QuadFramework_Autoloader $loader = null)
	{
		self::$_instance = $loader;
	}
}