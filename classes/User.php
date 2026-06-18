<?php
class User {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function register($data) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $data['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email уже зарегистрирован'];
        }
        
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare(
            "INSERT INTO users (name, email, password, phone) VALUES (:name, :email, :password, :phone)"
        );
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => $hash,
            ':phone' => $data['phone'] ?? ''
        ]);
        
        return ['success' => true, 'user_id' => $this->conn->lastInsertId()];
    }
    
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE users SET name = :name, phone = :phone, address = :address WHERE id = :id"
        );
        return $stmt->execute([
            ':name' => $data['name'],
            ':phone' => $data['phone'],
            ':address' => $data['address'],
            ':id' => $id
        ]);
    }
}
