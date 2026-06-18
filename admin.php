<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Book.php';

// Только для библиотекаря
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

try {
    $database = new Database();
    $db = $database->getConnection();
    $book = new Book($db);
    $genres = $book->getGenres();
    $allBooks = $book->getAll(null, null);
} catch (Exception $e) {
    $genres = [];
    $allBooks = [];
}

// Добавление книги
if (isset($_POST['add_book'])) {
    $title = trim($_POST['title']);
    $genre_id = (int)$_POST['genre_id'];
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $cover = trim($_POST['cover']);
    $year = trim($_POST['year']);
    $pages = trim($_POST['pages']);
    
    if (empty($title) || empty($genre_id)) {
        $error = 'Заполните обязательные поля';
    } else {
        $result = $book->add([
            'title' => $title,
            'genre_id' => $genre_id,
            'author' => $author,
            'description' => $description,
            'cover' => $cover ?: 'images/book1.jpg',
            'year' => $year,
            'pages' => $pages
        ]);
        
        if ($result) {
            $success = "Книга «{$title}» добавлена!";
            $allBooks = $book->getAll(null, null);
        } else {
            $error = 'Ошибка при добавлении';
        }
    }
}

// Удаление книги
if (isset($_POST['delete_book'])) {
    $deleteId = (int)$_POST['book_id'];
    
    try {
        $stmt = $db->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute([':id' => $deleteId]);
        $success = "Книга удалена!";
        $allBooks = $book->getAll(null, null);
    } catch (Exception $e) {
        $error = 'Ошибка при удалении';
    }
}

$bookingCount = isset($_SESSION['bookings']) ? count($_SESSION['bookings']) : 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Библиотекарь — Библиотека</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .admin-container {
        max-width: 900px;
        margin: 40px auto;
        background: #fff;
        padding: 40px;
        border-radius: 8px;
        border: 1px solid #d4c4a8;
        box-shadow: 0 4px 20px rgba(74, 44, 26, 0.1);
    }
    
    .admin-container h1, .admin-container h2 {
        text-align: center;
        font-family: 'Cormorant Garamond', serif;
        color: #4a2c1a;
        margin-bottom: 30px;
    }
    
    .admin-container h1::after, .admin-container h2::after { display: none; }
    
    .admin-form .form-group { margin-bottom: 16px; }
    
    .admin-form label {
        display: block;
        margin-bottom: 5px;
        font-size: 11px;
        font-weight: 500;
        color: #6b4d3a;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }
    
    .admin-form input,
    .admin-form select,
    .admin-form textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #d4c4a8;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Montserrat', sans-serif;
        background: #fdfaf5;
        outline: none;
    }
    
    .admin-form button {
        width: 100%;
        padding: 14px;
        background: #6b3a2a;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 2px;
        cursor: pointer;
    }
    
    .admin-form button:hover {
        background: #4a2c1a;
    }
    
    .book-list {
        margin-top: 40px;
    }
    
    .book-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 18px;
        border: 1px solid #d4c4a8;
        border-radius: 6px;
        margin-bottom: 8px;
        background: #fdfaf5;
    }
    
    .book-item span {
        font-size: 13px;
        color: #3d2b1f;
    }
    
    .delete-btn {
        background: #e74c3c;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 11px;
        letter-spacing: 1px;
    }
    
    .delete-btn:hover {
        background: #c0392b;
    }
    </style>
</head>
<body>
    <header class="header">
        <nav>
            <a href="index.php" class="logo">
                <img src="images/logotip.png" alt="Библиотека" class="logo-img"> Библиотека
            </a>
            <div>
                <a href="index.php">Главная</a>
                <a href="catalog.php">Книги</a>
                <a href="bookings.php">Брони (<?php echo $bookingCount; ?>)</a>
                <a href="admin.php">⚙️ Библиотекарь</a>
                <a href="logout.php">Выйти</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <h1>Добавить книгу</h1>
            
            <?php if ($success): ?>
                <p class="success-msg">✅ <?php echo $success; ?></p>
            <?php endif; ?>
            <?php if ($error): ?>
                <p class="error">❌ <?php echo $error; ?></p>
            <?php endif; ?>
            
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label>Название книги *</label>
                    <input type="text" name="title" required placeholder="Например: Война и мир">
                </div>
                
                <div class="form-group">
                    <label>Жанр *</label>
                    <select name="genre_id" required>
                        <option value="">— Выберите —</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?php echo $genre['id']; ?>"><?php echo $genre['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Автор</label>
                    <input type="text" name="author" placeholder="Например: Лев Толстой">
                </div>
                
                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="description" placeholder="Опишите книгу..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Обложка (имя файла)</label>
                    <input type="text" name="cover" value="images/book1.jpg">
                </div>
                
                <div class="form-group">
                    <label>Год издания</label>
                    <input type="text" name="year" placeholder="Например: 2024">
                </div>
                
                <div class="form-group">
                    <label>Количество страниц</label>
                    <input type="text" name="pages" placeholder="Например: 350">
                </div>
                
                <button type="submit" name="add_book">Добавить книгу</button>
            </form>
            
            <!-- Список книг с удалением -->
            <div class="book-list">
                <h2>Все книги (<?php echo count($allBooks); ?>)</h2>
                
                <?php foreach ($allBooks as $item): ?>
                <div class="book-item">
                    <span>
                        <strong><?php echo htmlspecialchars($item['title']); ?></strong> 
                        — <?php echo htmlspecialchars($item['author']); ?>
                    </span>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить книгу «<?php echo htmlspecialchars($item['title']); ?>»?')">
                        <input type="hidden" name="book_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" name="delete_book" class="delete-btn">🗑 Удалить</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer><p>&copy; 2026 Библиотека. Все права защищены.</p></footer>
</body>
</html>