<?php
class Booking {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create($userId, $bookId, $bookingDate, $readerName, $readerPhone) {
        $stmt = $this->conn->prepare(
            "INSERT INTO bookings (user_id, book_id, booking_date, reader_name, reader_phone) 
             VALUES (:user_id, :book_id, :booking_date, :reader_name, :reader_phone)"
        );
        return $stmt->execute([
            ':user_id' => $userId,
            ':book_id' => $bookId,
            ':booking_date' => $bookingDate,
            ':reader_name' => $readerName,
            ':reader_phone' => $readerPhone
        ]);
    }
    
    public function getByUser($userId) {
        $stmt = $this->conn->prepare(
            "SELECT b.*, bk.title as book_title, g.name as genre_name 
             FROM bookings b 
             JOIN books bk ON b.book_id = bk.id 
             JOIN genres g ON bk.genre_id = g.id 
             WHERE b.user_id = :user_id 
             ORDER BY b.booking_date DESC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function getAll() {
        $stmt = $this->conn->query(
            "SELECT b.*, bk.title as book_title, g.name as genre_name, u.name as user_name 
             FROM bookings b 
             JOIN books bk ON b.book_id = bk.id 
             JOIN genres g ON bk.genre_id = g.id 
             JOIN users u ON b.user_id = u.id 
             ORDER BY b.booking_date DESC"
        );
        return $stmt->fetchAll();
    }
}
