CREATE DATABASE IF NOT EXISTS library_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_db;

DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS genres;
DROP TABLE IF EXISTS users;

CREATE TABLE genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    genre_id INT,
    author VARCHAR(255),
    description TEXT,
    cover VARCHAR(500),
    year INT,
    pages INT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (genre_id) REFERENCES genres(id)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    book_id INT,
    booking_date DATETIME,
    status VARCHAR(50) DEFAULT 'Новая',
    reader_name VARCHAR(100),
    reader_phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

INSERT INTO genres (name) VALUES 
('Роман'), ('Детектив'), ('Фантастика'), ('Фэнтези'), ('Научная литература'),
('Поэзия'), ('Биография'), ('История'), ('Психология'), ('Приключения');

INSERT INTO books (title, genre_id, author, description, cover, year, pages) VALUES
('Война и мир', 1, 'Лев Толстой', 'Роман-эпопея о русском обществе в эпоху войн против Наполеона.', 'images/book1.jpg', 1869, 1225),
('Преступление и наказание', 2, 'Фёдор Достоевский', 'Психологический детектив о студенте, совершившем убийство.', 'images/book2.jpg', 1866, 672),
('451 градус по Фаренгейту', 3, 'Рэй Брэдбери', 'Антиутопия о будущем, где книги запрещены и сжигаются.', 'images/book3.jpg', 1953, 256),
('Гарри Поттер и философский камень', 4, 'Джоан Роулинг', 'Первая книга о юном волшебнике Гарри Поттере.', 'images/book4.jpg', 1997, 432),
('Краткая история времени', 5, 'Стивен Хокинг', 'Научно-популярная книга о космологии и чёрных дырах.', 'images/book5.jpg', 1988, 256),
('Евгений Онегин', 6, 'Александр Пушкин', 'Роман в стихах о любви и русской жизни XIX века.', 'images/book6.jpg', 1833, 224),
('Стив Джобс', 7, 'Уолтер Айзексон', 'Биография основателя Apple.', 'images/book7.jpg', 2011, 688),
('История России', 8, 'Николай Карамзин', 'Фундаментальный труд по истории Российского государства.', 'images/book8.jpg', 1818, 1024),
('Думай медленно, решай быстро', 9, 'Даниэль Канеман', 'Книга о психологии мышления и принятии решений.', 'images/book9.jpg', 2011, 480),
('Остров сокровищ', 10, 'Роберт Стивенсон', 'Приключенческий роман о поиске пиратских сокровищ.', 'images/book10.jpg', 1883, 304);

INSERT INTO users (name, email, password, is_admin) VALUES
('Библиотекарь', 'admin@library.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
