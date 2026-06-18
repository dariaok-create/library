<?php
session_start();
$bookingCount = isset($_SESSION['bookings']) ? count($_SESSION['bookings']) : 0;
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас — Библиотека</title>
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
                <a href="about.php">О нас</a>
                <a href="contacts.php">Контакты</a>
                <a href="bookings.php">Брони (<?php echo $bookingCount; ?>)</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php">Профиль</a>
                <?php else: ?>
                    <a href="login.php">Войти</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <h1>О нас</h1>
        <div class="auth-form" style="max-width: 800px; text-align: center;">
            <p style="font-size: 16px; line-height: 2; color: #6b4d3a;">
                Библиотека — это современное книжное пространство с богатым фондом.
                У нас собраны тысячи книг разных жанров — от классики до современной литературы.
                Мы предлагаем удобное онлайн-бронирование книг. Чтение — наш главный приоритет.
            </p>
            <div class="advantages" style="margin-top: 40px;">
                <div class="advantage-item">
                    <h4>Богатый фонд</h4>
                    <p>Более 10 000 книг</p>
                </div>
                <div class="advantage-item">
                    <h4>Удобный поиск</h4>
                    <p>По жанрам и авторам</p>
                </div>
                <div class="advantage-item">
                    <h4>Бронирование онлайн</h4>
                    <p>Без очередей</p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Библиотека. Все права защищены.</p>
    </footer>
</body>
</html>