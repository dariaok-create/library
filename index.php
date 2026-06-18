<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Book.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $book = new Book($db);
    $books = $book->getAll();
} catch (Exception $e) {
    $books = [];
}

$favCount = isset($_SESSION['favorites']) ? count($_SESSION['favorites']) : 0;
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
$bookingCount = isset($_SESSION['bookings']) ? count($_SESSION['bookings']) : 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Библиотека — Главная</title>
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
                <a href="favorites.php">❤️ Избранное (<?php echo $favCount; ?>)</a>
                <a href="about.php">О нас</a>
                <a href="contacts.php">Контакты</a>
                <a href="bookings.php" class="cart-link">Брони (<?php echo $bookingCount; ?>)</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php">Профиль</a>
                    <?php if ($isAdmin): ?>
                        <a href="admin.php">⚙️ Библиотекарь</a>
                    <?php endif; ?>
                    <a href="logout.php">Выйти</a>
                <?php else: ?>
                    <a href="login.php">Войти</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-text">
                <h1>Мир книг для вас</h1>
                <p>Бронируйте книги онлайн. Быстро, удобно, бесплатно.</p>
                <a href="catalog.php" class="btn">Выбрать книгу</a>
            </div>
            <div class="hero-image">
                <img src="images/hero-b.jpg" alt="Библиотека">
            </div>
        </section>

        <section class="shop-now">
            <div class="shop-now-image">
                <img src="images/shop-now-b.jpg" alt="Книжный фонд">
            </div>
            <div class="shop-now-text">
                <h2>Наш фонд</h2>
                <p style="color:#6b4d3a; margin-bottom:25px;">Тысячи книг разных жанров. От классики до современной литературы.</p>
                <a href="catalog.php" class="btn btn-outline">Все книги</a>
            </div>
        </section>

        <div class="banners">
            <div class="banner">
                <img src="images/baner1.jpg" alt="Новинки">
                <div class="banner-overlay"><span>Новые поступления</span></div>
            </div>
            <div class="banner">
                <img src="images/baner2.jpg" alt="Акции">
                <div class="banner-overlay"><span>Книжные выставки</span></div>
            </div>
        </div>

        <div class="advantages">
            <div class="advantage-item"><h4>Богатый фонд</h4><p>Более 10 000 книг</p></div>
            <div class="advantage-item"><h4>Удобный поиск</h4><p>По жанрам и авторам</p></div>
            <div class="advantage-item"><h4>Бронирование онлайн</h4><p>Без очередей</p></div>
        </div>

        <section class="featured">
            <h2>Популярные книги</h2>
            <div class="products-grid">
                <?php foreach (array_slice($books, 0, 4) as $item): ?>
                <?php $isFav = isset($_SESSION['favorites'][$item['id']]); ?>
                <div class="product-card" style="position: relative;">
                    <form method="POST" action="favorites.php" style="position:absolute; top:12px; right:12px; z-index:5; margin:0;">
                        <input type="hidden" name="book_id" value="<?php echo $item['id']; ?>">
                        <input type="hidden" name="book_title" value="<?php echo htmlspecialchars($item['title']); ?>">
                        <input type="hidden" name="book_cover" value="<?php echo $item['cover']; ?>">
                        <input type="hidden" name="book_author" value="<?php echo $item['author']; ?>">
                        <button type="submit" name="add_favorite" class="favorite-btn <?php echo $isFav ? 'active' : ''; ?>">
                            <?php echo $isFav ? '❤️' : '🤍'; ?>
                        </button>
                    </form>
                    
                    <img src="<?php echo $item['cover']; ?>" alt="<?php echo $item['title']; ?>">
                    <h3><?php echo $item['title']; ?></h3>
                    <p class="category"><?php echo $item['author']; ?></p>
                    <p class="price"><?php echo $item['genre_name']; ?></p>
                    <a href="book.php?id=<?php echo $item['id']; ?>" class="btn">Подробнее</a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="newsletter">
            <h3 style="font-family:'Cormorant Garamond',serif; font-size:22px; color:#4a2c1a;">Остались вопросы?</h3>
            <p style="color:#6b4d3a; font-size:13px; margin-bottom:18px;">Оставьте заявку, и мы ответим вам</p>
            <form><input type="tel" placeholder="Ваш телефон"><button type="submit">→</button></form>
        </div>
    </main>

    <footer><p>&copy; 2026 Библиотека. Все права защищены.</p></footer>
</body>
</html>