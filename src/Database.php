<?php

/**  
 *   Project:    XML Phonebook
 *   Package:    Database.php
 *   Authors:    Traven, Truvis
 *   Version:    1.0.0
 *   License:    BSD Clause 3, New or Revisted License
 *   
 *   This class is going to hadle all database connections and queries for the
 *   whole webh application. The functions prepare all of the queries before
 *   they are executed. This is the core class to the web application!
 */
 
class PDO
{
     
     /**
      *   These variables are going to allow us to connect to the MySQL
      *   database which the web application requires. This information
      *   should be protected and never shared outside of the class.
      *
      *   Variables:  String
      *   Returns:    MNySQL database  login information
      */
     protected $Hostname = '';
     protected $Database = '';
     protected $Username = '';
     protected $Password = '';
     
     protected $PDO;
     
     /**
      *   We are going to allow the Database class to handle errors itself till
      *   we create an error handling class. This is going to save a lot of 
      *   time so we don't have to try to get the error handling to work.
      *   
      */
     protected $error;

     
     /**
      *   We are going to make our database connection globally available to
      *   the whole web application. This is going to allow us to insert SQL
      *   queries directly into our the application.
      *
      *   Variables:  $PDO::%Function%
      *   Returns:    Globally accessable database instance
      */
     public function __construct()
     {
          //  I'm going to turn the "heavy lifting" piece of the connection as
          //  a simplized variable for our use of it.
          $DNS = 'mysql:host=' . $this->hostname . ';dbname=' . $this->database;
          
          //  I am going to add some "properly coded" options to the use of the
          //  database connection. We are going to make our connection be "on"
          //  all of the time. Then if any errors decide to happen to us while
          //  we are "running" or developing the web application, they are 
          //  going to be handled "gracefully" over a ugly mess.
           $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
          );
          
          //  Now I am going to do the actual connection of the database within 
          //  the class constructor so we gain global access to the connection.
          try
          {
               $this->PDO = new PDO($DNS, $this->Username, $this->Password, $options);
          }
          // Catch any errors
          catch(PDOException $error)
          {
               $this->error = $error->getMessage();
          }
     }
}
