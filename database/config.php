<?php

#Parent class for connection to database
class Database
{
    private $username = "developer";
    private $password = "3comicN*!b.";
    private $host = "localhost";
    private $database = "TripOut";
    protected $conn;

    #Database Constructor
    public function __construct()
    {
        #Connect to database when object is instantiated
        if (!isset($this->conn)) {

            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            #Creates connection to database using MySqli
            if (!$this->conn) {
                #Alerts and aborts if connection to database was unsuccessful
                echo 'Cannot connect to database server';
                exit;
            }
        }

        return $this->connection;
    }
}

class CRUD extends Database
{
    #The purpose of Methods will be described by the function declaration
    public function __construct()
    {
        parent::__construct();
        #Run parent constructor, since it is not implicitly done in PHP
    }

    public function getData($query)
    {
        #Send Queries which return data as an array
        $result = $this->connection->query($query);
        if ($result == false) {
            #If query yields no result, don't return an array
            return false;
        }
        $arr_rows = array(); #Create an array to store results in, each row is a new result
        while ($res = $result->fetch_assoc()) {
            #Loop through results and store each new row in arr_rows array
            $arr_rows[] = $res;
        }
    }

    public function Execute($query)
    {#Executes query (returns boolean)
        $result = $this->connection->query($query);

        if ($result == true) {
            return true;
        } else {
            echo 'Query cannot be executed!';
            return false;
        }

    }
}
/**
 *
 * class Crud extends DbConfig
 * {
 * public function __construct()
 * {
 * parent::__construct();
 * }
 *
 * public function getData($query)
 * {
 * $result = $this->connection->query($query);
 *
 * if ($result == false) {
 * return false;
 * }
 *
 * $rows = array();
 *
 * while ($row = $result->fetch_assoc()) {
 * $rows[] = $row;
 * }
 *
 * return $rows;
 * }
 *
 * public function execute($query)
 * {
 * $result = $this->connection->query($query);
 *
 * if ($result == false) {
 * echo 'Error: cannot execute the command';
 * return false;
 * } else {
 * return true;
 * }
 * }
 *
 * public function delete($id, $table)
 * {
 * $query = "DELETE FROM $table WHERE id = $id";
 *
 * $result = $this->connection->query($query);
 *
 * if ($result == false) {
 * echo 'Error: cannot delete id ' . $id . ' from table ' . $table;
 * return false;
 * } else {
 * return true;
 * }
 * }
 *
 * public function escape_string($value)
 * {
 * return $this->connection->real_escape_string($value);
 * }
 * }
 **/

