<?php

/**  
 *   Project:    LearnPHP CMS
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
      *   Returns:    MNySQL database login information
      */
     protected $Hostname = '';
     protected $Database = '';
     protected $Username = '';
     protected $Password = '';
     
     
     /**
      *   I am going to store the class instance as a variable for easier use
      *   and to create SQL queries and functions. This should allow for the 
      *   best security and prepared statements.
      *   
      *   Variables:  Instance
      *   Returns:    The database/PDO class
      */
     protected $PDO;
     
     /**
      *   We are going to allow the Database class to handle errors itself till
      *   we create an error handling class. This is going to save a lot of 
      *   time so we don't have to try to get the error handling to work.
      *   
      */
     protected $error;
     
     /**
      *   We are going to store the SQL statement as an object of the class so
      *   we can protect it from outside classes if another class was to be
      *   exploitable or vulnerable in the future. This is one of the safest
      *   ways to handle SQL queries for "future proofing".
      */
     protected $statement;

     
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

          catch(PDOException $error)
          {
               $this->error = $error->getMessage();
          }
     }
     
     
     /**
      *   I'm going to make our SQL query default to being a prepared statement
      *   over allowing our developing to cause SQL injections. This is a major
      *   security enhancing idea. In other words, all SQL statements are going
      *   to be prepared to remove the worry of hackers doing SQL injections.
      *   
      *   Variables:  SQL statement 
      *   Returns:    Prepared sql statement
      */
     public function query($query)
     {
          $this->statement = $this->PDO->prepare($query);
     }
     
     
     /**
      *   This may be the most complex piece of the Database class that will
      *   be used in all SQL statements. We are preparing the queries even
      *   further to stop all exploition. We are using a switch statement to
      *   allow the code/query choose the type of data which is going to be
      *   returned to us from the four common types.
      *   
      *   Variables:  Types
      *   Returns:    Binded SQL results
      */
     public function bind($param, $value, $type = null)
     {
          if (is_null($type)) 
               {
                    switch (true) 
                    {
                         case is_int($value):
                         $type = PDO::PARAM_INT;
                         break;
                         
                         case is_bool($value):
                         $type = PDO::PARAM_BOOL;
                         break;
                         
                         case is_null($value):
                         $type = PDO::PARAM_NULL;
                         break;
                         
                    default:
                    $type = PDO::PARAM_STR;
                    }
               }
          
          $this->statement->bindValue($param, $value, $type);
     
     }
     
     /**
      *   Now I can can create our function to execute our prepared the SQL
      *   statements that we write in the web application. All of the work 
      *   to prepare the statements is done, and now we need to actually
      *   execute and return our results which we requested.
      *   
      *   Variables:  
      *   Returns:    The query results
      */
     public function execute()
     {
          return $this->statement->execute();
     }
}
