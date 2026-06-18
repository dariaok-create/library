<?php
class Database {
    private $db_file;
    private $conn;
    
    public function __construct() {
        $this->db_file = __DIR__ . '/../data/library.db';
    }
    
    public function getConnection() {
        if ($this->conn !== null) return $this->conn;
        
        try {
            $this->conn = new PDO("sqlite:" . $this->db_file);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("PRAGMA journal_mode=WAL");
            $this->conn->exec("PRAGMA foreign_keys=ON");
            
            // Создаём таблицы, если их нет
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS genres (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL
                )
            ");
            
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS books (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    genre_id INTEGER,
                    author TEXT,
                    description TEXT,
                    cover TEXT,
                    year INTEGER,
                    pages INTEGER,
                    is_active INTEGER DEFAULT 1,
                    FOREIGN KEY (genre_id) REFERENCES genres(id)
                )
            ");
            
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT UNIQUE NOT NULL,
                    password TEXT NOT NULL,
                    phone TEXT,
                    address TEXT,
                    is_admin INTEGER DEFAULT 0
                )
            ");
            
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS bookings (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER,
                    book_id INTEGER,
                    booking_date TEXT,
                    status TEXT DEFAULT 'Новая',
                    reader_name TEXT,
                    reader_phone TEXT,
                    FOREIGN KEY (user_id) REFERENCES users(id),
                    FOREIGN KEY (book_id) REFERENCES books(id)
                )
            ");
            
            // Добавляем начальные данные, если таблицы пустые
            $count = $this->conn->query("SELECT COUNT(*) FROM genres")->fetchColumn();
            if ($count == 0) {
                $this->conn->exec("INSERT INTO genres (name) VALUES 
                    ('Роман'), ('Детектив'), ('Фантастика'), ('Фэнтези'), ('Научная литература'),
                    ('Поэзия'), ('Биография'), ('История'), ('Психология'), ('Приключения')
                ");
                
                $this->conn->exec("INSERT INTO books (title, genre_id, author, description, cover, year, pages) VALUES 
                    ('Война и мир', 1, 'Лев Толстой', 'Роман-эпопея о русском обществе в эпоху войн против Наполеона.', 'images/book1.jpg', 1869, 1225),
                    ('Преступление и наказание', 2, 'Фёдор Достоевский', 'Психологический детектив о студенте, совершившем убийство.', 'images/book2.jpg', 1866, 672),
                    ('451 градус по Фаренгейту', 3, 'Рэй Брэдбери', 'Антиутопия о будущем, где книги запрещены.', 'images/book3.jpg', 1953, 256),
                    ('Гарри Поттер и философский камень', 4, 'Джоан Роулинг', 'Первая книга о юном волшебнике.', 'images/book4.jpg', 1997, 432),
                    ('Краткая история времени', 5, 'Стивен Хокинг', 'Научно-популярная книга о космологии.', 'images/book5.jpg', 1988, 256)
                ");
                
                // Админ: admin@library.ru / password
                $this->conn->exec("INSERT INTO users (name, email, password, is_admin) VALUES 
                    ('Библиотекарь', 'admin@library.ru', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1)
                ");
            }
            
        } catch(PDOException $e) {
            throw new Exception("Ошибка подключения к базе данных: " . $e->getMessage());
        }
        return $this->conn;
    }
}
