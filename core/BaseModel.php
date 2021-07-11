<?php

namespace core;

use core\ConnectDB;

abstract class BAseModel
{
    static $table = 'table';

    abstract public function rules() : array;

    public function loadPost()
    {
        if($_SERVER['REQUEST_METHOD']== 'POST')
        {
            $data = $_POST;
            $fields = get_object_vars($this);
            foreach($fields as $key => $field)
            {
                if(isset($data[$key]))
                {
                    $this->{$key} = $data[$key];
                }
            }
            return true;
        }
        return false;
    }

    public function loadGet()
    {
        if($_SERVER['REQUEST_METHOD']== 'GET')
        {
            $data = $_GET;
            $fields = get_object_vars($this);
            foreach($fields as $key => $field)
            {
                if(isset($data[$key]))
                {
                    $this->{$key} = $data[$key];
                }
            }
            return true;
        }
        return false;
    }

    public function validate()
    {
        $error = true;
        $error_messages = '';
        $rules = $this->rules();
        foreach($rules as $key => $fields)
        {
            switch($key){
                case 'email':
                    foreach($fields as $field){
                        if(!filter_var($this->$field,FILTER_VALIDATE_EMAIL)){
                            $error_messages .="$field  is invalid email  <br>";
                            $error = $error && false;
                        }
                    }
                    break;
                case 'required':
                    foreach($fields as $field){
                        if($this->$field == ''){
                            $error_messages .="$field  is required  <br>";
                            $error = $error && false;
                        }
                    }
                    break;
            }
        }

        //boolean ,float , integer

        if(!isset($_SESSION))
        {
             session_start();
        }
        if(!$error){
            $_SESSION['error'] = $error_messages;
        }
        return $error;
    }


    public function save()
    {
        $fields = get_object_vars($this);
        $keys =[];
        $values = [];
        foreach($fields as $key => $value)
        {
            if($value)
            {
                $keys[] = "`$key`";
                $values[] = "`$key`";
            }
        }   
        $conn = ConnectDB::connect();
        $table = static::$table;
        $keys = implode(', ', $keys);
        $values = implode(', ', $values);
        
        $stmt = $conn->prepare("INSERT INTO `$table` ($keys) VALUES ($values)");
        foreach($fields as $key => $value)
        {
            if($value)
            {
                $stmt->bindParam(":$key", $fields[$key]);
            }
        }
        if($stmt->execute()){
            $this->id = $conn->lastInsertId();
            return $this;
        }
        return false;
    }
}