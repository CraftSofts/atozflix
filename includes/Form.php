<?php
class Form {
    function post($name) {
        // get a value of a post
        $var = $_POST[$name];
        return $this->trimVar($var);
    }
    function get($name) {
        // get a value of a get
        $var = $_GET[$name];
        return $this->trimVar($var);
    }
    function request($name) {
         // get a value of any form method (get/post)
         $var = $_REQUEST[$name];
         return $this->trimVar($var);
    }
    function trimVar($var) {
        // trim a variable
        $var = trim($var);
        return $var;
    }
    function isExists($name,$type='') {
        // check if a $var has been set
        if(isset($_POST[$name])) {
            return true;
        } else {
            return false;
        }
    }
    function method() {
        return $_SERVER['REQUEST_METHOD'];
    }
}
?>