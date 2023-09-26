# Hello! I was bored, and I decided to make life a little easier for PHP developers.

**I wrote a script that makes it easier to work with the database.**

**And so let's get acquainted with the main methods of the MYSQLDB class:**

### 1. Create connection `$db = new MYSQLDB('host', 'database', 'username', 'password');`.

### 2. Select `$db->select(string $from, string $columns);`.
        $db = new MYSQLDB('localhost', 'test', 'root', '');
        $users = $db->select('users', '*');
        var_dump($users); 
        /* array { [0]=> array(4) { ["id"]=>"1" ["username"]=> "user name №1" ["password"]=> "user pass №1" ["email"]=>  "user email №1" }, ... }*/

### 3. Select a specific column `$db->select(string $from WHERE $param, string $columns);`.
        $user_1 = $db->select('users WHERE users.id = 1', 'email');
        $user_2 = $db->select('users WHERE users.id = 2', 'email');
        var_dump($user_1);
        var_dump($user_2);
        /*
        array(1) { [0]=> array(1) { ["email"]=> "user email №1" } }
        array(1) { [0]=> array(1) { ["email"]=> "user email №2" } }
        */

### 4. Select columns info `$db->selectColmnsInfo(string $table_name);`.
    $users_table = $db->selectColmnsInfo('users');
    var_dump($users_table);
    /*
    array(4) { 
        [0]=> array(6) { ["Field"]=> "id" ["Type"]=> "int" ["Null"]=> "NO" ["Key"]=> "PRI" ["Default"]=> NULL ["Extra"]=> "auto_increment" } 

        [1]=> array(5) { ["Field"]=> "username" ["Type"]=> "varchar(128)" ["Null"]=> "NO" ["Key"]=> "UNI" ["Default"]=> NULL } 

        [2]=> array(4) { ["Field"]=> "password" ["Type"]=> "varchar(128)" ["Null"]=> "NO" ["Key"]=> "" } 

        [3]=> array(4) { ["Field"]=> "email" ["Type"]=> "varchar(256)" ["Null"]=> "NO" ["Key"]=> "" } 
    }
    */
    ```

### 5. Select database information schema `$db->selectDbInfo(string $items, string $database, string $table, string $column = NULL)`
    $db_users_email = $db->selectDbInfo('*', 'test', 'users', 'email');
    var_dump($db_users_email);

    $db_users_columns_name = $db->selectDbInfo('COLUMN_NAME', 'test', 'users');
    var_dump($db_users_columns_name);
    /*
    array(1) { [0]=> array(14) { 
        ["TABLE_CATALOG"]=>  "def" 
        ["TABLE_SCHEMA"]=> "test" 
        ["TABLE_NAME"]=> "users" 
        ["COLUMN_NAME"]=> "email" 
        ["ORDINAL_POSITION"]=> "4" 
        ["COLUMN_DEFAULT"]=> NULL 
        ["IS_NULLABLE"]=> "NO" 
        ["DATA_TYPE"]=> "varchar" 
        ["CHARACTER_MAXIMUM_LENGTH"]=> "256" 
        ["CHARACTER_OCTET_LENGTH"]=> "1024" 
        ["CHARACTER_SET_NAME"]=> "utf8mb4" 
        ["COLLATION_NAME"]=> "utf8mb4_0900_ai_ci" 
        ["COLUMN_TYPE"]=> "varchar(256)" 
        ["PRIVILEGES"]=> "select,insert,update,references" } 
    }

    array(4) { 
        [0]=> array(1) { ["COLUMN_NAME"]=> "email" } 
        [1]=> array(1) { ["COLUMN_NAME"]=> "id" } 
        [2]=> array(1) { ["COLUMN_NAME"]=> "password" } 
        [3]=> array(1) { ["COLUMN_NAME"]=> "username" } 
    }
    */

### 6. Add items `$db->add(string $table_name, array $data)`.
   **IMPORTANT! The size of $data should match the size of your table columns.**
   **Required in column order if value p-key or null set `NULL` / `''`.**

    $newUser = [NULL, 'user name №3', 'user pass №3', 'user email №3'];
    $add_user = $db->add('users', $newUser);

    if($db->send) echo 'ok';
    else echo $add_user['message'];
### 7. Update items `$db->update(string $table, array $columns, array $newData, string $col_value = null)`.
   **IMPORTANT! `$columns` and `$newData` must be the same size** 

    $email = ['exemple@email.com'];
    $update_user_email = $db->update('users', ['email'], $email, 'id = 2');

    $email_pass = ['exemple@email.com', 'new_password']; 
    $update_user_email = $db->update('users', ['email', 'password'], $email_pass);

    if($db->send) echo 'ok';
    else echo $add_user['message'];


### 8. Delete items `$db->delete(string $from, string $column = NULL);`.
   **If the `$column` is empty, all data will be deleted.**

    $delete_user = $db->delete('users', 'id = 5');
    $delete_table_data = $db->delete('user');

    if($db->send) echo 'ok';
    else echo $add_user['message']; 
### 9. Crtate table `$db->crateTable(string $table_name, array $columns);`.
    $table_name = 'my_new_table';
    $colls = [
        'coll_1' => ['INT', 'NOT NULL', 'PRIMARY KEY', 'AUTO_INCREMENT'],
        'coll_2' => ['INT', 'NOT NULL', 'UNIQUE'],
        'coll_3' => ['varchar(128)', 'NOT NULL'],
        'coll_4' => ['varchar(512)'] 
    ]
    $db->crateTable($table_name, $colls);

    if($db->send) echo 'ok';
    else echo $add_user['message'];
### 10. Error processing.   
   **Id in my table is primary key and id 2 is already taken**
    
           id(p-key)       username      password     email
    $new_user = [2,     'user №4 name',    '',     'user №4 email'];
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

 ### 11. Check table items   

    $a = $db->check('users', '*');

    if($a){
        echo "var $.a = the table 'users' has elements";
    }else{
        echo "var $.a = the table 'users' has't elements";
    }

    $b = $db->check('users', '*', 'id = 5');
    if($b){
        echo "var $.b = the table 'users' where id = 5 has elements";
    }else{
        echo "var $.b = the table 'users' where id = 5 has't elements";
    }


    /* 
    RESULT
    var $.a = the table 'users' has elements

    var $.b = the table 'users' where id = 5 has't elements
    */

 ### 12. Create log file
    //You can save either a ready-made array or a homemade one
    $a = $db->select('asd', 'dsa'); //in my db dosen't have table 'asd'
    if(!$db->send){
        $db->path = __DIR__.'./error.log';
        $db->crateLog($a);

        //or

        $a['exemple data'] = 'exemple data';
        $db->crateLog($a);

        //or

        $array = [1, 2, 3];
        $db->crateLog($array);
    }


    /*
    RESULT
    26.09.2023 15:42 | 42S02 | SQLSTATE[42S02]: Base table or view not found: 1146 Table 'test.asd' doesn't exist | 248 | D:\OSPanel\domains\db_php\exemple.php
    26.09.2023 15:42 | 42S02 | SQLSTATE[42S02]: Base table or view not found: 1146 Table 'test.asd' doesn't exist | 248 | D:\OSPanel\domains\db_php\exemple.php | exemple data
    1 | 2 | 3

    */   