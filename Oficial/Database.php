<?php

class Database {
    private $host;
    private $database;
    private $user;
    private $password;

    public function __construct() {
        $this->host = 'localhost';
        $this->database = 'db_HealthComply_Innovations_User';
        $this->user = 'root';
        $this->password = '';
    }

    public function connect() {
        try {
            $conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->user, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
}
?>