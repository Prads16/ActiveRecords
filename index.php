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
      //echo '<center><b>Database connected successfully.</b></center> <br>';
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

$obj = new displayoutput();
class displayoutput
{
    public function __construct()
    {
        echo "<h1> Table: Accounts </h1>";
        echo '<h2>Select All Records</h2>';
        $records = accounts::findAll();
        $tableGen = table::displayTable($records);

        echo '<h2>Select One Record</h2>';
        $id=5;
        $records = accounts::findOne($id);
        echo '<h3>Record retrieved with id: '.$id.'</h3>';
        $tableGen = table::displayTable($records);

        echo '<h2>Delete one record</h2>';
        $id=19;
        $records = account::delete($id);
        echo "Deleted record with id: $id <br>";

        echo "<h3>Table accounts after delete.</h3>";
        $records = accounts::findAll();
        $tableGen = table::displayTable($records);

        /*echo "<h2> Insert one record </h2>";
        $record = new account();
        $record->email = "paul089@gmail.com";
        $record->fname = "Paul";
        $record->lname = "Smith";
        $record->phone="12345";
        $record->birthday="1994-02-01";
        $record->gender="male";
        $record->password="567";
        $record->save();
        $records = accounts::findAll();
        $tableGen = table::displayTable($records);*/


        echo "<h2>Update a record</h2>";
        $record = new account();
        $record->id = 6;
        $record->fname="John";
        $record->lname="Smith";
        $record->gender="male";
        $record->save();
        $records = accounts::findAll();
        $tableGen = table::displayTable($records);
        echo "<h3>Updated the record with id: $record->id.</h3>";

        echo "<h1> Table: To-dos </h1>";
        echo '<h2>Select All Records</h2>';
        $records = todos::findAll();
        $tableGen = table::displayTable($records);      

    }
}

abstract class collection 
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
 


abstract class model
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

    public function save()
    {
        if ($this->id != '') 
        {
           $sqlquery = $this->update();
        } 
        else 
        {
           $sqlquery = $this->insert();
        }
        $db = dbConnect::getConnection();
        $statement = $db->prepare($sqlquery);
        $arraylist = get_object_vars($this);
        foreach (array_flip($arraylist) as $key=>$value)
        {
            $statement->bindParam(":$value", $this->$value);
        }
        $statement->execute();
    }
    private function insert() 
    {
        $modelName=static::$modelName;
        $tableName = $modelName::getTablename();
        $arraylist = get_object_vars($this);
        $colstring = implode(',', array_flip($arraylist));
        $valstring = ':'.implode(',:', array_flip($arraylist));
        $sqlquery =  'INSERT INTO '.$tableName.' ('.$colstring.') VALUES ('.$valstring.')';
        return $sqlquery;
    }

    private function update()
    {
        $modelName=static::$modelName;
        $tableName = $modelName::getTablename();
        $arraylist = get_object_vars($this);
        $separate = " ";
        $sqlquery = 'UPDATE '.$tableName.' SET ';
        foreach ($arraylist as $key=>$value)
        {
            if( ! empty($value)) 
            {
                $sqlquery .= $separate . $key . ' = "'. $value .'"';
                $separate = ", ";
            }
        }
        $sqlquery .= ' WHERE id='.$this->id;
        return $sqlquery;
    }
}

class todo extends model 
{
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    protected static $modelName = 'todo';

    public static function getTablename()
    {
        $tableName='todos';
        return $tableName;
    }
}

class account extends model 
{
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    protected static $modelName = 'account';

    public static function getTablename()
    {
        $tableName='accounts';
        return $tableName;
    }
}

$obj = new table();
class table
{
    public static function displayTable($record)
    {
        $tableGen = '<table border="1"';
        foreach($record as $row => $innerArray)
        {
            $tableGen .= '<tr>';
            foreach($innerArray as $innerRow => $value)
            {
            	$tableGen .= '<td>' . $value.'</td>';

            }
            $tableGen .= '</tr>';
        }
        $tableGen .= '</table>';
        print_r($tableGen);
    }
}









 
?>
