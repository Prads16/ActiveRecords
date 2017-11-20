<?php

ini_set('display_errors','On');
error_reporting(E_ALL);

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
      echo '<center><b>Database connected successfully.</b></center> <br>';
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
 class collection 
{
  protected $tableName;
  public static function createdb() 
  {
    $model = new static::$modelName;
    return $model;
  }
  public static function findAll()
  {
    $db = dbConnect::getConnection();
    $tableName = get_called_class();
    $sqlquery = 'SELECT * FROM ' . $tableName;
    $statement = $db->prepare($sqlquery);
    $statement->execute();
    $childclass = static::$modelName;
    $statement->setFetchMode(PDO::FETCH_CLASS, $childclass);
    $recordsSet =  $statement->fetchAll();
    return $recordsSet;
  }
  public static function findOne($id) 
  {
    $db = dbConnect::getConnection();
    $tableName = get_called_class();
    $sqlquery = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
    $statement = $db->prepare($sqlquery);
    $statement->execute();
    $childclass = static::$modelName;
    $statement->setFetchMode(PDO::FETCH_CLASS, $childclass);
    $recordsSet =  $statement->fetchAll();
    return $recordsSet;
    
  }

}
class accounts extends collection 
{
  protected static $modelName = 'account';
}
class todos extends collection 
{
  protected static $modelName = 'todo';
}

 
$records = accounts::findAll();
print_r($records);
$records = todos::findAll();
print_r($records);
$records = accounts::findOne(4);
print_r($records);
$records = todos::findOne(4);
print_r($records);

class model
{
	protected $tableName;
	
    public static function delete($id) 
    {
        $db = dbConnect::getConnection();
        $modelName = static::$modelName;
        $tableName = $modelName::getTablename();
        $sqlquery = 'DELETE FROM '.$tableName.' WHERE id='.$id;
        $statement = $db->prepare($sqlquery);
        $statement->execute();
    }

}



class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    protected static $modelName = 'todo';
    public static function getTablename(){
        $tableName='todos';
        return $tableName;
    }
}

$result = todo::delete(2);
print_r($result);








 
?>
