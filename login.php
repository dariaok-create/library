<?php
session_start();
require_once 'config/database.php';
require_once 'classes/User.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            $user = new User($db);
            $userData = $user->login($email, $password);
            if ($userData) {
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_name'] = $userData['name'];
                $_SESSION['is_admin'] = $userData['is_admin'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Неверный email или пароль';
            }
        } catch (Exception $e) {
            $error = 'Ошибка входа';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Библиотека</title>
    <link rel="stylesheet" href="style.css">
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
                <a href="register.php">Регистрация</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="auth-form">
            <h1>Вход</h1>
            
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Введите email" required>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" placeholder="Введите пароль" required>
                </div>
                <div class="form-group">
                    <button type="submit">Войти</button>
                </div>
            </form>
            <p style="text-align:center; font-size:12px; color:#6b4d3a; margin-top:15px;">
                Нет аккаунта? <a href="register.php" style="color:#6b3a2a;">Регистрация</a>
            </p>
        </div>
    </main>

    <footer><p>&copy; 2026 Библиотека. Все права защищены.</p></footer>
</body>
</html>