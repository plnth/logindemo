<?php 

class Database {
    private $connection;
    private $serverName;
    private $username;
    private $password;
    private $dbName;
    private $selectCount;
    
    public function __construct($serverName, $username, $password, $dbName) {
        
        $this->serverName = $serverName;
        $this->username = $username;
        $this->password = $password;
        $this->dbName = $dbName;
        
        try {
            $this->connection = new PDO("mysql:hostname=$this->serverName;dbname=$this->dbName", $this->username, $this->password);
        
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } 
        catch (PDOException $e) {
            echo("Connection failed: ".$e->getMessage()."<br/>");
        }
    }
    
    public function closeConnection() {
        $this->connection = null;
    }
    
    public function insert($table, $fields) {
        
        $index = 0;
        
        $columns = "";
        $valueNames = "";
        $params = array();
        
        foreach($fields as $field) {
            $rootKey = array_keys($fields)[$index];
            
            $columns .= $rootKey;
            
            if($index < count($fields) - 1) {
                $columns .= ", ";
            }
            
            $key = array_keys($field)[0];
            $value = $field[$key];
            
            $valueNames .= $key;
            
            if($index < count($fields) - 1) {
                $valueNames .= ", ";
            }
            
            $params[$key] = $value;
            
            $index++;
        }
        
        $query = $this->connection->prepare("
            INSERT INTO $table ($columns)
            VALUES ($valueNames)
        ");
        
        $query->execute($params);
    }
    
    public function select($table, $where = NULL) {
        $query = NULL;
        
        if($where === NULL) {
            $query = $this->connection->prepare("
                SELECT * FROM $table
            ");
        }
        else {
            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];
            
            $query = $this->connection->prepare("
                SELECT * FROM $table
                WHERE $field $operator '$value'
            ");
        }
        
        $query->execute();
        
        $result = $query->fetchAll(\PDO::FETCH_OBJ);
        $this->selectCount = $query->rowCount();
        
        return $result;
    }
    
    public function count() {
        return $this->selectCount;
    }
}

?>