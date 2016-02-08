<?php

/**
 *   ----------------------------------------------------------------------------------------------
 *   Class Information
 *   ----------------------------------------------------------------------------------------------
 *   Project:  QuadFramework 
 *   Package:  Models.php
 *   Authors:  Traven West <me@travenwest.com>
 *   Version:  1.0.4
 *   License:  BSD Clause 3 "New or Revised" License
 *   ----------------------------------------------------------------------------------------------
 *   Class Description
 *   ----------------------------------------------------------------------------------------------
 *   This is the abstract class for Models. Usually models don't share that much, so most of the
 *   implementations will be adding methods onto this class. This class simply provides helper
 *   methods for common actions that are shared between classes.
 *   ----------------------------------------------------------------------------------------------
 */
 
abstract class QuadFramework_Model
{

	public function __construct() {}    
     
     
	public function ValidOperator($operator)
	{
		switch ($operator)
		{
			case '<':
			case '<=':
			case '=':
			case '>':
			case '>=':
				break;

			default:
				throw new Exception('Invalid cut off operator.');
		}
	}
}