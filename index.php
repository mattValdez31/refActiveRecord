<?php

namespace conn {
//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);

define('DATABASE', 'mjv32');
define('USERNAME', 'mjv32');
define('PASSWORD', 'ccYhBxVxR');
define('CONNECTION', 'sql2.njit.edu');

class dbConn
{
	//variable to hold connection object
	protected static $db;

	//private constructor - class cannot be instantiated externally
	private function __construct()
	{
		try
		{
			//assign PDO object to db variable
			self::$db = new PDO( 'mysql:host=' . CONNECTION.';dbname=' . DATABASE, USERNAME, PASSWORD);
			self::$db->setAttribute( PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)
		{
			//output error
			echo "Connection Error: " . $e->getMessage();
		}
	}

	// static method - accessible w/o instantiation
	public static function getConnection()
	{
		// guarantees single instance, if no connection object exists then create one.
		if (!self::$db) 
		{
			// new connection object
			new dbConn();
		}

		// return connection
		return self::$db;
	}
}
}

namespace coll {
class collection
{
	static public function create()
	{
		$model = new static::$modelName;
		return $model;
	}

	static public function findAll()
	{
		$db = dbConn::getConnection();
		$tableName = get_called_class();
		$sql = 'SELECT * FROM ' . $tableName;
		$statement = $db->prepare($sql);
		$statement->execute();
		$class = static::$modelName;
		$statement->setFetchMode(PDO::FETCH_CLASS, $class);
		$recordsSet = $statement->fetchAll();
		return $recordsSet;
	}

	static public function findOne($id)
	{
		$db = dbConn::getConnection();
		$tableName = get_called_class(); //gets name of current class
		$sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
		$statement = $db->prepare($sql);
		$statement->execute();
		$class = static::$modelName; //$modelName from child class
		$statement->setFetchMode(PDO::FETCH_CLASS, $class); //maps columns of each row to class variables
		$recordsSet = $statement->fetchAll();
		return $recordsSet[0];
	}
}
}

namespace colAccts {
class accounts extends collection
{
	protected static $modelName = 'account';
}
}

namespace colTodos {
class todos extends collection
{
	protected static $modelName = 'todo';
}
}

namespace mdl {
class model
{
	protected $tableName;
	public function save()
	{
		//$columns = get_object_vars($this);
		$class = get_called_class();
		$tableName = $class::getTableName();

		if ($this->id == '')
		{
			$sql = $this->insert($tableName);
		}

		else
		{
			$sql = $this->update($tableName);
		}
		
		$db = dbConn::getConnection();
		$statement = $db->prepare($sql);
		$statement->execute();
		print_r($sql);
		/*
		//$tableName = get_called_class();
		$class = get_called_class();
		$tableName = $class::getTableName();

		$array = get_object_vars($this);
		$columnString = implode(',', $array);
		$valueString = ":".implode(',:', $array);

		echo "INSERT INTO $tableName (" . $columnString . ") VALUES (" . $valueString . ")</br>";

		echo 'I just saved record: ' . $this->id;*/
	}
	
	static public function remove($id)
	{
		$class = get_called_class();
		$tableName = $class::getTableName();
		$db = dbConn::getConnection();

		$sql = 'DELETE FROM ' . $tableName . ' WHERE id = ' . $id;
		
		$statement = $db->prepare($sql);
		$statement->execute();
		print_r($sql);
		return $statement;
	}

	public function insert($tableName)
	{
		if ($tableName == 'accounts')
		{
			$sql = 'INSERT INTO ' . $tableName . ' (' .  $this->col[1] . ',' . $this->col[2] . ',' . $this->col[3] . ',' . $this->col[4] . ',' . $this->col[5] . ',' . $this->col[6] . ',' . $this->col[7] . ') VALUES (' . $this->email . ',' . $this->fname . ',' . $this->lname . ',' . $this->phone . ',' . $this->birthday . ',' . $this->gender . ',' . $this->password . ')';
			return $sql;
		}
		else
		{
			$sql = 'INSERT INTO ' . $tableName . ' (' . $this->col[1] . ',' . $this->col[2] . ',' . $this->col[3] . ',' . $this->col[4] . ',' . $this->col[5] . ',' . $this->col[6]  . ') VALUES (' . $this->owneremail . ',' . $this->ownerid . ',' . $this->createddate . ',' . $this->duedate . ',' . $this->message . ',' . $this->isdone . ')';
			return $sql;
		}
	}

	static private function update($changes)
	{
		/*if ($tableName == 'accounts')
		{
			$sql = 'UPDATE ' . $tableName . ' SET '
		}
		else
		{

		}*/
	}

}
}

namespace mdlAcct {
class account extends model
{
	public $id;
	public $email = 'NULL';
	public $fname = 'NULL';
	public $lname = 'NULL';
	public $phone = 'NULL';
	public $birthday = 'NULL';
	public $gender = 'NULL';
	public $password = 'NULL';

	public $col = array('id', 'email', 'fname', 'lname', 'phone', 'birthday', 'gender', 'password');

	public function __construct()
	{
		$this->tableName = 'accounts';
	}

	static public function getTableName()
	{
		$tableName = 'accounts';
		return $tableName;
	}


}
}

namespace mdlTodo {
class todo extends model
{
	public $id;
	public $owneremail = 'NULL';
	public $ownerid = 'NULL';
	public $createddate = 'NULL';
	public $duedate = 'NULL';
	public $message = 'NULL';
	public $isdone = 'NULL';
	
	public $col = array('id', 'owneremail', 'ownerid', 'createddate', 'duedate', 'message', 'isdone');

	public function __construct()
	{
		$this->tableName = 'todos';
	}

	static public function getTableName()
	{
		$tableName = 'todos';
		return $tableName;
	}

}
}

class htmlTags
{
	public static function horizontalRule()
	{
		return '<hr>';
	}

	public static function headingOne($text)
	{
		return '<h1>' . $text . '</h1>';
	}
}

class htmlTable
{
	public static function genTable($result)
	{
		echo '<table>';
		foreach ($result as $column)
		{
			echo '<tr>';
			foreach ($column as $row)
			{
				echo '<td>';
				echo $row;
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}
}
				 				

echo htmlTags::headingOne('Find One Entry demo');
$record = todos::findOne(3);
//print_r($records);
htmlTable::genTable($record);
echo htmlTags::horizontalRule();

echo htmlTags::headingOne('Find All Entries demo:');
$records = accounts::findAll();
//print_r($record);
htmlTable::genTable($records);
echo htmlTags::horizontalRule();

/*echo htmlTags::headingOne('Insert demo:');
$ins = new todo();
$ins->isdone = 0;
$ins->save();
echo htmlTags::horizontalRule();

echo htmlTags::headingOne('Delete demo:');
$del = account::remove(13);
echo htmlTags::horizontalRule();

echo htmlTags::headingOne('Update demo:');
$upd = new todo();
$upd->message = 'almost done';
//$changes = array(
$up = todo::update(

*/

//$rec = new todo();
//$rec->isdone = 0;
//$rec->id = 15;
//$rec->phone = '7327569999';
//$rec->save();
//print_r($records);
//print_r($record);
//print_r($del);

?>
