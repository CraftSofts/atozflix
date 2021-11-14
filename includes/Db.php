<?php
class Db {
    private $connection;
    private $servername;
    private $username;
    private $password;
    public $error;
    function __construct($servername,$username,$password='',$dbname='') {
        $conn = mysqli_connect($servername, $username, $password);
        if (!$conn) {
            $this->error = "Connection failed: " . $conn->connect_error;
            die("Server is overloaded, please try again by refreshing this page.". $this->error);
        }
        if(!empty($dbname)) {
            $conn->select_db($dbname);
        }
            mysqli_set_charset($conn,"utf8");
            $this->connection = $conn;
    }

    function createDb($name) {
        $sql = "CREATE DATABASE $name";
        if (mysqli_query($this->connection, $sql)) {
            return $this->result();
        } else {
            return $this->result(0,"Error creating database: " . mysqli_error($this->connection));
        }
    }

    function deleteDb($name) {
        $sql = "DROP DATABASE $name";
        if (mysqli_query($this->connection, $sql)) {
            return $this->result();
        } else {
            return $this->result(0,"Error deleting database: " . mysqli_error($this->connection));
        }
    }

    function createTable($sql) {
        if (mysqli_query($this->connection,$sql)) {
            return $this->result();
        } else {
            return $this->result(0,"Error creating table: " . mysqli_error($this->connection));
        }
    }

    function deleteTable($name) {
        $sql = 'DROP TABLE '.$name.'';
        if (mysqli_query($this->connection, $sql)) {
            return $this->result();
        } else {
            return $this->result(0,"Error deleting table: " . mysqli_error($this->connection));
        }
    }

    function alterTable($name,$sql) {
        $sql = 'ALTER TABLE '.$name.' '.$sql.'';
        if (mysqli_query($this->connection, $sql)) {
            return $this->result();
        } else {
            return $this->result(0,"Error altering table: " . mysqli_error($this->connection));
        }
    }

    function insertRow($table,$column,$values) {
        for($i = 0; $i < count($column); $i++) {
            if($i == count($column)-1) {
                $params .= $column[$i];
            } else {
                $params .= "$column[$i], ";
            }
        }
        for($i = 0; $i < count($values); $i++) {
            if($i == count($values)-1) {
                $value .= "'$values[$i]'";
            } else {
                $value .= "'$values[$i]', ";
            }
        }
        $sql = 'INSERT INTO '.$table.' ('.$params.') VALUES ('.$value.')';
        if (mysqli_query($this->connection, $sql)) {
            $last_id = mysqli_insert_id($this->connection);
            return $this->result(1,$last_id);
        } else {
            return $this->result(0,"Error inserting record: ".mysqli_error($this->connection));
        }

    }

    function deleteRow($table,$param,$value) {
        $sql = "DELETE FROM $table WHERE $param='".$this->escapeString($value)."' LIMIT 1";
        if (mysqli_query($this->connection, $sql)) {
            return $this->result();
        } else {
            return $this->result(0,"Error deleting record: ".mysqli_error($this->connection));
        }
    }

    function updateRow($table,$param,$value,$data) {
        $items = count($data);
        $update_data = '';
        $i = 0;
        foreach($data as $key => $val) {
            if(++$i === $items) {
                $update_data .= ''.$key.'="'.$this->escapeString($val).'"';
            } else {
                $update_data .= ''.$key.'="'.$this->escapeString($val).'", ';
            }
        }
        $sql = "UPDATE $table SET $update_data WHERE $param='$value'";
        if (mysqli_query($this->connection, $sql)) {
            return $this->result();
        } else {
            return $this->result(0,"Error updating record: " . mysqli_error($this->connection));
        }
    }

    function selectRow($table,$param,$value,$extra='') {
        $sql = 'SELECT * FROM '.$table.' WHERE '.$param.'="'.$this->escapeString($value).'"'.$extra.'';
        $result = mysqli_query($this->connection, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row=mysqli_fetch_object($result);
            return $this->result(1,$row);
        } else {
            return $this->result(0,"Error selecting record: " . mysqli_error($this->connection));
        }
    }

    function selectRows($table,$extra='') {
        $sql = "SELECT * FROM $table$extra";
        $result = mysqli_query($this->connection, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_object($result)) {
                if($row) $data[] = $row;
            }
            return $this->result(1,$data);
        } else {
            return $this->result(0,"Error selecting records: " . mysqli_error($this->connection));
        }
    }

    function countRows($sql) {
        $count=mysqli_query($this->connection,$sql);
        $result = 0;
        $result= mysqli_num_rows($count);
        //if(is_null($result)) $result = 0;
        //echo mysqli_error($this->connection);
        //return $this->result(1,$result);
        return $result;
        mysqli_free_result($result);
    }

    function sqlQuery($sql) {
        $result = mysqli_query($this->connection, $sql);
        if (mysqli_num_rows($result) > 0) {
            return $this->result();
        } else {
            return $this->result(0,"Error executing SQL: " . mysqli_error($this->connection));
        }
        
    }
	
    function escapeString($string) {
        $string = mysqli_real_escape_string($this->connection,$string);
        return $string;
    }

    function result($result=1,$reason='') {
        if($result==1) {
            if(empty($reason)) {
                return array('result'=>'success');
            } else {
                return array('result'=>'success','data'=>(array)$reason);
            }
        } else {
            return array('result'=>'failed','reason'=>$reason);
        }
    }
    
    function __destruct() {
        mysqli_close($this->connection);
    }
}