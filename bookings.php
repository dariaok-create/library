<?php
session_start();

if (isset($_POST['remove'])) {
    unset($_SESSION['bookings'][$_POST['key']]);
}

if (isset($_POST['clear'])) {
    $_SESSION['bookings'] = [];
}

if (isset($_POST['checkout']) && isset($_SESSION['user_id'])) {
    require_once 'config/database.php';
    require_once 'classes/Booking.php';
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        $booking = new Booking($db);
        
        foreach ($_SESSION['bookings'] as $item) {
            $booking->create(
                $_SESSION['user_id'],
                $item['book_id'],
                $item['booking_date'],
                $_POST['name'],
                $_POST['phone']
            );
        }
        
        $_SESSION['bookings'] = [];
        $orderSuccess = "Книги успешно забронированы!";
    } catch (Exception $e) {
        $orderError = "Ошибка при бронировании";
    }
}

$bookingItems = $_SESSION['bookings'] ?? [];
$bookingCount = count($bookingItems);
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои бронирования — Библиотека</title>
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
        <h1>Мои бронирования</h1>

        <?php if (isset($orderSuccess)): ?>
            <div class="success-msg">
                <h2>✅ <?php echo $orderSuccess; ?></h2>
                <a href="profile.php" class="btn" style="margin-top: 15px;">Личный кабинет</a>
            </div>
        <?php elseif (empty($bookingItems)): ?>
            <div style="text-align: center; padding: 50px;">
                <p style="font-size: 16px; color: #6b4d3a;">У вас пока нет бронирований</p>
                <a href="catalog.php" class="btn" style="margin-top: 20px;">Выбрать книгу</a>
            </div>
        <?php else: ?>
            <?php foreach ($bookingItems as $key => $item): ?>
            <div class="cart-item">
                <div style="flex: 1;">
                    <h3><?php echo htmlspecialchars($item['book_title'] ?? ''); ?></h3>
                    <p>Автор: <?php echo htmlspecialchars($item['author'] ?? ''); ?></p>
                    <p>Дата и время: <?php echo date('d.m.Y H:i', strtotime($item['booking_date'] ?? '')); ?></p>
                    <p>Жанр: <?php echo htmlspecialchars($item['genre'] ?? ''); ?></p>
                </div>
                <form method="POST">
                    <input type="hidden" name="key" value="<?php echo $key; ?>">
                    <button type="submit" name="remove" class="btn" style="background:#e74c3c; font-size:10px; padding:8px 16px;">Отменить</button>
                </form>
            </div>
            <?php endforeach; ?>

            <div class="cart-total">
                <p>Всего книг: <strong><?php echo $bookingCount; ?></strong></p>
                
                <?php if ($isLoggedIn): ?>
                    <form method="POST">
                        <div class="form-group">
                            <label>ФИО</label>
                            <input type="text" name="name" value="<?php echo $_SESSION['user_name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Телефон</label>
                            <input type="text" name="phone" required>
                        </div>
                        <button type="submit" name="checkout" class="btn" style="width:100%;">Подтвердить бронирование</button>
                    </form>
                <?php else: ?>
                    <p>Для бронирования нужно <a href="login.php">войти</a></p>
                <?php endif; ?>
                
                <form method="POST" style="margin-top:15px;">
                    <button type="submit" name="clear" class="btn btn-outline" style="width:100%;">Очистить</button>
                </form>
            </div>
        <?php endif; ?>
    </main>

    <footer><p>&copy; 2026 Библиотека. Все права защищены.</p></footer>
</body>
</html>