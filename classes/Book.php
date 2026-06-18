<?php
class Book {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll($genre_id = null, $search = null) {
        $sql = "SELECT b.*, g.name as genre_name 
                FROM books b 
                LEFT JOIN genres g ON b.genre_id = g.id 
                WHERE b.is_active = 1";
        $params = [];
        
        if ($genre_id) {
            $sql .= " AND b.genre_id = :genre_id";
            $params[':genre_id'] = $genre_id;
        }
        if ($search) {
            $sql .= " AND b.title LIKE :search";
            $params[':search'] = "%$search%";
        }
        
        $sql .= " ORDER BY b.title ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT b.*, g.name as genre_name 
             FROM books b 
             LEFT JOIN genres g ON b.genre_id = g.id 
             WHERE b.id = :id AND b.is_active = 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function getGenres() {
        $stmt = $this->conn->query("SELECT * FROM genres ORDER BY name");
        return $stmt->fetchAll();
    }
    
    public function add($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO books (title, genre_id, author, description, cover, year, pages) 
             VALUES (:title, :genre_id, :author, :description, :cover, :year, :pages)"
        );
        return $stmt->execute([
            ':title' => $data['title'],
            ':genre_id' => $data['genre_id'],
            ':author' => $data['author'],
            ':description' => $data['description'],
            ':cover' => $data['cover'],
            ':year' => $data['year'],
            ':pages' => $data['pages']
        ]);
    }
}
