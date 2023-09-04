<? 
include_once __DIR__.'/db.php';

#set connection
$db = new MYSQLDB('localhost', 'test', 'root', '');

/*⸻⸻⸻⸻⸻⸻⸻
    exemple №1 - get all column from db
⸻⸻⸻⸻⸻⸻⸻*/

$ex_1 = $db->select('users', '*');
for($i = 0; $i < count($ex_1); $i++){
    echo "⸺⸺⸺<br>user №$i <br> ⸺⸺⸺ <br>";
    foreach($ex_1[$i] as $key => $value){
        echo $key . ': ' . $value . '<br>';
    }
}


#RESULT
/*
⸺⸺⸺
user №0
⸺⸺⸺
id: 1
username: user №1 name
password: user №1 password
email: user №1 email
⸺⸺⸺
user №1
⸺⸺⸺
id: 2
username: user №2 name
password: user №2 password
email: user №2 email
⸺⸺⸺
user №2
⸺⸺⸺
id: 3
username: user №3 name
password: user №3 password
email: user №3 email

⸻⸻⸻⸻⸻⸻⸻
    exemple №1.1 - get specific columns
⸻⸻⸻⸻⸻⸻⸻
*/

$ex_2 = $db->select('users', 'username');
for($i = 0; $i < count($ex_2); $i++){
    echo "⸺⸺⸺<br>user №$i <br> ⸺⸺⸺ <br>";
    foreach($ex_2[$i] as $key => $value){
        echo $key . ': ' . $value . '<br>';
    }
}

/*
RESULT
⸺⸺⸺
user №0
⸺⸺⸺
username: user №1 name
⸺⸺⸺
user №1
⸺⸺⸺
username: user №2 name
⸺⸺⸺
user №2
⸺⸺⸺
username: user №3 name

/*⸻⸻⸻⸻⸻⸻⸻
    exemple №1.2 - get specific columns by key
⸻⸻⸻⸻⸻⸻⸻*/

$ex_3 = $db->select('users WHERE users.id = 1', '*');
foreach($ex_3[0] as $key => $value){
    echo $key . ': ' . $value . '<br>';
}

#RESULT
/*
id: 1
username: user №1 name
password: user №1 password
email: user №1 email


/*⸻⸻⸻⸻⸻⸻⸻
    exemple №2 - add items in db         
⸻⸻⸻⸻⸻⸻⸻*/
#IMPORTANT! The size of $data should match the size of your table columns.
#Required in column order if value p-key or null set NULL / '' 

//        id(p-key)       username      password     email
$new_user = [NULL,     'user №4 name',    '150',     'user №4 email'];
$add = $db->add('users', $new_user);

/*⸻⸻⸻⸻⸻⸻⸻
    exemple №3 - get information schema  
⸻⸻⸻⸻⸻⸻⸻*/
$information_schema = $db->selectDbInfo('*', 'test', 'users', 'username');
foreach($information_schema[0] as $key => $value){
    echo $key . ": " . $value . '<br>';
}

#RESULT
/*
TABLE_CATALOG: def
TABLE_SCHEMA: test
TABLE_NAME: users
COLUMN_NAME: username
ORDINAL_POSITION: 2
COLUMN_DEFAULT:
IS_NULLABLE: NO
DATA_TYPE: varchar
CHARACTER_MAXIMUM_LENGTH: 128
CHARACTER_OCTET_LENGTH: 512
CHARACTER_SET_NAME: utf8mb4
COLLATION_NAME: utf8mb4_0900_ai_ci
COLUMN_TYPE: varchar(128)
PRIVILEGES: select,insert,update,references

/*⸻⸻⸻⸻⸻⸻⸻
    exemple №4 - get columns info       
⸻⸻⸻⸻⸻⸻⸻*/

$columns = $db->selectColmnsInfo('users');
foreach($columns as $key){
    echo 'name: '. $key['Field'] . '<br>';
    echo 'type: ' .$key['Type'] . '<br>';
    echo 'key:  ' .$key['Key'] . '<br>';
    echo 'Null: ' .$key['Null'] . '<br>';
    
}

#RESULT
/*
name: id
type: int
key: PRI
Null: NO

name: username
type: varchar(128)
key:
Null: NO

name: password
type: varchar(128)
key:
Null: NO

name: email
type: varchar(256)
key:
Null: NO


/*⸻⸻⸻⸻⸻⸻⸻
    exemple №5 - update table        
⸻⸻⸻⸻⸻⸻⸻*/

#IMPORTANT! $columns and $newData must be the same size

$db->update('users', ['password'], ['5448']);
$db->update('users', ['password'], ['5448'], 'id = 1');


/*⸻⸻⸻⸻⸻⸻⸻
    exemple №6 - delete from table     
⸻⸻⸻⸻⸻⸻⸻*/

$db->delete('users');
$db->delete('users', 'id = 5');

/*⸻⸻⸻⸻⸻⸻⸻
    exemple №7 - crate table     
⸻⸻⸻⸻⸻⸻⸻*/
$table_name = 'person';
$table_colss = [
    'id' => ['INT', 'NOT NULL', 'PRIMARY KEY', 'AUTO_INCREMENT'], 
    'name' => ['varchar(128)', 'NOT NULL'], 
    'tel' => ['INT', 'NOT NULL', 'UNIQUE']
];

$db->createTable($table_name, $table_colss);

/*⸻⸻⸻⸻⸻⸻⸻
    exemple №8 - error processing        
⸻⸻⸻⸻⸻⸻⸻*/

#id in my table is primary key and id 2 is already taken

#        id(p-key)       username      password     email
$new_user = [3,     'user №4 name',    '',     'user №4 email'];
$add = $db->add('users', $new_user);

#if the send fails, get an array with errors
if(!$db->send){
    //var_dump($add)
    echo 'err_code: '   .$add['code'].   '<br>'; //get error code
    echo 'err_message: '.$add['message'].'<br>'; //get error message
    echo 'err_file: '   .$add['file'].   '<br>'; //get file with error
    echo 'err_line: '   .$add['line'].   '<br>'; //get line with error

    /*
    RESULT
    err_code: 23000
    err_message: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '3' for key 'users.PRIMARY'
    err_file: D:\OSPanel\domains\db_php\exemple.php
    err_line: 96
    */
}

?>