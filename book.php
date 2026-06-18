<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Book.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $bookObj = new Book($db);
    
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $book = $bookObj->getById($id);
    
    if (!$book) {
        header('Location: catalog.php');
        exit;
    }
} catch (Exception $e) {
    $error = "Книга не найдена";
}

// Бронирование книги
if (isset($_POST['add_booking'])) {
    $bookingDate = $_POST['booking_date'] . ' ' . $_POST['booking_time'] . ':00';
    
    if (!isset($_SESSION['bookings'])) {
        $_SESSION['bookings'] = [];
    }
    
    $_SESSION['bookings'][] = [
        'book_id' => $book['id'],
        'book_title' => $book['title'],
        'author' => $book['author'],
        'booking_date' => $bookingDate,
        'genre' => $book['genre_name']
    ];
    
    $success = "Книга забронирована!";
}

$bookingCount = isset($_SESSION['bookings']) ? count($_SESSION['bookings']) : 0;
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $book['title']; ?> — Библиотека</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <nav>
            <a href="index.php" class="logo">
                <img src="images/logotip.png" alt="Библиотека" class="logo-img">
                Библиотека
            </a>
            <div>
                <a href="index.php">Главная</a>
                <a href="catalog.php">Книги</a>
                <a href="bookings.php">Брони (<?php echo $bookingCount; ?>)</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php">Профиль</a>
                    <a href="logout.php">Выйти</a>
                <?php else: ?>
                    <a href="login.php">Войти</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <a href="catalog.php" class="back-link">← Назад к каталогу книг</a>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php else: ?>
            <div class="product-detail">
                <div class="product-image">
                    <img src="<?php echo $book['cover']; ?>" alt="<?php echo $book['title']; ?>">
                </div>
                <div class="product-info">
                    <p class="product-category"><?php echo $book['genre_name']; ?></p>
                    <h1><?php echo $book['title']; ?></h1>
                    <p class="product-price">Автор: <?php echo $book['author']; ?></p>
                    <p class="product-color">Год издания: <?php echo $book['year']; ?></p>
                    <p class="product-color">Страниц: <?php echo $book['pages']; ?></p>
                    <p class="product-description"><?php echo $book['description']; ?></p>
                    
                    <?php if (isset($success)): ?>
                        <p class="success-msg">✅ <?php echo $success; ?></p>
                    <?php endif; ?>
                    
                    <?php if ($isLoggedIn): ?>
                    <div class="add-form">
                        <label>Забронировать книгу:</label>
                        <form method="POST" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                            <input type="date" name="booking_date" required 
                                   style="padding:10px 14px; border:2px solid #d4c4a8; border-radius:6px; font-family:'Montserrat',sans-serif;">
                            <select name="booking_time" required
                                    style="padding:10px 14px; border:2px solid #d4c4a8; border-radius:6px; font-family:'Montserrat',sans-serif;">
                                <option value="">— Время —</option>
                                <option value="09:00">09:00</option>
                                <option value="10:00">10:00</option>
                                <option value="11:00">11:00</option>
                                <option value="12:00">12:00</option>
                                <option value="13:00">13:00</option>
                                <option value="14:00">14:00</option>
                                <option value="15:00">15:00</option>
                                <option value="16:00">16:00</option>
                            </select>
                            <button type="submit" name="add_booking" class="btn">Забронировать</button>
                        </form>
                    </div>
                    <?php else: ?>
                        <p style="margin-top:20px; color:#6b4d3a;">Для бронирования книги необходимо <a href="login.php" style="color:#6b3a2a;">войти</a></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2026 Библиотека. Все права защищены.</p>
    </footer>
</body>
</html>