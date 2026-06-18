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
    <title>Контакты — Библиотека</title>
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
        <h1>Контакты</h1>
        <div class="auth-form" style="max-width: 800px;">
            <div style="text-align: center; margin-bottom: 30px; color: #6b4d3a;">
                <p>📞 +7 (999) 123-45-67 — абонемент</p>
                <p>📧 info@library.ru</p>
                <p>📍 г. Ростов-на-Дону, ул. Книжная, 10</p>
                <p>🕐 Пн-Пт: 09:00 – 19:00 | Сб: 10:00 – 16:00</p>
            </div>
            
            <h1>Напишите нам</h1>
            <form method="POST">
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" name="name">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email">
                </div>
                <div class="form-group">
                    <label>Сообщение</label>
                    <textarea name="message"></textarea>
                </div>
                <button type="submit" class="btn" style="width:100%;">Отправить</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Библиотека. Все права защищены.</p>
    </footer>
</body>
</html>