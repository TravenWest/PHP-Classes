<?php

/**
 *   ----------------------------------------------------------------------------------------------
 *   Class Information
 *   ----------------------------------------------------------------------------------------------
 *   Project:  QuadFramework 
 *   Package:  DataWriter.php
 *   Authors:  Traven West <me@travenwest.com>
 *   Version:  1.0.4
 *   License:  BSD Clause 3 "New or Revised" License
 *   ----------------------------------------------------------------------------------------------
 *   Class Description
 *   ----------------------------------------------------------------------------------------------
 *   This is an abstract class for data writing. This class will allow us to focus on writing units
 *   of data to the database. It also helps with verifying all data to the web application rules.
 *   
 *   This includes those set by the owner and doing denormalized updates as necessary.
 *   ----------------------------------------------------------------------------------------------
 */

abstract class QuadFramework_DataWriter
{
    
    const ERROR_EXCEPTION = 1;
    const ERROR_ARRAY = 2;
    const ERROR_SILENT = 3;
    
    
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_STRING = 'string';
    const TYPE_BINARY = 'binary';
    const TYPE_INT = 'int';
    const TYPE_UINT = 'uint';
    const TYPE_UINT_FORCED = 'uint_forced';
    const TYPE_FLOAT = 'float';
    const TYPE_SERIALIZED = 'serialized';
    const TYPE_UNKNOWN = 'unknown';
    
    
    protected $_fields = array();
    
    
    protected $_options = array();
    
    
    protected $_extraData = array();
    
    
    protected $_newData = array();
    
    
    protected $_existingData = array();

    
	protected $_errorHandler = 0;
    
    protected $_errors = array();
    
    
    protected $_setOptions = array(
		'ignoreInvalidFields' => false,
		'replaceInvalidWithDefault' => false,
		'runVerificationCallback' => true,
		'setAfterPreSave' => false
	);
    
    
	public function __construct()
	{
		$fields = $this->_getFields();
		if (is_array($fields))
		{
			$this->_fields = $fields;
		}
	}
    
    
	abstract protected function _getFields();
    
    
    abstract protected function _getExistingData($data);
    
    
    abstract protected function _getUpdateCondition($tableName);
    
    
    protected function _getExistingPrimaryKey($data, $primaryKeyField = '', $tableName = '')
	{
		if (!$tableName)
		{
			$tableName = $this->_getPrimaryTable();
		}

		if (!$primaryKeyField)
		{
			$primaryKeyField = $this->_getAutoIncrementField($tableName);
		}

		if (!isset($this->_fields[$tableName][$primaryKeyField]))
		{
			return false;
		}

		if (is_array($data))
		{
			if (isset($data[$primaryKeyField]))
			{
				return $data[$primaryKeyField];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $data;
		}
	}
    
    
    protected function _getDefaultOptions()
	{
		return array();
	}
    
	
	protected function _setInternal($table, $field, $newValue, $forceSet = false)
	{
		$existingValue = $this->get($field, $table);
		if ($forceSet
			|| $existingValue === null
			|| !is_scalar($newValue)
			|| !is_scalar($existingValue)
			|| strval($newValue) != strval($existingValue)
		)
		{
			if ($newValue === $this->getExisting($field, $table))
			{
				unset($this->_newData[$table][$field]);
			}
			else
			{
				$this->_newData[$table][$field] = $newValue;
			}
		}
	}
    
    
    protected function _isFieldValueValid($fieldName, array $fieldData, &$value, array $options = array())
	{
		$fieldType = isset($fieldData['type']) ? $fieldData['type'] : self::TYPE_BINARY;
		$value = $this->_castValueToType($fieldType, $value, $fieldName, $fieldData);

		if (!empty($options['runVerificationCallback']) && !empty($fieldData['verification']))
		{
			if (!$this->_runVerificationCallback($fieldData['verification'], $value, $fieldData, $fieldName))
			{
				//  The verification callbacks are responsible for throwing errors
				return false;
			}
		}

		$checkLimits = $this->_applyFieldValueLimits($fieldType, $value, $fieldData);

		if ($checkLimits !== true)
		{
			if (empty($options['replaceInvalidWithDefault']) || !isset($fieldData['default']))
			{
				$this->error($checkLimits, $fieldName, false);
				return false;
			}
			else
			{
				$value = $fieldData['default'];
			}
		}

		return true;
	}
    
    
	protected function _ValueToType($fieldType, $value, $fieldName, array $fieldData)
	{
		switch ($fieldType)
		{
			case self::TYPE_STRING:
				if (isset($fieldData['noTrim']))
				{
					return strval($value);
				}
				else
				{
					return trim(strval($value));
				}

			case self::TYPE_BINARY:
				return strval($value);

			case self::TYPE_UINT_FORCED:
				$value = intval($value);
				return ($value < 0 ? 0 : $value);

			case self::TYPE_UINT:
			case self::TYPE_INT:
				return intval($value);

			case self::TYPE_FLOAT:
				return strval($value) + 0;

			case self::TYPE_BOOLEAN:
				return ($value ? 1 : 0);

			case self::TYPE_SERIALIZED:
				if (!is_string($value))
				{
					return serialize($value);
				}

				if (@unserialize($value) === false && $value != serialize(false))
				{
					throw new Exception('Value is not unserializable');
				}

				return $value;

			case self::TYPE_UNKNOWN:
				return $value; //  Would be unmodified

			default:
				throw new Exception((
					($fieldName === false)
					? "There is no field type '$fieldType'."
					: "The field type specified for '$fieldName' is not valid ($fieldType)."
				));
		}
	}
    
    
    public function setErrorHandler($errorHandler)
	{
		$this->_errorHandler = intval($errorHandler);
	}
    
    
    public function get($field, $tableName = '')
	{
		$tables = $this->_getTableList($tableName);

		foreach ($tables AS $tableName)
		{
			if (isset($this->_newData[$tableName][$field]))
			{
				return $this->_newData[$tableName][$field];
			}
			else if (isset($this->_existingData[$tableName][$field]))
			{
				return $this->_existingData[$tableName][$field];
			}
		}

		return null;
	}
    
    
    public function getNew($field, $tableName = '')
	{
		$tables = $this->_getTableList($tableName);

		foreach ($tables AS $tableName)
		{
			if (isset($this->_newData[$tableName][$field]))
			{
				return $this->_newData[$tableName][$field];
			}
		}

		return null;
	}
    
    
    public function isChanged($field, $tableName = '')
	{
		return ($this->getNew($field, $tableName) !== null);
	}
    
    
    public function getExisting($field, $tableName = '')
	{
		$tables = $this->_getTableList($tableName);

		foreach ($tables AS $tableName)
		{
			if (isset($this->_existingData[$tableName][$field]))
			{
				return $this->_existingData[$tableName][$field];
			}
		}

		return null;
	}
    
    
    public function getMergedData($tableName = '')
	{
		$tables = $this->_getTableList($tableName);

		$output = array();

		//  It loop through all tables and use the first value that comes up for a field.
		//  Also this assumes that the more "primary" tables come first
		foreach ($tables AS $tableName)
		{
			if (isset($this->_newData[$tableName]))
			{
				$output += $this->_newData[$tableName];
			}

			if (isset($this->_existingData[$tableName]))
			{
				$output += $this->_existingData[$tableName];
			}
		}

		return $output;
	}
    
    
    public function error($error, $errorKey = false, $specificError = true)
	{
		if ($errorKey !== false)
		{
			if ($specificError || !isset($this->_errors[strval($errorKey)]))
			{
				$this->_errors[strval($errorKey)] = $error;
			}
		}
		else
		{
			$this->_errors[] = $error;
		}

		if ($this->_errorHandler == self::ERROR_EXCEPTION)
		{
			throw new Exception($error, true);
		}
	}
    
    
    public function getErrors()
	{
		return $this->_errors;
	}