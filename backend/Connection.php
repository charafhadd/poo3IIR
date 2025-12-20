<?php

class Connection
{
    private $servername = "localhost:3307";
    private $username = "root";
    private $password = "";
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli(
            $this->servername,
            $this->username,
            $this->password
        );

        if ($this->conn->connect_error) {
            die("Erreur connexion DB : " . $this->conn->connect_error);
        }
    }

    public function getConn()
    {
        return $this->conn;
    }

    public function createDatabase($dbName)
    {
        $sql = "CREATE DATABASE IF NOT EXISTS $dbName";
        return $this->conn->query($sql);
    }

    public function selectDatabase($dbName)
    {
        return $this->conn->select_db($dbName);
    }

    public function createTable($query)
    {
        return $this->conn->query($query);
    }
}