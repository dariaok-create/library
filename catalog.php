<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Book.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $book = new Book($db);
    
    $genre_ids = isset($_GET['genres']) ? $_GET['genres'] : [];
    if (!is_array($genre_ids)) $genre_ids = [$genre_ids];
    $genre_ids = array_map('intval', $genre_ids);
    
    $allBooks = $book->getAll(null, null);
    
    $books = [];
    foreach ($allBooks as $b) {
        $ok = true;
        if (!empty($genre_ids)) {
            if (!in_array($b['genre_id'], $genre_ids)) $ok = false;
        }
        if ($ok) $books[] = $b;
    }
    
    $genres = $book->getGenres();
    
    $activeNames = [];
    if (!empty($genre_ids)) {
        foreach ($genres as $g) {
            if (in_array($g['id'], $genre_ids)) $activeNames[] = $g['name'];
        }
    }
    $titleText = !empty($activeNames) ? implode(' + ', $activeNames) : 'Все книги';
    
} catch (Exception $e) {
    $books = [];
    $genres = [];
    $titleText = 'Все книги';
}

$favCount = isset($_SESSION['favorites']) ? count($_SESSION['favorites']) : 0;
$isLoggedIn = isset($_SESSION['user_id']);
$bookingCount = isset($_SESSION['bookings']) ? count($_SESSION['bookings']) : 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Книги — Библиотека</title>
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
                    <a href="logout.php">Выйти</a>
                <?php else: ?>
                    <a href="login.php">Войти</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <h1>Каталог книг</h1>

        <div class="main-content">
            <aside class="filters-sidebar">
                <div class="filters-header"><h2>Фильтры</h2></div>
                
                <div class="filter-group">
                    <h3>🔍 Поиск</h3>
                    <input type="text" id="searchBox" placeholder="Поиск книги..." autocomplete="off"
                           style="width:100%; padding:12px 14px; border:2px solid #d4c4a8; border-radius:6px; 
                                  font-size:13px; outline:none; font-family:'Montserrat',sans-serif; background:#fff;">
                </div>
                
                <form method="GET" action="catalog.php" id="filterForm">
                    <div class="filter-group">
                        <h3>Жанр</h3>
                        <div class="filter-options">
                            <?php foreach ($genres as $genre): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="genres[]" value="<?php echo $genre['id']; ?>" 
                                       <?php echo in_array($genre['id'], $genre_ids) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span> <?php echo $genre['name']; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="apply-filters">Применить</button>
                    
                    <?php if (!empty($genre_ids)): ?>
                        <a href="catalog.php" style="display:block; text-align:center; margin-top:10px; 
                           color:#6b4d3a; font-size:11px; text-decoration:none;">Сбросить всё</a>
                    <?php endif; ?>
                </form>
            </aside>

            <div class="products-results">
                <div class="results-header">
                    <h2 style="font-family:'Cormorant Garamond',serif; font-size:20px; color:#4a2c1a;">
                        <?php echo $titleText; ?>
                    </h2>
                    <span style="font-size:12px; color:#6b4d3a;">
                        Найдено: <span id="countDisplay"><?php echo count($books); ?></span>
                    </span>
                </div>

                <div class="products-grid" id="productsGrid">
                    <?php foreach ($books as $item): ?>
                    <?php 
                        $dataName = mb_strtolower(strip_tags($item['title']));
                        $dataName = preg_replace('/[^a-zа-яё0-9\s]/u', '', $dataName);
                        $isFav = isset($_SESSION['favorites'][$item['id']]);
                    ?>
                    <div class="product-card" data-name="<?php echo $dataName; ?>" style="position: relative;">
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
                        <h3 class="product-name"><?php echo $item['title']; ?></h3>
                        <p class="category"><?php echo $item['author']; ?></p>
                        <p class="price"><?php echo $item['genre_name']; ?></p>
                        <a href="book.php?id=<?php echo $item['id']; ?>" class="btn">Подробнее</a>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div id="noResults" style="display:none; text-align:center; padding:50px;">
                    <p style="font-size:18px; color:#6b4d3a;">Ничего не найдено</p>
                    <p style="color:#6b4d3a; margin-top:8px; font-size:13px;">Попробуйте изменить запрос</p>
                </div>
            </div>
        </div>
    </main>

    <footer><p>&copy; 2026 Библиотека. Все права защищены.</p></footer>

    <script>
    (function() {
        var searchBox = document.getElementById('searchBox');
        var countDisplay = document.getElementById('countDisplay');
        var noResults = document.getElementById('noResults');
        if (!searchBox) return;
        
        searchBox.addEventListener('input', function() {
            var query = this.value.toLowerCase().trim();
            var cards = document.querySelectorAll('.product-card');
            var found = 0;
            for (var i = 0; i < cards.length; i++) {
                var name = cards[i].getAttribute('data-name') || '';
                if (query === '' || name.indexOf(query) !== -1) {
                    cards[i].style.display = '';
                    found++;
                } else {
                    cards[i].style.display = 'none';
                }
            }
            if (countDisplay) countDisplay.textContent = found;
            if (noResults) noResults.style.display = (found === 0 && query !== '') ? 'block' : 'none';
        });
    })();
    </script>
</body>
</html>