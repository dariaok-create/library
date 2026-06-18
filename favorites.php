<?php
session_start();

// Добавление в избранное
if (isset($_POST['add_favorite'])) {
    $bookId = (int)$_POST['book_id'];
    $bookTitle = $_POST['book_title'];
    $bookCover = $_POST['book_cover'];
    $bookAuthor = $_POST['book_author'];
    
    if (!isset($_SESSION['favorites'])) {
        $_SESSION['favorites'] = [];
    }
    
    if (!isset($_SESSION['favorites'][$bookId])) {
        $_SESSION['favorites'][$bookId] = [
            'id' => $bookId,
            'title' => $bookTitle,
            'cover' => $bookCover,
            'author' => $bookAuthor
        ];
    }
    
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'catalog.php'));
    exit;
}

// Удаление из избранного
if (isset($_POST['remove_favorite'])) {
    $bookId = (int)$_POST['book_id'];
    unset($_SESSION['favorites'][$bookId]);
    header('Location: favorites.php');
    exit;
}

$favorites = $_SESSION['favorites'] ?? [];
$favCount = count($favorites);

$bookingCount = isset($_SESSION['bookings']) ? count($_SESSION['bookings']) : 0;
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранное — Библиотека</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .remove-fav-btn {
        display: inline-block;
        background: #6b3a2a;
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .remove-fav-btn:hover {
        background: #4a2c1a;
        transform: translateY(-2px);
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
                <a href="favorites.php">❤️ Избранное (<?php echo $favCount; ?>)</a>
                <a href="about.php">О нас</a>
                <a href="contacts.php">Контакты</a>
                <a href="bookings.php" class="cart-link">Брони (<?php echo $bookingCount; ?>)</a>
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
        <h1>❤️ Избранные книги</h1>
        
        <?php if (empty($favorites)): ?>
            <div style="text-align:center; padding:60px; background:#fff; border-radius:8px; border:1px solid #d4c4a8;">
                <p style="font-size:18px; color:#6b4d3a;">В избранном пока ничего нет</p>
                <a href="catalog.php" class="btn" style="margin-top:20px;">Выбрать книгу</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($favorites as $fav): ?>
                <div class="product-card" style="position: relative;">
                    <img src="<?php echo $fav['cover']; ?>" alt="<?php echo htmlspecialchars($fav['title']); ?>">
                    <h3><?php echo htmlspecialchars($fav['title']); ?></h3>
                    <p class="category"><?php echo $fav['author']; ?></p>
                    <a href="book.php?id=<?php echo $fav['id']; ?>" class="btn" style="margin:0 14px 10px; display:block; text-align:center;">Подробнее</a>
                    <form method="POST" style="text-align:center; margin-bottom:14px;">
                        <input type="hidden" name="book_id" value="<?php echo $fav['id']; ?>">
                        <button type="submit" name="remove_favorite" class="remove-fav-btn">Убрать из избранного</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer><p>&copy; 2026 Библиотека. Все права защищены.</p></footer>
</body>
</html>