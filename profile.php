<?php
session_start();
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/Booking.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $userObj = new User($db);
    $bookingObj = new Booking($db);
    $user = $userObj->getById($_SESSION['user_id']);
    $bookings = $bookingObj->getByUser($_SESSION['user_id']);
} catch (Exception $e) {
    $error = "Ошибка загрузки данных";
}

if (isset($_POST['update_profile'])) {
    try {
        $userObj->update($_SESSION['user_id'], [
            'name' => $_POST['name'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address']
        ]);
        $success = "Профиль обновлён!";
        $user = $userObj->getById($_SESSION['user_id']);
    } catch (Exception $e) {
        $error = "Ошибка обновления";
    }
}

$bookingCount = isset($_SESSION['bookings']) ? count($_SESSION['bookings']) : 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет — Библиотека</title>
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
                <a href="profile.php">Профиль</a>
                <a href="logout.php">Выйти</a>
            </div>
        </nav>
    </header>

    <main>
        <h1>Личный кабинет</h1>
        
        <?php if (isset($success)): ?>
            <p class="success-msg"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="auth-form" style="margin: 0;">
                <h1>Мои данные</h1>
                <form method="POST">
                    <div class="form-group">
                        <label>ФИО</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Телефон</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Адрес</label>
                        <textarea name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="btn" style="width:100%;">Сохранить</button>
                </form>
            </div>

            <div class="auth-form" style="margin: 0;">
                <h1>История бронирований</h1>
                <?php if (empty($bookings)): ?>
                    <p style="color: #6b4d3a; font-size: 13px;">У вас пока нет бронирований</p>
                <?php else: ?>
                    <?php foreach ($bookings as $book): ?>
                        <div style="border: 1px solid #d4c4a8; padding: 15px; margin-bottom: 12px; border-radius: 4px;">
                            <p><strong>Книга: <?php echo htmlspecialchars($book['book_title']); ?></strong></p>
                            <p>Жанр: <?php echo htmlspecialchars($book['genre_name']); ?></p>
                            <p>Дата: <?php echo date('d.m.Y H:i', strtotime($book['booking_date'])); ?></p>
                            <p>Статус: <?php echo $book['status']; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Библиотека. Все права защищены.</p>
    </footer>
</body>
</html>