<?php

/**  
 *   Project:    XML Phonebook
 *   Package:    Application.php
 *   Authors:    Traven, Truvis
 *   Version:    1.0.4
 *   License:    BSD Clause 3, New or Revisted License
 *   
 *   This class is going to handle generalized functions which the full web 
 *   can access and used. The reasoning behind this setup is to organize the
 *   source code into logical thinking pieces. Also it helps with the actual
 *   page construction and overall feel of a properly coded application.
 */
 
class Application
{
    /**
     *   I am not sure if we need this or not, but it may come in handy for us
     *   when we are working on new feature releases of the web application. Integer
     *   do know that havin a version number reference for the "SysAdmins" would
     *   help with extending and version control of each new release.
     *
     *   Variable:  String
     *   Returns:   Version Number and ID
     */
    public static $version   = "1.0.4";
    public static $versionID = "01000004";  //  AABBCC0D (7 = stable, 5 = beta, 3 = alpha)
    
    
    /**
	 *   We need to define the path to the web application's root directory
     *   because we can look for other directories within the root.
	 *
	 *   Variable:  String
     *   Returns:   Root directory of the web application
	 */
	public static $rootDir = '.';
    
    
    
    /**
     *   We need to get unix timestamp representing the current webserver date
     *   and time. This is for getting dates of creation and "now" reference.
	 *
	 *   Variable:  Integer
     *   Returns:   Current time in Unix 
	 */
	public static $time = 0;
    
}
