<?php

/**
 *   This is the base application class for a web application. We are going
 *   to be setting up the environment as necessary and this class acts like
 *   the registry for us. It can also be a broker for the Autoloader.
 *   
 *   Project:  QuadFramework
 *   Package:  Application.php
 *   Authors:  Traven West <me@travenwest.com>
 *   Version:  1.0.1
 */
 
 
class QuadFramework_Application extends Zend_Registry
{
	
	const URL_ID_DELIMITER = '.';
    
    
    public static $version   = '1.0.4';
	public static $versionID = 01000407; // AABBCCDD

    
    public static $jQueryVersion = '1.4.4';
    
    
    protected $_rootDir = '.';
    
    
    protected $_initialized = false;
    
    
    protected static $_handlePhpError = true;
    
    
    protected static $_debug;
    
    
    protected static $_classCache = array();
    
    
    public static $time = 0;
    
    
    public static $host = 'localhost';
    
    
    public static $secure = false;
    
    
    public static $integerSentinel = '{{sentinel}}';
    
    
    public static $externalDataPath = 'data';
    
    
    public static $externalDataUrl = 'data';
    
    
    public static $javaScriptUrl = 'js';
    
    
	public function beginApplication($configDir = '.', $rootDir = '.', $loadDefaultData = true)
	{
		if ($this->_initialized)
		{
			return;
		}

		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
		{
			self::undoMagicQuotes($_GET);
			self::undoMagicQuotes($_POST);
			self::undoMagicQuotes($_COOKIE);
			self::undoMagicQuotes($_REQUEST);
		}
		if (function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime())
		{
			@set_magic_quotes_runtime(false);
		}

		@ini_set('memory_limit', 128 * 1024 * 1024);
		ignore_user_abort(true);

		@ini_set('output_buffering', false);

		//  See http://bugs.php.net/bug.php?id=36514
		if (!@ini_get('output_handler')) while (@ob_end_clean());

		error_reporting(E_ALL | E_STRICT & ~8192);
		set_error_handler(array('QuadFramework_Application', 'handlePhpError'));
		set_exception_handler(array('QuadFramework_Application', 'handleException'));

		//  @ini_set('pcre.backtrack_limit', 1000000);

		date_default_timezone_set('UTC');

		self::$time = time();

		self::$host = (empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST']);

		self::$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');

		$this->_configDir = $configDir;
		$this->_rootDir = $rootDir;
		$this->addLazyLoader('requestPaths', array($this, 'loadRequestPaths'));

		if ($loadDefaultData)
		{
			$this->loadDefaultData();
		}

		$this->_initialized = true;
	}
    
    
    public static function initialize($configDir = '.', $rootDir = '.', $loadDefaultData = true)
	{
		self::setClassName(__CLASS__);
		self::getInstance()->beginApplication($configDir, $rootDir, $loadDefaultData);
	}
    
    
    public static function handlePhpError($errorType, $errorString, $file, $line)
	{
		if (!self::$_handlePhpError)
		{
			return false;
		}

		if ($errorType & error_reporting())
		{
			throw new ErrorException($errorString, 0, $errorType, $file, $line);
		}
	}
    
    
    public static function disablePhpErrorHandler()
	{
		self::$_handlePhpError = false;
	}
    
    
    public static function enablePhpErrorHandler()
	{
		self::$_handlePhpError = true;
	}
    
    
    public static function handleException(Exception $error)
	{
		QuadFramework_Error::logException($error);
		QuadFramework_Error::unexpectedException($error);
	}
    
    
    public static function debugMode()
	{
		return self::$_debug;
	}
    
    
    public static function setDebugMode($debug)
	{
		self::$_debug = (boolean)$debug;

		if (self::$_debug)
		{
			@ini_set('display_errors', true);
		}
	}
    
    
    public function getConfigDir()
	{
		return $this->_configDir;
	}
    
    
    public function getRootDir()
	{
		return $this->_rootDir;
	}
    
    
    public function loadDb(Zend_Config $dbConfig)
	{
		$db = Zend_Db::factory($dbConfig->adapter,
			array(
				'host' => $dbConfig->host,
				'port' => $dbConfig->port,
				'username' => $dbConfig->username,
				'password' => $dbConfig->password,
				'dbname' => $dbConfig->dbname,
				'charset' => 'utf8'
			)
		);

		switch (get_class($db))
		{
			case 'Zend_Db_Adapter_Mysqli':
				$db->getConnection()->query("SET @@session.sql_mode='STRICT_ALL_TABLES'");
				break;
			case 'Zend_Db_Adapter_Pdo_Mysql':
				$db->getConnection()->exec("SET @@session.sql_mode='STRICT_ALL_TABLES'");
				break;
		}

		if (self::debugMode())
		{
			$db->setProfiler(true);
		}

		return $db;
	}
    
    
    public function loadCache(Zend_Config $cacheConfig)
	{
		if (!$cacheConfig->enabled)
		{
			return false;
		}

		return Zend_Cache::factory(
		    $cacheConfig->frontend,
		    $cacheConfig->backend,
		    $cacheConfig->frontendOptions->toArray(),
		    $cacheConfig->backendOptions->toArray()
		);
	}
    
    
    public function loadRequestPaths()
	{
		return self::getRequestPaths(new Zend_Controller_Request_Http());
	}
    
    
    public static function getRequestPaths(Zend_Controller_Request_Http $request)
	{
		$basePath = $request->getBasePath();
		if ($basePath === '' || substr($basePath, -1) != '/')
		{
			$basePath .= '/';
		}

		$host = $request->getServer('HTTP_HOST');
		if (!$host)
		{
			$host = $request->getServer('SERVER_NAME');
			$serverPort = intval($request->getServer('SERVER_PORT'));
			if ($serverPort && $serverPort != 80 && $serverPort != 443)
			{
				$host .= ':' . $serverPort;
			}
		}

		$protocol = ($request->isSecure() ? 'https' : 'http');

		$requestUri = $request->getRequestUri();

		return array(
			'basePath' => $basePath,
			'host' => $host,
			'protocol' => $protocol,
			'fullBasePath' => $protocol . '://' . $host . $basePath,
			'requestUri' => $requestUri,
			'fullUri' => $protocol . '://' . $host . $requestUri
		);
	}
    
    
    public static function get($index)
	{
		$instance = self::getInstance();

		if (!$instance->offsetExists($index))
		{
            throw new Zend_Exception("No entry is registered for key '$index'");
		}

		return $instance->offsetGet($index);
	}
    
    
    public static function getWithFallback($index, $callback, array $args = array())
	{
		if (self::isRegistered($index))
		{
			return self::get($index);
		}
		else
		{
			$result = call_user_func_array($callback, $args);
			self::set($index, $result);
			return $result;
		}
	}
    
    
    public static function autoload($class)
	{
		return QuadFramework_Autoloader::getInstance()->autoload($class);
	}
    
    
    public static function undoMagicQuotes(&$array, $depth = 0)
	{
		if ($depth > 10 || !is_array($array))
		{
			return;
		}

		foreach ($array AS $key => $value)
		{
			if (is_array($value))
			{
				self::undoMagicQuotes($array[$key], $depth + 1);
			}
			else
			{
				$array[$key] = stripslashes($value);
			}

			if (is_string($key))
			{
				$new_key = stripslashes($key);
				if ($new_key != $key)
				{
					$array[$new_key] = $array[$key];
					unset($array[$key]);
				}
			}
		}
	}
    
    
    public static function gzipContentIfSupported(&$content)
	{
		if (@ini_get('zlib.output_compression') || @ini_get('output_handler'))
		{
			return array();
		}

		if (!function_exists('gzencode') || empty($_SERVER['HTTP_ACCEPT_ENCODING']))
		{
			return array();
		}

		if (!is_string($content))
		{
			return array();
		}

		if (!self::get('config')->enableGzip)
		{
			return array();
		}

		$headers = array();

		if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)
		{
			$headers[] = array('Content-Encoding', 'gzip', true);
			$headers[] = array('Vary', 'Accept-Encoding', false);

			$content = gzencode($content, 1);
		}

		return $headers;
	}
    
    
    public static function generateRandomString($length)
	{
		while (strlen(self::$_randomData) < $length)
		{
			//  OpenSSL_Random_Pseudo_Bytes is **ridiculously** slow on windows
			if (function_exists('openssl_random_pseudo_bytes') && substr(PHP_OS, 0, 3) != 'WIN')
			{
				self::$_randomData .= bin2hex(openssl_random_pseudo_bytes(max($length, 1024) / 2));
			}
			else
			{
				self::$_randomData .= md5(uniqid(mt_rand(), true));
			}
		}

		$return = substr(self::$_randomData, 0, $length);
		self::$_randomData = substr(self::$_randomData, $length);

		return $return;
	}
    
}