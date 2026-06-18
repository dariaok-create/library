<?php
session_start();
require_once 'config/database.php';
require_once 'classes/User.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Заполните обязательные поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email';
    } elseif ($password !== $password2) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            $user = new User($db);
            $result = $user->register([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone
            ]);
            if ($result['success']) {
                $success = 'Регистрация успешна! Теперь войдите.';
            } else {
                $error = $result['message'];
            }
        } catch (Exception $e) {
            $error = 'Ошибка регистрации';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация — Библиотека</title>
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
                <a href="login.php">Войти</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="auth-form">
            <h1>Регистрация</h1>
            
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="success-msg">✅ <?php echo $success; ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Имя *</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="Ваше имя" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="+7 (999) 123-45-67">
                </div>
                <div class="form-group">
                    <label>Пароль * (мин. 6 символов)</label>
                    <input type="password" name="password" placeholder="Пароль" required>
                </div>
                <div class="form-group">
                    <label>Повторите пароль *</label>
                    <input type="password" name="password2" placeholder="Повторите пароль" required>
                </div>
                <div class="form-group">
                    <button type="submit">Зарегистрироваться</button>
                </div>
            </form>
            <p style="text-align:center; font-size:12px; color:#6b4d3a; margin-top:15px;">
                Есть аккаунт? <a href="login.php" style="color:#6b3a2a;">Войти</a>
            </p>
        </div>
    </main>

    <footer><p>&copy; 2026 Библиотека. Все права защищены.</p></footer>
</body>
</html>