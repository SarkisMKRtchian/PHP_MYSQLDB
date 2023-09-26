<?php

/**
 * @author https://github.com/SarkisMKRtchian
 * @param string $username user name
 * @param string $password user password
 * @param string $host     database host 
 * @param string $database database name
 * 
 */
class MYSQLDB{
    
    private string $host;
    private string $database;
    private string $username;
    private string $password;
    public string $path;
    public bool $send;

    function __construct(string $host, string $database, string $username, string $password){
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
    }

    private function conect(){
        try{
            $conn = new PDO("mysql:host=$this->host; dbname=$this->database", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->query("SET NAMES 'utf8'");
            $this->send = true;
            return $conn;
        }catch(PDOException $err){
            $err_code = $err->getCode();
            $err_message = $err->getMessage();
            $line = debug_backtrace()[0]['line'];
            $file = debug_backtrace()[0]['file'];
            $error = [
                'err_code' => $err_code,
                'err_message' => $err_message,
                'line' => $line,
                'file' => $file
            ];
            $this->send = false;
            return $error;
        }
    } 

    /**
     * select items from db
     * @param string $table table name 
     * @param string $columns the names of the columns to be selected or '*' select all
     * @return array object with your column name columns
     */
    public function select(string $table, string $columns) {
        try{
            $sql = "SELECT $columns FROM $table";
            $db_items = $this->conect()->query($sql)->fetchAll();
            $this->send = true;
            return $db_items;
        }catch(PDOException $err){
            $err_code = $err->getCode();
            $err_message = $err->getMessage();
            $line = debug_backtrace()[0]['line'];
            $file = debug_backtrace()[0]['file'];
            $error = [
                'time' => date('d.m.Y H:i'),
                'code' => $err_code,
                'message' => $err_message,
                'line' => $line,
                'file' => $file
            ];
            $this->send = false;
            return $error;
        }
    }

    /**
     * get column names
     * @param string $table table name 
     * @return array return object with your column name
     */
    public function selectColmnsInfo($table){
        try{
            $sql = "DESC `$table`";
            $col_names = $this->conect()->query($sql)->fetchAll();
            $this->send = true;
            return $col_names;
        }catch(PDOException $err){
            $err_code = $err->getCode();
            $err_message = $err->getMessage();
            $line = debug_backtrace()[0]['line'];
            $file = debug_backtrace()[0]['file'];
            $error = [
                'time' => date('d.m.Y H:i'),
                'code' => $err_code,
                'message' => $err_message,
                'line' => $line,
                'file' => $file
            ];
            $this->send = false;
            return $error;
        }
    }

    /**
     * Get information schema from your db
     * @param string $items items to gets. '*' get all items
     * @param string $database db name
     * @param string $table tbale name
     * @param string $column table column names
     * @return array returns an objet with your data
     * 
     * Useful Arguments
     * DATA_TYPE                —― get column type |
     * CHARACTER_MAXIMUM_LENGTH ―― get column lenght |
     * COLUMN_KEY               ―― get column keys, if any |
     */
    public function selectDbInfo(string $items, string $database, string $table, string $column = NULL){
        try{
            $sql = "SELECT $items 
            FROM information_schema.COLUMNS 
            WHERE table_schema = '$database' 
            AND table_name = '$table'";
            if($column != NULL){
                $sql .= " AND column_name = '$column'";
            }
            $data = $this->conect()->query($sql)->fetchAll();
            $this->send = true;
            return $data;
        }catch(PDOException $err){
            $err_code = $err->getCode();
            $err_message = $err->getMessage();
            $line = debug_backtrace()[0]['line'];
            $file = debug_backtrace()[0]['file'];
            $error = [
                'time' => date('d.m.Y H:i'),
                'code' => $err_code,
                'message' => $err_message,
                'line' => $line,
                'file' => $file
            ];
            $this->send = false;
            return $error;
        }
        
    }

    /**
     * add items in db
     * @param string $table table name
     * @param array $data array in strict ordering of your columns 
     * IMPORTANT! The size of $data should match the size of your table columns.
     * @return boolean|array returns true if successful. Returns an array with error information on failure
     */
    public function add(string $table, array $data) {
        $cols = $this->selectColmnsInfo($table);

        $table_cols = [];

        foreach($cols as $key){
            $table_cols[] = $key['Field'];
        }
   
        $colss = "";
        $vall = "";
        $conn = $this->conect();
        for($i = 0; $i < count($table_cols); $i++){
            
            $db_item_lenght[$i][0]['name'] = $table_cols[$i]; 
            if($i + 1 == count($table_cols)){
                $colss .= "`{$table_cols[$i]}`";
                $vall .= "?";
                continue;
            }
            $colss .= "`{$table_cols[$i]}`, ";
            $vall .= "?, ";
        }
        
        

        try{
            $sql = "INSERT INTO `$table` ($colss) VALUES ($vall)";
            $stmt = $conn->prepare($sql);
            $stmt->execute($data);
            $this->send = true;
        }catch(PDOException $err){
            $err_code = $err->getCode();
            $err_message = $err->getMessage();
            $line = debug_backtrace()[0]['line'];
            $file = debug_backtrace()[0]['file'];
            $error = [
                'time' => date('d.m.Y H:i'),
                'code' => $err_code,
                'message' => $err_message,
                'line' => $line,
                'file' => $file
            ];
            $this->send = false;
            return $error;

        }

        
        
    }

    /**
     * update table
     * @param string $table table name
     * @param array $columns columns to update
     * @param array $newData new data to set
     * IMPORTANT! $columns and $newData must be the same size
     * @param string $col_value select specific columns exp(id = 1)
     */
    public function update(string $table, array $columns, array $newData, string $col_value = null){
        try{
            if(count($columns) != count($newData)) throw new Exception('array $columns is greater/less than array $newData', 9910); 

            $colss = '';
            for($i = 0;  $i < count($columns); $i++){
                $i + 1 == count($columns) ? $colss .= "`{$columns[$i]}` = '{$newData[$i]}'" : $colss .= "`{$columns[$i]}` = '{$newData[$i]}',";
            }

            if(!empty($col_value)){
                $colss .= "WHERE $col_value";
            }
            

            $sql = "UPDATE `$table` SET $colss";
            
                $this->conect()->exec($sql);
                $this->send = true;
        }catch(Exception $err){
            $err_code = $err->getCode();
            $err_message = $err->getMessage();
            $line = debug_backtrace()[0]['line'];
            $file = debug_backtrace()[0]['file'];
            $error = [
                'time' => date('d.m.Y H:i'),
                'code' => $err_code,
                'message' => $err_message,
                'line' => $line,
                'file' => $file
            ];
            $this->send = false;
            return $error;
        }
    }

    public function delete(string $from, string $column = NULL){
        try{
            $sql = "DELETE FROM `{$from}`";

            if($column != NULL){
                $sql .= "WHERE $column";
            }

            $this->conect()->exec($sql);
        }catch(PDOException $err){
            $err_code = $err->getCode();
            $err_message = $err->getMessage();
            $line = debug_backtrace()[0]['line'];
            $file = debug_backtrace()[0]['file'];
            $error = [
                'time' => 'time: ' . date('d.m.Y H:i'),
                'code' => 'code: ' . $err_code,
                'message' => 'message' . $err_message,
                'line' => 'line: ' . $line,
                'file' => 'file: ' . $file
            ];
            $this->send = false;
            return $error;
        }
    }


    /**
     * Check table items
     * @param string $table table name
     * @param string $data  data to be cheked
     * @param string $cla_value specific columns exp(id = 1)
     * @return boolean if table has elements return true. else return false
     */
    public function check(string $table, string $data, string $col_value = null): bool{
        try{
            $col_value != null ? $table .= " WHERE $col_value" : "";
            $data = $this->select($table, $data);
            $this->send = true;
            $hasItems = false;
            
            empty($data) ? $hasItems = false : $hasItems =  true;
            return $hasItems;
        }catch(PDOException $err){
            $err_code = $err->getCode();
            $err_message = $err->getMessage();
            $line = debug_backtrace()[0]['line'];
            $file = debug_backtrace()[0]['file'];
            $error = [
                'time' => date('d.m.Y H:i'),
                'code' => $err_code,
                'message' => $err_message,
                'line' => $line,
                'file' => $file
            ];
            $this->send = false;
            return $error;
        }
    }
    
      
    /**
     * ctrate table
     * @param  string $table_name table name
     * @param  array $columns columns exp ['collumn_1' => ['INT', 'NOT NULL', 'PRIMARY KEY', 'AUTO_INCREMENT'], 'collumn_2' => ['varchar(255)']]
     */
    public function createTable(string $table_name, array $columns){
        $a = ['name' => ['type', 'null', 'keys']];
        $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (";
        try{
            $itter = 0;
            foreach($columns as $key => $value){
                $sql .= "`$key` ";
                
                for($i = 0; $i < count($value); $i++){
                    $sql .= $value[$i]. " ";
                }
                $itter++;
                $itter == count($columns) ? $sql .= '' : $sql .= ', ';
                echo $itter .'<br>';
                echo count($columns) .'<br>';

            }
            $sql .= ")";
            echo $sql.'<br>';
            $this->conect()->exec($sql);
            $this->send = true;
        }catch(PDOException $err){
            $err_code = $err->getCode();
            $err_message = $err->getMessage();
            $line = debug_backtrace()[0]['line'];
            $file = debug_backtrace()[0]['file'];
            $error = [
                'time' => date('d.m.Y H:i'),
                'code' => $err_code,
                'message' => $err_message,
                'line' => $line,
                'file' => $file
            ];
            $this->send = false;
            return $error;
        }
    }

    /**
     * create a log file
     */
    public function crateLog(array $data){
        $log = implode(" | ", $data);
        file_put_contents($this->path, $log."\n", FILE_APPEND);
    }
}
    
?>
