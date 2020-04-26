<?php 
class DB{
    private $_host = "localhost";
    private $_dbname ="lestari";
    private $_username = "root";
    private $_password = "";

    private static $_instance = null;
    private $_columnName = "*";
    private $_orderBy = "";
    private $_count = 0;
    private $_pdo;

    public function __construct() 
    {   try {
        $this->_pdo = new PDO('mysql:host = '.$this->_host.';dbname='.$this->_dbname,$this->_username
        ,$this->_password);
        $this->_pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    
    catch (PDOException $e) {
        die("Koneksi / Query bermasalah :" .$e->getMessage()."(".$e->getCode().")");
    }
}
    public static function getInstance(){
        if(!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;

    }
    public function RunQuery($query,$bindvalue = [])
    {
        try{
            $stmt = $this->_pdo->prepare($query);
            $stmt->execute($bindvalue);
        }
        catch (PDOException $e) {
            die("koneksi / query bermasalah =".$e->getMessage()."(".$e->getCode().")");
        }
        return $stmt;
    }
    public function getQuery($query,$bindvalue = []){
        return $this->RunQuery($query,$bindvalue)->fetchAll(PDO::FETCH_OBJ);
    }
    public function select($columnName){
        $this->_columnName = $columnName;
        return $this;
    }
    public function orderBy($columnName,$sortType = "ASC"){
        $query = "ORDER BY {$columnName} {$sortType}";
        return $this;
    }
    public function get($tablename,$condition="",$bindvalue=[]){
    $query = "SELECT {$this->_columnName} FROM {$tablename} {$condition} {$this->_orderBy}";
    $this->_columnName = "*";
    $this->_orderBy = "";
    return $this->getQuery($query,$bindvalue);
    }
    public function getWhere($tablename,$condition){
        $queryCondition = "WHERE {$condition[0]} {$condition[1]} ?";
        return $this->get($tablename,$queryCondition,[$condition[2]]);
      
    }
    public function getWhereOnce($tablename,$condition){
        $result= $this->getWhere($tablename,$condition);
        if (!empty($result)){
            return $result[0];
        } else {
        return false;
        }

    }
    public function getLike($tablename,$columnLike,$search){
        $queryLike = "WHERE {$columnLike} LIKE ?";
        return $this->get($tablename,$queryLike,[$search]);
    }

    public function count(){
        return $this->_count;
    }
    public function check($tablename,$columnName,$dataValues){
    $query = "SELECT {$columnName} FROM {$tablename} WHERE {$columnName} = ?";
    return $this->RunQuery($query,[$dataValues])->rowCount();
    }

    public function insert($tablename,$data){
        $dataKeys = array_keys($data);
        $dataValues = array_values($data);
        $placeholder = "(".str_repeat('?,',count($data)-1)."?)";

    $query = "INSERT INTO {$tablename} (".implode(',',$dataKeys).") VALUES {$placeholder}";
     $this->_count=$this->RunQuery($query,$dataValues)->rowCount();
     return true;
    }

    public function update($tableName, $data, $condition){
        $query = "UPDATE {$tableName} SET ";
        foreach ($data as $key => $val){
          $query .= "$key = ?, " ;
        }
        $query = substr($query,0,-2);
        $query .= " WHERE {$condition[0]} {$condition[1]} ?";
    
        $dataValues = array_values($data);
        array_push($dataValues,$condition[2]);
    
        $this->_count = $this->runQuery($query,$dataValues)->rowCount();
        return true;
      }
    public function delete($tablename,$condition){
    $query = "DELETE FROM {$tablename} WHERE {$condition[0]} {$condition[1]} ?";
    $this->_count=$this->RunQuery($query,[$condition[2]])->rowCount();
    return true;
    }
}