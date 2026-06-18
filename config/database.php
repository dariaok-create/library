<?php
class Database {
    private $db_file;
    private $conn;
    
    public function __construct() {
        $this->db_file = __DIR__ . '/../data/library.db';
    }
    
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("sqlite:" . $this->db_file);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("PRAGMA foreign_keys = ON");
        } catch(PDOException $e) {
            throw new Exception("Ошибка подключения к базе данных");
        }
        return $this->conn;
    }
}
