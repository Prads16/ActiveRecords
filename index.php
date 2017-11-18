<?php
 define('DATABASE', 'pra22');
 define('USERNAME', 'pra22');
 define('PASSWORD', 'zsLR8d2wM');
 define('CONNECTION', 'sql2.njit.edu');

 class dbConnect
 {
 protected static $conn;
 private function __construct()
  {
    try 
    {
      self::$conn = new PDO('mysql:host=' . CONNECTION . ';dbname=' . DATABASE, USERNAME, PASSWORD);
      self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      echo 'Database connected successfully. <br>';
    } 
    catch (PDOException $e) 
    {
      echo 'Connection to the database failed ' . $e->getMessage() . '<br>';
    }
  }
 public static function getConnection()
  {
    if (!self::$conn)
    {
      new dbConnect();
    }
    return self::$conn;
  }
 }
 ?>
